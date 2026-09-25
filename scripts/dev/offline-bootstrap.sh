#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────────────────
# Quick GIGS — dev bootstrap for machines with NO php and NO composer.
#
# Normal setup is just:  composer install && php artisan migrate --seed && php artisan serve
# Use this script only in a sandbox/CI box where PHP and Packagist are unavailable:
#   * runs PHP 8.5 through WebAssembly (@php-wasm/cli from npm)
#   * installs every locked dependency straight from GitHub (composer.lock dist urls)
#   * generates a Composer-compatible autoloader
#   * creates .env, an SQLite database, and seeds demo data
#
# Usage:  bash scripts/dev/offline-bootstrap.sh [--serve]
# Then:   ~/tools/phpw artisan <command>
# ──────────────────────────────────────────────────────────────────────────────
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
TOOLS="${TOOLS_DIR:-$HOME/tools}"
mkdir -p "$TOOLS"

echo "▸ 1/6  PHP runtime (WebAssembly)"
if [ ! -x "$TOOLS/phpw" ] || [ ! -d "$TOOLS/node_modules/@php-wasm/cli" ]; then
  (cd "$TOOLS" && npm init -y >/dev/null 2>&1 || true; npm install @php-wasm/cli >/dev/null 2>&1)
  printf '#!/bin/bash\nexec node %s/node_modules/@php-wasm/cli/php-wasm.js "$@"\n' "$TOOLS" > "$TOOLS/phpw"
  chmod +x "$TOOLS/phpw"
fi
echo "   php $("$TOOLS/phpw" -r 'echo PHP_VERSION;')"

