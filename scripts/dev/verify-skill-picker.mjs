// Drives the admin-managed skill picker in jsdom: options render, search filters, clicking selects,
// hidden skills[] inputs track the selection, and removing a chip updates them.
//
//   node scripts/dev/verify-skill-picker.mjs      (expects the dev server on :8080)
import { JSDOM, VirtualConsole } from 'jsdom';
import fs from 'node:fs';
const NM = '/home/user/tools/jsdom/node_modules';

const res = await fetch('http://localhost:8080/register?type=creator');
let html = (await res.text())
  .replace(/<script[^>]*src="https:\/\/cdn\.jsdelivr\.net[^"]*"[^>]*><\/script>/g, '')
  .replace(/<script[^>]*src="https:\/\/cdn\.tailwindcss\.com"[^>]*><\/script>/g, '')
  .replace(/<script[^>]*>\s*tailwind\.config[\s\S]*?<\/script>/g, '');

const errs = [];
const vc = new VirtualConsole();
vc.on('jsdomError', e => errs.push(String(e.message || e)));
vc.on('warn', (...a) => errs.push(a.join(' ')));
const dom = new JSDOM(html, { runScripts: 'dangerously', virtualConsole: vc, pretendToBeVisual: true, url: 'http://localhost:8080/register' });
const { window } = dom;
window.IntersectionObserver = class { constructor(cb){this.cb=cb;} observe(el){this.cb([{isIntersecting:true,target:el}],this);} unobserve(){} disconnect(){} };
window.matchMedia = () => ({ matches:false, addListener(){}, removeListener(){} });
for (const f of ['@alpinejs/collapse','@alpinejs/intersect','alpinejs']) {
  const s = window.document.createElement('script');
  s.textContent = fs.readFileSync(`${NM}/${f}/dist/cdn.min.js`, 'utf8');
  window.document.head.appendChild(s);
}
await new Promise(r => setTimeout(r, 700));

const doc = window.document;
const picker = doc.querySelector('[x-data^="skillPicker"]');
const search = picker.querySelector('input[x-ref="search"]');
const options = () => [...picker.querySelectorAll('button[type="button"]')].map(b => b.textContent.trim()).filter(Boolean);
const chips   = () => [...picker.querySelectorAll('span.inline-flex span')].map(s => s.textContent.trim()).filter(Boolean);
const hidden  = () => [...picker.querySelectorAll('input[type="hidden"][name="skills[]"]')].map(i => i.value);

console.log('options rendered   :', options().length);
search.value = 'caption';
search.dispatchEvent(new window.Event('input', { bubbles: true }));
await new Promise(r => setTimeout(r, 200));
console.log('search "caption"   :', options().slice(0, 4).join(' | '));

const pick = [...picker.querySelectorAll('button[type="button"]')].find(b => b.textContent.includes('Captions'));
pick.dispatchEvent(new window.MouseEvent('click', { bubbles: true }));
await new Promise(r => setTimeout(r, 200));
console.log('after click chips  :', chips().join(', '));
console.log('posted values      :', hidden().join(', '));

search.value = '';
search.dispatchEvent(new window.Event('input', { bubbles: true }));
await new Promise(r => setTimeout(r, 150));
const second = [...picker.querySelectorAll('button[type="button"]')].find(b => b.textContent.trim() === 'Figma');
second?.dispatchEvent(new window.MouseEvent('click', { bubbles: true }));
await new Promise(r => setTimeout(r, 150));
console.log('two selected       :', hidden().join(', '));

const x = picker.querySelector('button[aria-label="Remove"]');
x.dispatchEvent(new window.MouseEvent('click', { bubbles: true }));
await new Promise(r => setTimeout(r, 150));
console.log('after remove       :', hidden().join(', ') || '(empty)');
console.log('alpine errors      :', errs.filter(e => /Alpine/i.test(e)).length);
