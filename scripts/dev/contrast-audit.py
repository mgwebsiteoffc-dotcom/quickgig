#!/usr/bin/env python3
"""Flag text/surface colour collisions in Blade views (same element and dark-parent cases)."""
import glob, re

DARK   = ('bg-ink', 'btn-grad', 'bg-deep', 'bg-black')
LIGHTS = ('bg-mint-wash', 'bg-tint', 'bg-white', 'bg-mint', 'btn-mint', 'badge-off', 'glass')
BAD_ON_DARK  = ('text-ink', 'text-deep', 'text-body', 'text-faint', 'text-mut', 'text-mint-deep')
BAD_ON_LIGHT = ('text-white',)

def classes_in(attr):
    """every class set the attribute could resolve to, including blade ternary branches"""
    sets = [set(re.split(r'\s+', re.sub(r'\{\{.*?\}\}', ' ', attr)))]
    for lit in re.findall(r"'([^']*)'", attr) + re.findall(r'"([^"]*)"', attr):
        sets.append(set(re.split(r'\s+', lit)))
    return sets

hits = []
for f in sorted(glob.glob('resources/views/**/*.blade.php', recursive=True)):
    if '/admin/' in f: continue
    lines = open(f).read().split('\n')
    for i, line in enumerate(lines):
        for attr in re.findall(r'class="([^"]*)"', line):
            for toks in classes_in(attr):
                d = [s for s in DARK if s in toks]
                l = [s for s in LIGHTS if s in toks]
                if d and (bad := [t for t in BAD_ON_DARK if t in toks]):
                    hits.append((f, i+1, 'SAME-EL', d[0], bad, line.strip()[:100]))
                if l and (bad := [t for t in BAD_ON_LIGHT if t in toks]):
                    hits.append((f, i+1, 'SAME-EL', l[0], bad, line.strip()[:100]))

        # dark container -> dark descendant text within the block
        if any(s in line for s in ('bg-ink', 'btn-grad')) and 'band-dark' not in line:
            indent = len(line) - len(line.lstrip())
            for j in range(i+1, min(i+10, len(lines))):
                nxt = lines[j]
                if nxt.strip() and (len(nxt) - len(nxt.lstrip())) <= indent: break
                for t in ('text-body', 'text-faint', 'text-mut'):
                    if re.search(rf'class="[^"]*\b{t}\b', nxt):
                        hits.append((f, j+1, 'CHILD-OF-DARK', 'bg-ink', [t], nxt.strip()[:100]))

seen = set(); out = []
for h in hits:
    k = (h[0], h[1], h[4][0])
    if k not in seen: seen.add(k); out.append(h)

for f, ln, kind, surface, bad, snippet in out:
    print(f"{f}:{ln}  [{kind}] {surface} + {','.join(bad)}\n    {snippet}")
print(f"\n{len(out)} collisions")