echo "▸ 2/6  Composer runtime classes"
if [ ! -f "$TOOLS/composer-src/ClassLoader.php" ]; then
  mkdir -p "$TOOLS/composer-src"
  curl -sL -H "Authorization: Bearer $(gh auth token 2>/dev/null || echo '')" \
    "https://api.github.com/repos/composer/composer/zipball/2.8.12" -o "$TOOLS/composer.zip"
  (cd "$TOOLS/composer-src" && python3 -c "
import zipfile, os
z = zipfile.ZipFile('../composer.zip')
for n in z.namelist():
    if n.endswith(('src/Composer/Autoload/ClassLoader.php','src/Composer/InstalledVersions.php')):
        open(os.path.basename(n),'wb').write(z.read(n))
")
fi

echo "▸ 3/6  vendor/ from GitHub"
python3 - "$ROOT" <<'PY'
import json, os, io, sys, zipfile, urllib.request, shutil, subprocess, concurrent.futures
ROOT = sys.argv[1]; VENDOR = os.path.join(ROOT, 'vendor')
lock = json.load(open(os.path.join(ROOT, 'composer.lock')))
token = subprocess.run(['gh','auth','token'], capture_output=True, text=True).stdout.strip()
pkgs = lock['packages']; os.makedirs(VENDOR, exist_ok=True)

def fetch(pkg):
    name = pkg['name']; target = os.path.join(VENDOR, *name.split('/'))
    if os.path.isdir(target) and os.listdir(target): return name, 'cached'
    headers = {'Accept': 'application/vnd.github+json', 'User-Agent': 'qg-bootstrap'}
    if token: headers['Authorization'] = f'Bearer {token}'
    req = urllib.request.Request(pkg['dist']['url'], headers=headers)
    for attempt in range(4):
        try:
            data = urllib.request.urlopen(req, timeout=120).read(); break
        except Exception as e:
            if attempt == 3: return name, f'FAIL {e}'
    z = zipfile.ZipFile(io.BytesIO(data)); names = z.namelist(); prefix = names[0].split('/')[0] + '/'
    tmp = target + '.tmp'; shutil.rmtree(tmp, ignore_errors=True); os.makedirs(tmp, exist_ok=True)
    for n in names:
        if not n.startswith(prefix) or n.endswith('/'): continue
        dest = os.path.join(tmp, n[len(prefix):]); os.makedirs(os.path.dirname(dest), exist_ok=True)
        open(dest, 'wb').write(z.read(n))
    shutil.rmtree(target, ignore_errors=True); os.makedirs(os.path.dirname(target), exist_ok=True); os.rename(tmp, target)
    return name, 'ok'

failed = []
with concurrent.futures.ThreadPoolExecutor(max_workers=8) as ex:
    for name, status in ex.map(fetch, pkgs):
        if status not in ('ok', 'cached'): failed.append((name, status))
print('   packages:', len(pkgs), '| failed:', failed or 'none')
PY

echo "▸ 4/6  autoloader"
python3 - "$ROOT" "$TOOLS" <<'PY'
import json, os, re, sys, hashlib, shutil
ROOT, TOOLS = sys.argv[1], sys.argv[2]
VENDOR = os.path.join(ROOT, 'vendor'); CDIR = os.path.join(VENDOR, 'composer')
os.makedirs(CDIR, exist_ok=True)
lock = json.load(open(os.path.join(ROOT, 'composer.lock'))); root = json.load(open(os.path.join(ROOT, 'composer.json')))
byname = {p['name']: p for p in lock['packages']}

order, seen = [], set()
def visit(n, stack=()):
    if n in seen or n not in byname or n in stack: return
    for dep in byname[n].get('require', {}):
        if dep in byname: visit(dep, stack + (n,))
    seen.add(n); order.append(n)
for n in sorted(byname): visit(n)

psr4, psr0, classmap, files = {}, {}, {}, []
CLASS_RE = re.compile(rb'^\s*(?:final\s+|abstract\s+|readonly\s+)*(class|interface|trait|enum)\s+([A-Za-z_\x80-\xff][A-Za-z0-9_\x80-\xff]*)', re.M)
NS_RE = re.compile(rb'^\s*namespace\s+([^;{\s]+)\s*[;{]', re.M)

def scan(path):
    out = {}
    cand = [path] if os.path.isfile(path) else [
        os.path.join(dp, f) for dp, dn, fn in os.walk(path) for f in fn
        if f.endswith('.php') and not any(x in dp for x in ('/tests', '/Tests', '/.git'))
    ]
    for f in cand:
        try: src = open(f, 'rb').read()
        except Exception: continue
        ns = NS_RE.search(src); prefix = (ns.group(1).decode() + '\\') if ns else ''
        for m in CLASS_RE.finditer(src): out[prefix + m.group(2).decode()] = f
    return out

def add(al, basedir, pkgname=''):
    for prefix, paths in (al.get('psr-4') or {}).items():
        paths = [paths] if isinstance(paths, str) else paths
        psr4.setdefault(prefix, []).extend(os.path.normpath(os.path.join(basedir, p)) for p in paths)
    for prefix, paths in (al.get('psr-0') or {}).items():
        paths = [paths] if isinstance(paths, str) else paths
        psr0.setdefault(prefix, []).extend(os.path.normpath(os.path.join(basedir, p)) for p in paths)
    for p in (al.get('classmap') or []):
        full = os.path.normpath(os.path.join(basedir, p))
        if os.path.exists(full): classmap.update(scan(full))
    for p in (al.get('files') or []):
        full = os.path.normpath(os.path.join(basedir, p))
        if os.path.exists(full): files.append((hashlib.md5((pkgname + ':' + p).encode()).hexdigest(), full))

for n in order: add(byname[n].get('autoload') or {}, os.path.join(VENDOR, *n.split('/')), n)
add(root.get('autoload') or {}, ROOT, '__root__')

def php_path(p):
    rel = os.path.relpath(p, VENDOR)
    return "$baseDir . '/" + os.path.relpath(p, ROOT) + "'" if rel.startswith('..') else "$vendorDir . '/" + rel + "'"

hdr = "<?php\n$vendorDir = dirname(__DIR__);\n$baseDir = dirname($vendorDir);\nreturn array(\n"
for fname, data in [('autoload_psr4.php', psr4), ('autoload_namespaces.php', psr0)]:
    with open(os.path.join(CDIR, fname), 'w') as f:
        f.write(hdr)
        for k in sorted(data, key=lambda x: -len(x)):
            f.write("    %s => array(%s),\n" % (json.dumps(k), ', '.join(php_path(p) for p in data[k])))
        f.write(");\n")
with open(os.path.join(CDIR, 'autoload_classmap.php'), 'w') as f:
    f.write(hdr)
    for k in sorted(classmap): f.write("    %s => %s,\n" % (json.dumps(k), php_path(classmap[k])))
    f.write(");\n")
with open(os.path.join(CDIR, 'autoload_files.php'), 'w') as f:
    f.write(hdr)
    for k, p in files: f.write("    %s => %s,\n" % (json.dumps(k), php_path(p)))
    f.write(");\n")

shutil.copy(os.path.join(TOOLS, 'composer-src/ClassLoader.php'), os.path.join(CDIR, 'ClassLoader.php'))
shutil.copy(os.path.join(TOOLS, 'composer-src/InstalledVersions.php'), os.path.join(CDIR, 'InstalledVersions.php'))

open(os.path.join(CDIR, 'autoload_real.php'), 'w').write('''<?php
class ComposerAutoloaderInitQuickGigs {
    private static $loader;
    public static function getLoader() {
        if (null !== self::$loader) return self::$loader;
        require __DIR__ . '/ClassLoader.php';
        require __DIR__ . '/InstalledVersions.php';
        self::$loader = $loader = new \\Composer\\Autoload\\ClassLoader(dirname(__DIR__));
        foreach (require __DIR__ . '/autoload_namespaces.php' as $ns => $path) { $loader->set($ns, $path); }
        foreach (require __DIR__ . '/autoload_psr4.php' as $ns => $path) { $loader->setPsr4($ns, $path); }
        $loader->addClassMap(require __DIR__ . '/autoload_classmap.php');
        $loader->register(true);
        foreach (require __DIR__ . '/autoload_files.php' as $id => $file) {
            if (empty($GLOBALS['__composer_autoload_files'][$id])) { $GLOBALS['__composer_autoload_files'][$id] = true; require $file; }
        }
        return $loader;
    }
}
''')
open(os.path.join(VENDOR, 'autoload.php'), 'w').write("<?php\n\nrequire_once __DIR__ . '/composer/autoload_real.php';\n\nreturn ComposerAutoloaderInitQuickGigs::getLoader();\n")

installed, versions = [], {}
for n in order:
    p = byname[n]; e = dict(p); e['install-path'] = '../' + '/'.join(n.split('/')); installed.append(e)
    versions[n] = {'pretty_version': p['version'], 'version': p.get('version_normalized', p['version']),
                   'reference': p.get('source', {}).get('reference'), 'type': p.get('type', 'library'),
                   'install_path': os.path.join(VENDOR, *n.split('/')), 'aliases': [], 'dev_requirement': False}
json.dump({'packages': installed, 'dev': False, 'dev-package-names': []}, open(os.path.join(CDIR, 'installed.json'), 'w'), indent=2)

def parr(d, indent=8):
    out = 'array(\n'
    for k, v in d.items():
        pad = ' ' * indent
        if isinstance(v, dict): out += pad + json.dumps(k) + ' => ' + parr(v, indent + 4) + ',\n'
        elif isinstance(v, list): out += pad + json.dumps(k) + ' => array(),\n'
        elif v is None: out += pad + json.dumps(k) + ' => NULL,\n'
        elif isinstance(v, bool): out += pad + json.dumps(k) + ' => ' + ('true' if v else 'false') + ',\n'
        else: out += pad + json.dumps(k) + ' => ' + json.dumps(str(v)) + ',\n'
    return out + ' ' * (indent - 4) + ')'

rootinfo = {'name': root.get('name', 'quickgigs/marketplace'), 'pretty_version': 'dev-main', 'version': 'dev-main',
            'reference': None, 'type': 'project', 'install_path': ROOT + '/', 'aliases': [], 'dev': True}
open(os.path.join(CDIR, 'installed.php'), 'w').write(
    "<?php return array(\n    'root' => " + parr(rootinfo) + ",\n    'versions' => " + parr(versions) + ",\n);\n")
print('   psr4:', len(psr4), '| classmap:', len(classmap), '| files:', len(files))
PY

echo "▸ 5/6  env + database"
cd "$ROOT"
mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache database
if [ ! -f .env ]; then
  cat > .env <<ENV
APP_NAME="Quick GIGS"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080
LOG_CHANNEL=stack
LOG_LEVEL=debug
DB_CONNECTION=sqlite
DB_DATABASE=$ROOT/database/database.sqlite
CACHE_STORE=file
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
MAIL_MAILER=log
ENV
fi
touch database/database.sqlite
PHPW="$TOOLS/phpw -d error_reporting=E_ALL&~E_DEPRECATED"
"$TOOLS/phpw" -d error_reporting="E_ALL & ~E_DEPRECATED" artisan key:generate --force >/dev/null
"$TOOLS/phpw" -d error_reporting="E_ALL & ~E_DEPRECATED" artisan migrate:fresh --seed --force | tail -2

echo "▸ 6/6  ready"
echo "   run:  $TOOLS/phpw artisan serve   (or --serve to start it now on :8080)"

if [ "${1:-}" = "--serve" ]; then
  exec "$TOOLS/phpw" -d error_reporting="E_ALL & ~E_DEPRECATED" -S 0.0.0.0:8080 -t public scripts/dev/router.php
fi
