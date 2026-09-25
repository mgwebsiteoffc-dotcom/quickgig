// Loads rendered pages in jsdom with Alpine + plugins and reports any component that fails to
// initialise. Catches the class of bug where a rename breaks an x-data factory and the whole
// section silently renders empty.
//
//   npm i --prefix ~/tools/jsdom jsdom alpinejs @alpinejs/collapse @alpinejs/intersect
//   node scripts/dev/alpine-audit.mjs http://localhost:8080/ http://localhost:8080/for-business
import { JSDOM, VirtualConsole } from 'jsdom';
import fs from 'node:fs';

const NM = '/home/user/tools/jsdom/node_modules';
const pages = process.argv.slice(2);
let total = 0;

for (const url of pages) {
  const res = await fetch(url);
  let html = (await res.text())
    .replace(/<script[^>]*src="https:\/\/cdn\.jsdelivr\.net[^"]*"[^>]*><\/script>/g, '')
    .replace(/<script[^>]*src="https:\/\/cdn\.tailwindcss\.com"[^>]*><\/script>/g, '')
    .replace(/<script[^>]*>\s*tailwind\.config[\s\S]*?<\/script>/g, '');

  const errs = [];
  const vc = new VirtualConsole();
  vc.on('jsdomError', e => errs.push(String(e.message || e)));
  vc.on('error', (...a) => errs.push(a.join(' ')));
  vc.on('warn', (...a) => errs.push(a.join(' ')));

  const dom = new JSDOM(html, { runScripts: 'dangerously', virtualConsole: vc, pretendToBeVisual: true, url });
  const { window } = dom;
  window.IntersectionObserver = class { constructor(cb){this.cb=cb;} observe(el){ this.cb([{isIntersecting:true, target:el}], this);} unobserve(){} disconnect(){} };
  window.matchMedia = () => ({ matches:false, addListener(){}, removeListener(){} });
  window.fetch = async () => ({ ok:true, json: async () => ({}) });

  for (const f of ['@alpinejs/collapse', '@alpinejs/intersect', 'alpinejs']) {
    const s = window.document.createElement('script');
    s.textContent = fs.readFileSync(`${NM}/${f}/dist/cdn.min.js`, 'utf8');
    window.document.head.appendChild(s);
  }
  await new Promise(r => setTimeout(r, 700));

  const alpine = errs.filter(e => /Alpine/i.test(e));
  total += alpine.length;
  const path = new URL(url).pathname;
  console.log(`${path.padEnd(18)} components=${window.document.querySelectorAll('[x-data]').length}  alpine-errors=${alpine.length}`);
  alpine.slice(0, 3).forEach(e => console.log('    ' + e.split('\n')[0].slice(0, 120)));
  window.close();
}
console.log(total === 0 ? '\nAll Alpine components initialise cleanly.' : `\n${total} Alpine errors`);
