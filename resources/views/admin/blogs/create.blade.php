@extends('admin.layout')
@section('title','New Blog Post')
@section('breadcrumb','Admin • Blogs • Create')
@section('content')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>.ql-editor{min-height:280px;font-size:14px;line-height:1.7} .ql-toolbar{border-top-left-radius:12px;border-top-right-radius:12px} .ql-container{border-bottom-left-radius:12px;border-bottom-right-radius:12px}</style>

<form method="POST" action="{{ route('admin.blogs.store') }}" enctype="multipart/form-data" class="max-w-[860px] bg-white border border-[#E8E8E6] rounded-2xl p-5 sm:p-6" id="blogForm">
@csrf
  <div class="grid sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2"><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Title * (SEO H1)</label><input name="title" id="title" required placeholder="How to... in 1 Day (Hook → ...)" value="{{ old('title') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px] font-bold outline-none focus:bg-white"></div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Slug (auto)</label><input name="slug" id="slug" placeholder="avante-1-day-reel" value="{{ old('slug') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-mono"></div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Category</label><select name="category_id" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold"><option value="">— General —</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select></div>
    <div class="sm:col-span-2"><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Excerpt (155 chars for AEO) — also meta description fallback</label><textarea name="excerpt" rows="2" maxlength="360" class="mt-1 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] leading-6" placeholder="Short answer for answer engines — 1-2 lines.">{{ old('excerpt') }}</textarea></div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Tags (comma)</label><input name="tags" value="{{ old('tags') }}" placeholder="Reels, Retention, Veo 3" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Reading Minutes</label><input name="reading_minutes" type="number" value="{{ old('reading_minutes',4) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
  </div>

  <div class="mt-4"><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Content * — rich text editor</label>
    <div id="editor" class="mt-1 bg-white border border-[#E8E8E6] rounded-xl overflow-hidden">{!! old('content','<h2>Why this matters</h2><p>Start with the answer — AEO first.</p>') !!}</div>
    <input type="hidden" name="content" id="content">
    <div class="text-[11px] font-medium text-[#7A7A78] mt-1">Images: drag/paste or toolbar image → uploads to <code class="bg-[#F8F8F7] border px-1 py-0.5 rounded">/storage/blogs/content</code> or <code class="bg-[#F8F8F7] px-1 py-0.5 rounded">/uploads/blogs</code> (local fallback).</div>
  </div>

  <div class="mt-4 p-4 bg-[#F8F8F7] border border-[#E8E8E6] rounded-2xl">
    <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">SEO / AEO (optional but recommended)</div>
    <div class="mt-3 grid sm:grid-cols-2 gap-3">
      <div class="sm:col-span-2"><label class="text-[11px] font-semibold text-[#7A7A78]">Meta Title (70 chars)</label><input name="meta_title" id="meta_title" value="{{ old('meta_title') }}" maxlength="70" placeholder="Leave blank = Title | Quick GIGS" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]"><div class="text-[11px] text-[#7A7A78] font-mono"><span id="mtCount">0</span>/70</div></div>
      <div class="sm:col-span-2"><label class="text-[11px] font-semibold text-[#7A7A78]">Meta Description (155 chars, for Google + AEO)</label><textarea name="meta_description" id="meta_desc" rows="2" maxlength="165" class="mt-1 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-white text-[13px]">{{ old('meta_description') }}</textarea><div class="text-[11px] text-[#7A7A78] font-mono"><span id="mdCount">0</span>/165</div></div>
      <div class="sm:col-span-2"><label class="text-[11px] font-semibold text-[#7A7A78]">Canonical URL (leave blank for auto)</label><input name="canonical_url" value="{{ old('canonical_url') }}" placeholder="https://yourdomain.com/blog/your-slug" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]"></div>
      <div><label class="text-[11px] font-semibold text-[#7A7A78]">Cover Image (OG)</label><input type="file" name="cover" accept="image/*" class="mt-1 w-full text-[13px]"></div>
      <div><label class="text-[11px] font-semibold text-[#7A7A78]">Cover Alt (AEO)</label><input name="cover_alt" value="{{ old('cover_alt') }}" placeholder="Describe image for screen readers + SEO" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]"></div>
    </div>
  </div>

  <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
    <div class="text-[11px] font-bold tracking-widest uppercase text-amber-800">Per-Post FAQs for AEO (optional) — adds FAQPage JSON-LD to this post</div>
    <div id="faqList" class="mt-3 space-y-3"></div>
    <button type="button" onclick="addFaq()" class="mt-3 h-9 px-4 rounded-full border border-amber-300 bg-white font-bold text-[12px]">+ Add FAQ</button>
    <input type="hidden" name="faq_json" id="faq_json">
  </div>

  <div class="mt-4 flex gap-3">
    <label class="flex items-center gap-2 text-[13px] font-bold"><input type="checkbox" name="is_published" value="1" checked> Published</label>
    <label class="flex items-center gap-2 text-[13px] font-bold"><input type="checkbox" name="is_featured" value="1"> Featured (on blog + landing)</label>
  </div>

  @if($errors->any())<div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-3 py-2 text-[13px] font-semibold">{{ $errors->first() }}</div>@endif

  <button type="submit" class="mt-6 w-full h-11 rounded-full bg-[#0F0F0F] text-white font-extrabold text-[14px]">Create Post → live with JSON-LD + Sitemap</button>
