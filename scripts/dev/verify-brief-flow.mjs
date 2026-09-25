// End-to-end check of the brief flow in jsdom: submit the builder form, confirm the loading state
// scrolls into view, the brief paints, the confirmation banner and toast fire, and no Alpine
// component errors along the way.
//
//   node scripts/dev/verify-brief-flow.mjs        (expects the dev server on :8080)
import { JSDOM, VirtualConsole } from 'jsdom';
import fs from 'node:fs';
const NM = '/home/user/tools/jsdom/node_modules';

const res = await fetch('http://localhost:8080/brief-builder');
const cookie = (res.headers.getSetCookie?.() || []).map(c => c.split(';')[0]).join('; ');
let html = (await res.text())
  .replace(/<script[^>]*src="https:\/\/cdn\.jsdelivr\.net[^"]*"[^>]*><\/script>/g, '')
  .replace(/<script[^>]*src="https:\/\/cdn\.tailwindcss\.com"[^>]*><\/script>/g, '')
  .replace(/<script[^>]*>\s*tailwind\.config[\s\S]*?<\/script>/g, '');

const errs = [];
const vc = new VirtualConsole();
vc.on('jsdomError', e => errs.push(String(e.message || e)));
vc.on('warn', (...a) => errs.push(a.join(' ')));
const dom = new JSDOM(html, { runScripts: 'dangerously', virtualConsole: vc, pretendToBeVisual: true, url: 'http://localhost:8080/brief-builder' });
const { window } = dom;
window.IntersectionObserver = class { constructor(cb){this.cb=cb;} observe(el){this.cb([{isIntersecting:true,target:el}],this);} unobserve(){} disconnect(){} };
window.matchMedia = () => ({ matches:false, addListener(){}, removeListener(){} });

const scrolls = [];
window.scrollTo = (o) => scrolls.push(Math.round(o?.top ?? 0));
window.fetch = async (url, opts = {}) => {
  const headers = { ...(opts.headers || {}), cookie };
  const body = opts.body instanceof window.FormData ? new URLSearchParams([...opts.body]) : opts.body;
  const r = await fetch(url, { ...opts, headers, body });
  return { ok: r.ok, status: r.status, json: () => r.json() };
};
let toast = null;
window.addEventListener('toast', e => toast = e.detail);

for (const f of ['@alpinejs/collapse','@alpinejs/intersect','alpinejs']) {
  const s = window.document.createElement('script');
  s.textContent = fs.readFileSync(`${NM}/${f}/dist/cdn.min.js`, 'utf8');
  window.document.head.appendChild(s);
}
await new Promise(r => setTimeout(r, 700));

const doc = window.document;
doc.querySelector('#idea').value = 'A launch reel for our new protein bar, founder on camera';
const form = doc.querySelector('#brief-form');
form.dispatchEvent(new window.Event('submit', { bubbles: true, cancelable: true }));

await new Promise(r => setTimeout(r, 2500));

const text = doc.body.textContent;
console.log('scrolls fired      :', scrolls.length, scrolls.length ? '(loading → result)' : '');
console.log('banner text        :', (text.match(/Brief ready in [\d.]+s[^—]*—[^.]*\./) || ['none'])[0].trim().slice(0, 95));
console.log('brief rendered     :', /Hook options/.test(text) && /Beat sheet/.test(text));
console.log('matched freelancers:', /matched to this brief/i.test(text));
console.log('toast              :', toast);
console.log('alpine errors      :', errs.filter(e => /Alpine/i.test(e)).length);
errs.slice(0,4).forEach(e => console.log('   ERR ' + e.split('\n')[0].slice(0,160)));
