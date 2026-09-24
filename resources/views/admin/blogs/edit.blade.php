@extends('admin.layout')
@section('title','Edit: '.$blog->title)
@section('breadcrumb','Admin • Blogs • Edit')
@section('content')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>.ql-editor{min-height:280px;font-size:14px;line-height:1.7} .ql-toolbar{border-top-left-radius:12px;border-top-right-radius:12px} .ql-container{border-bottom-left-radius:12px;border-bottom-right-radius:12px}</style>

<form method="POST" action="{{ route('admin.blogs.update',$blog) }}" enctype="multipart/form-data" class="max-w-[860px] bg-white border border-[#E8E8E6] rounded-2xl p-5 sm:p-6" id="blogForm">
@csrf @method('PUT')
  <div class="grid sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2"><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Title *</label><input name="title" id="title" required value="{{ old('title',$blog->title) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px] font-bold"></div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Slug *</label><input name="slug" value="{{ old('slug',$blog->slug) }}" required class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-mono"></div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Category</label><select name="category_id" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold"><option value="">— General —</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ (old('category_id',$blog->category_id)==$c->id)?'selected':'' }}>{{ $c->name }}</option>@endforeach</select></div>
    <div class="sm:col-span-2"><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Excerpt (155 chars)</label><textarea name="excerpt" rows="2" maxlength="360" class="mt-1 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] leading-6">{{ old('excerpt',$blog->excerpt) }}</textarea></div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Tags (comma)</label><input name="tags" value="{{ old('tags', is_array($blog->tags) ? implode(', ', $blog->tags) : $blog->tags) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Reading Minutes</label><input name="reading_minutes" type="number" value="{{ old('reading_minutes',$blog->reading_minutes) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
  </div>

  <div class="mt-4"><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Content *</label>
    <div id="editor" class="mt-1 bg-white border border-[#E8E8E6] rounded-xl overflow-hidden">{!! old('content',$blog->content) !!}</div>
    <input type="hidden" name="content" id="content">
  </div>

  <div class="mt-4 p-4 bg-[#F8F8F7] border border-[#E8E8E6] rounded-2xl">
    <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">SEO / AEO</div>
    <div class="mt-3 grid sm:grid-cols-2 gap-3">
      <div class="sm:col-span-2"><label class="text-[11px] font-semibold text-[#7A7A78]">Meta Title (70)</label><input name="meta_title" id="meta_title" value="{{ old('meta_title',$blog->meta_title) }}" maxlength="70" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]"><div class="text-[11px] font-mono text-[#7A7A78]"><span id="mtCount">0</span>/70</div></div>
      <div class="sm:col-span-2"><label class="text-[11px] font-semibold text-[#7A7A78]">Meta Description (165)</label><textarea name="meta_description" id="meta_desc" rows="2" maxlength="165" class="mt-1 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-white text-[13px]">{{ old('meta_description',$blog->meta_description) }}</textarea><div class="text-[11px] font-mono text-[#7A7A78]"><span id="mdCount">0</span>/165</div></div>
      <div class="sm:col-span-2"><label class="text-[11px] font-semibold text-[#7A7A78]">Canonical URL</label><input name="canonical_url" value="{{ old('canonical_url',$blog->canonical_url) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]"></div>
      <div><label class="text-[11px] font-semibold text-[#7A7A78]">Cover Image</label><input type="file" name="cover" accept="image/*" class="mt-1 w-full text-[13px]">@if($blog->cover)<div class="mt-1 text-[11px] text-[#7A7A78]">Current: {{ $blog->cover }}</div>@endif</div>
      <div><label class="text-[11px] font-semibold text-[#7A7A78]">Cover Alt</label><input name="cover_alt" value="{{ old('cover_alt',$blog->cover_alt) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]"></div>
    </div>
  </div>

  <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
    <div class="text-[11px] font-bold tracking-widest uppercase text-amber-800">Per-Post FAQs for AEO</div>
    <div id="faqList" class="mt-3 space-y-3"></div>
    <button type="button" onclick="addFaq()" class="mt-3 h-9 px-4 rounded-full border border-amber-300 bg-white font-bold text-[12px]">+ Add FAQ</button>
    <input type="hidden" name="faq_json" id="faq_json">
  </div>

  <div class="mt-4 flex gap-3">
    <label class="flex items-center gap-2 text-[13px] font-bold"><input type="checkbox" name="is_published" value="1" {{ old('is_published',$blog->is_published) ? 'checked':'' }}> Published</label>
    <label class="flex items-center gap-2 text-[13px] font-bold"><input type="checkbox" name="is_featured" value="1" {{ old('is_featured',$blog->is_featured) ? 'checked':'' }}> Featured</label>
  </div>

  @if($errors->any())<div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-3 py-2 text-[13px] font-semibold">{{ $errors->first() }}</div>@endif

  <div class="mt-6 flex gap-3">
    <button type="submit" class="flex-1 h-11 rounded-full bg-[#0F0F0F] text-white font-extrabold text-[14px]">Save Changes</button>
    <a href="{{ route('blog.show',$blog->slug) }}" target="_blank" class="h-11 px-6 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px] inline-flex items-center">View live</a>
  </div>
</form>

<form method="POST" action="{{ route('admin.blogs.destroy',$blog) }}" onsubmit="return confirm('Delete this post?')" class="max-w-[860px] mt-4 text-center">@csrf @method('DELETE')<button class="text-[12px] font-bold text-red-600 hover:underline">Delete post permanently</button></form>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
const quill = new Quill('#editor', { theme:'snow', modules:{ toolbar:{ container:[[{header:[2,3,false]}],['bold','italic','underline'],[{list:'ordered'},{list:'bullet'}],['link','image','blockquote','code-block'],[{color:[]},{background:[]}],['clean']], handlers:{ image: imageHandler } } } });
function imageHandler(){ const input=document.createElement('input'); input.setAttribute('type','file'); input.setAttribute('accept','image/*'); input.click(); input.onchange=()=>{ const file=input.files[0]; if(!file) return; const fd=new FormData(); fd.append('image', file); fd.append('_token','{{ csrf_token() }}'); fetch('{{ route('admin.blogs.upload') }}',{method:'POST', body:fd}).then(r=>r.json()).then(d=>{ if(d.url){ const range=quill.getSelection(true); quill.insertEmbed(range.index,'image', d.url); } }).catch(()=>alert('Upload failed')); }; }
function updateCounts(){ const mt=document.getElementById('meta_title'); const md=document.getElementById('meta_desc'); document.getElementById('mtCount').textContent=mt.value.length; document.getElementById('mdCount').textContent=md.value.length; }
document.getElementById('meta_title').addEventListener('input', updateCounts); document.getElementById('meta_desc').addEventListener('input', updateCounts); updateCounts();
const existingFaq = @json($blog->faq_json ?? []);
function addFaq(q='',a=''){ const list=document.getElementById('faqList'); const div=document.createElement('div'); div.className='bg-white border border-amber-200 rounded-xl p-3 flex gap-2'; div.innerHTML=`<div class="flex-1"><input placeholder="Question" value="${q.replace(/"/g,'&quot;')}" class="faq-q w-full h-9 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold"><input placeholder="Answer" value="${a.replace(/"/g,'&quot;')}" class="faq-a mt-2 w-full h-9 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div><button type="button" onclick="this.parentElement.remove(); syncFaq()" class="h-8 w-8 rounded-full border border-red-200 bg-red-50 text-red-600 grid place-items-center shrink-0">✕</button>`; list.appendChild(div); div.querySelectorAll('input').forEach(i=>i.addEventListener('input', syncFaq)); syncFaq();
}
function syncFaq(){ const items=[...document.querySelectorAll('#faqList > div')].map(d=>({q:d.querySelector('.faq-q').value.trim(), a:d.querySelector('.faq-a').value.trim()})).filter(x=>x.q && x.a); document.getElementById('faq_json').value= items.length ? JSON.stringify(items.map(x=>({q:x.q,a:x.a}))) : ''; }
existingFaq.forEach(f=> addFaq(f.q||f.question||'', f.a||f.answer||'')); syncFaq();
document.getElementById('blogForm').addEventListener('submit', ()=>{ document.getElementById('content').value=quill.root.innerHTML; syncFaq(); });
</script>
@endsection
