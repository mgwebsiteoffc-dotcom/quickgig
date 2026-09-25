@extends('admin.layout')
@section('title','Skills')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
  <div>
    <h1 class="text-[22px] font-black tracking-tight">Skill library</h1>
    <p class="text-[13px] font-medium text-[#7A7A78]">
      The list freelancers pick from on sign-up and in their profile. {{ $active }} of {{ $total }} active.
    </p>
  </div>
  <form method="GET" class="flex gap-2">
    <input name="q" value="{{ $filters['q'] }}" placeholder="Search skills"
           class="h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px] w-[190px]">
    <select name="discipline" class="h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]">
      <option value="">All disciplines</option>
      @foreach($disciplines as $d)
        <option value="{{ $d }}" @selected($filters['discipline'] === $d)>{{ $d }}</option>
      @endforeach
    </select>
    <button class="h-10 px-4 rounded-xl bg-[#0F0F0F] text-white font-bold text-[13px]">Filter</button>
  </form>
</div>

@if($errors->any())
  <div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-2.5 text-[13px] font-semibold">{{ $errors->first() }}</div>
@endif

<div class="mt-5 grid lg:grid-cols-[1fr_320px] gap-5 items-start">

  {{-- library --}}
  <div class="space-y-4">
    @forelse($skills as $discipline => $items)
      <div class="bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
        <div class="px-4 py-3 bg-[#F8F8F7] border-b border-[#E8E8E6] flex items-center justify-between">
          <span class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">{{ $discipline }}</span>
          <span class="text-[11px] font-bold text-[#7A7A78]">{{ $items->count() }}</span>
        </div>

        <div class="divide-y divide-[#F0F0EE]">
          @foreach($items as $skill)
            <div class="px-4 py-2.5 flex flex-wrap items-center gap-3" x-data="{ edit: false }">
              <div x-show="!edit" class="flex items-center gap-2.5 flex-1 min-w-0">
                <span class="w-2 h-2 rounded-full shrink-0 {{ $skill->is_active ? 'bg-green-500' : 'bg-[#D4D4D2]' }}"></span>
                <span class="text-[13.5px] font-semibold {{ $skill->is_active ? '' : 'text-[#9A9A98] line-through' }}">{{ $skill->name }}</span>
                <span class="text-[11.5px] text-[#7A7A78]">{{ $skill->usage_count }} {{ Str::plural('profile', $skill->usage_count) }}</span>
              </div>

              <form x-show="edit" x-cloak method="POST" action="{{ route('admin.skills.update', $skill) }}" class="flex flex-wrap items-center gap-2 flex-1">
                @csrf @method('PUT')
                <input name="name" value="{{ $skill->name }}" required class="h-9 px-3 rounded-lg border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] flex-1 min-w-[140px]">
                <select name="discipline" class="h-9 px-2 rounded-lg border border-[#E8E8E6] bg-[#F8F8F7] text-[12.5px]">
                  @foreach($disciplines as $d)<option value="{{ $d }}" @selected($skill->discipline === $d)>{{ $d }}</option>@endforeach
                </select>
                <input name="sort_order" type="number" min="0" value="{{ $skill->sort_order }}" class="h-9 w-16 px-2 rounded-lg border border-[#E8E8E6] bg-[#F8F8F7] text-[12.5px]" title="Sort order">
                <button class="h-9 px-3 rounded-lg bg-[#0F0F0F] text-white font-bold text-[12px]">Save</button>
                <button type="button" x-on:click="edit=false" class="text-[12px] text-[#7A7A78]">Cancel</button>
              </form>

              <div x-show="!edit" class="flex items-center gap-1.5 shrink-0">
                <button x-on:click="edit=true" class="h-8 px-3 rounded-lg border border-[#E8E8E6] text-[12px] font-semibold hover:bg-[#F8F8F7]">Edit</button>
                <form method="POST" action="{{ route('admin.skills.toggle', $skill) }}">@csrf
                  <button class="h-8 px-3 rounded-lg border border-[#E8E8E6] text-[12px] font-semibold hover:bg-[#F8F8F7]">{{ $skill->is_active ? 'Hide' : 'Show' }}</button>
                </form>
                <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" onsubmit="return confirm('Delete {{ $skill->name }}? Profiles already using it keep the value.')">
                  @csrf @method('DELETE')
                  <button class="h-8 px-3 rounded-lg border border-red-200 text-red-600 text-[12px] font-semibold hover:bg-red-50">Delete</button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="bg-white border border-dashed border-[#E8E8E6] rounded-2xl p-12 text-center">
        <div class="font-black">No skills yet</div>
        <div class="text-[13px] text-[#7A7A78] font-medium">Add them on the right — freelancers can only pick what is listed here.</div>
      </div>
    @endforelse
  </div>

  {{-- add --}}
  <div class="space-y-4">
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Add a skill</div>
      <form method="POST" action="{{ route('admin.skills.store') }}" class="mt-3 space-y-2.5">
        @csrf
        <input name="name" required placeholder="Retention editing" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
        <select name="discipline" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
          @foreach($disciplines as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach
        </select>
        <button class="w-full h-10 rounded-xl bg-[#0F0F0F] text-white font-bold text-[13px]">Add skill</button>
      </form>
    </div>

    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Bulk add</div>
      <p class="mt-1 text-[12px] text-[#7A7A78]">One per line or comma separated. Duplicates are skipped.</p>
      <form method="POST" action="{{ route('admin.skills.bulk') }}" class="mt-3 space-y-2.5">
        @csrf
        <textarea name="names" rows="5" required placeholder="Colour grading&#10;Sound design&#10;Motion tracking"
                  class="w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] leading-6"></textarea>
        <select name="discipline" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
          @foreach($disciplines as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach
        </select>
        <button class="w-full h-10 rounded-xl border border-[#E8E8E6] font-bold text-[13px] hover:bg-[#F8F8F7]">Add all</button>
      </form>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-[12.5px] leading-5">
      <b>Hiding vs deleting.</b> Hiding removes a skill from the picker but leaves it on profiles that
      already use it. Deleting only removes it from the library — existing profiles keep the value
      until the freelancer edits them.
    </div>
  </div>
</div>
@endsection
