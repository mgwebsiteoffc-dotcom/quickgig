@extends('admin.layout')
@section('title','FAQs')
@section('breadcrumb','Admin • Content • FAQs')
@section('content')
<div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 flex gap-3">
  <div class="w-8 h-8 rounded-full bg-[#2563EB] text-white grid place-items-center font-black shrink-0">?</div>
  <div class="text-[13px] leading-5"><b>AEO — Answer Engine Optimization:</b> These FAQs auto-render on landing + per-blog FAQPage JSON-LD. Google & Perplexity pick them up. Keep answers <b>concise (40-60 words), answer-first, no fluff</b>. Order via <b>Sort</b>. Featured = landing. File cache is cleared automatically on save.</div>
</div>

<div class="mt-4 grid lg:grid-cols-[1.6fr_0.9fr] gap-6">
  <div class="bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-[#F0F0EE] flex items-center justify-between">
      <div class="font-black text-[14px]">All FAQs</div>
      <form method="GET" class="flex gap-2"><input name="q" value="{{ $q }}" placeholder="Search..." class="h-9 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"><button class="h-9 px-4 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Search</button></form>
    </div>
    <div class="divide-y divide-[#F0F0EE]">
      @forelse($faqs as $f)
      <div class="p-4 hover:bg-[#F8F8F7]/50">
        <form method="POST" action="{{ route('admin.faqs.update',$f) }}" class="space-y-2">@csrf @method('PUT')
          <input name="question" value="{{ $f->question }}" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px] font-bold">
          <textarea name="answer" rows="3" class="w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] leading-6">{{ $f->answer }}</textarea>
          <div class="flex flex-wrap gap-2">
            <select name="category" class="h-9 px-3 rounded-full border border-[#E8E8E6] bg-white text-[12px] font-bold"><option>General</option><option @selected($f->category=='Pricing')>Pricing</option><option @selected($f->category=='Delivery')>Delivery</option><option @selected($f->category=='Creators')>Creators</option></select>
            <input name="sort_order" type="number" value="{{ $f->sort_order }}" class="w-[90px] h-9 px-3 rounded-full border border-[#E8E8E6] bg-white text-[12px] font-bold" placeholder="Sort">
            <input name="slug" value="{{ $f->slug }}" class="flex-1 h-9 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[12px] font-mono" placeholder="slug">
            <label class="flex items-center gap-1 text-[12px] font-bold"><input type="checkbox" name="is_published" value="1" {{ $f->is_published?'checked':'' }}> Published</label>
            <label class="flex items-center gap-1 text-[12px] font-bold"><input type="checkbox" name="is_featured" value="1" {{ $f->is_featured?'checked':'' }}> Featured (landing)</label>
            <button class="h-9 px-4 rounded-full bg-[#0F0F0F] text-white font-bold text-[12px]">Save</button>
          </div>
        </form>
        <form method="POST" action="{{ route('admin.faqs.destroy',$f) }}" onsubmit="return confirm('Delete FAQ?')" class="mt-2">@csrf @method('DELETE')<button class="text-[11px] font-bold text-red-600 hover:underline">Delete</button></form>
      </div>
      @empty
      <div class="p-8 text-center text-[#7A7A78] font-medium">No FAQs yet.</div>
      @endforelse
    </div>
    <div class="p-4 border-t border-[#F0F0EE]">{{ $faqs->links() }}</div>
  </div>

  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5 h-fit">
    <div class="font-black text-[14px]">Add FAQ</div>
    <div class="text-[12px] font-medium text-[#7A7A78]">Writes to DB → landing FAQPage JSON-LD updates instantly (AEO).</div>
    <form method="POST" action="{{ route('admin.faqs.store') }}" class="mt-4 space-y-3">@csrf
      <input name="question" required placeholder="Question — e.g. What does it cost?" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold">
      <textarea name="answer" required rows="4" placeholder="Answer — concise, 40-60 words, answer first for AEO. Mention escrow and pricing clearly." class="w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] leading-6"></textarea>
      <div class="grid grid-cols-2 gap-3">
        <select name="category" class="h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold"><option>General</option><option>Pricing</option><option>Delivery</option><option>Creators</option></select>
        <input name="sort_order" type="number" value="99" class="h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]" placeholder="Sort (1 = top)">
      </div>
      <label class="flex items-center gap-2 text-[12px] font-bold"><input type="checkbox" name="is_published" value="1" checked> Published</label>
      <label class="flex items-center gap-2 text-[12px] font-bold"><input type="checkbox" name="is_featured" value="1" checked> Featured on landing</label>
      <button class="w-full h-10 rounded-full bg-[#2563EB] text-white font-bold text-[13px]">Add FAQ → live JSON-LD</button>
    </form>
    <div class="mt-4 text-[11px] font-medium text-[#7A7A78]">Preview JSON-LD at <code class="bg-[#F8F8F7] border px-1 py-0.5 rounded">view-source:https://yourdomain.com</code> → search <code class="bg-[#F8F8F7] px-1 py-0.5 rounded">FAQPage</code>.</div>
  </div>
</div>
@endsection