</form>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
const quill = new Quill('#editor', { theme:'snow', modules:{ toolbar:{ container:[[{header:[2,3,false]}],['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],['link','image','blockquote','code-block'],[{color:[]},{background:[]}],['clean']], handlers:{ image: imageHandler } } } });
function imageHandler(){
  const input=document.createElement('input'); input.setAttribute('type','file'); input.setAttribute('accept','image/*'); input.click();
  input.onchange=()=>{ const file=input.files[0]; if(!file) return; const fd=new FormData(); fd.append('image', file); fd.append('_token','{{ csrf_token() }}');
    fetch('{{ route('admin.blogs.upload') }}',{method:'POST', body:fd}).then(r=>r.json()).then(d=>{ if(d.url){ const range=quill.getSelection(true); quill.insertEmbed(range.index,'image', d.url); } }).catch(()=>alert('Upload failed — try smaller image (<3MB). upload path: public/uploads/blogs'));
  };
}
document.getElementById('title').addEventListener('input', e=>{
  const slug=document.getElementById('slug'); if(!slug.dataset.touched) slug.value=e.target.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'').slice(0,80);
});
document.getElementById('slug').addEventListener('input', e=> e.target.dataset.touched='1');
function updateCounts(){ document.getElementById('mtCount').textContent=document.getElementById('meta_title').value.length; document.getElementById('mdCount').textContent=document.getElementById('meta_desc').value.length; }
document.getElementById('meta_title').addEventListener('input', updateCounts); document.getElementById('meta_desc').addEventListener('input', updateCounts); updateCounts();
function addFaq(q='',a=''){ const list=document.getElementById('faqList'); const div=document.createElement('div'); div.className='bg-white border border-amber-200 rounded-xl p-3 flex gap-2'; div.innerHTML=`<div class="flex-1"><input placeholder="Question" value="${q.replace(/"/g,'&quot;')}" class="faq-q w-full h-9 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold"><input placeholder="Answer (concise, AEO-ready)" value="${a.replace(/"/g,'&quot;')}" class="faq-a mt-2 w-full h-9 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div><button type="button" onclick="this.parentElement.remove(); syncFaq()" class="h-8 w-8 rounded-full border border-red-200 bg-red-50 text-red-600 grid place-items-center shrink-0">✕</button>`; list.appendChild(div); div.querySelectorAll('input').forEach(i=>i.addEventListener('input', syncFaq)); syncFaq();
}
function syncFaq(){ const items=[...document.querySelectorAll('#faqList > div')].map(d=>({q:d.querySelector('.faq-q').value.trim(), a:d.querySelector('.faq-a').value.trim()})).filter(x=>x.q && x.a); document.getElementById('faq_json').value= items.length ? JSON.stringify(items.map(x=>({q:x.q,a:x.a}))) : ''; }
document.getElementById('blogForm').addEventListener('submit', ()=>{ document.getElementById('content').value=quill.root.innerHTML; syncFaq(); });
</script>
@endsection
