@extends('admin.layout')
@section('title','Users & Roles')
@section('breadcrumb','Admin • Users')
@section('content')
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex gap-3">
  <div class="w-8 h-8 rounded-full bg-amber-400 text-[#0F0F0F] grid place-items-center font-black shrink-0">!</div>
  <div class="text-[13px] leading-5"><b>Roles explained:</b> <b>Super Admin</b> = everything + delete users + settings. <b>Admin</b> = everything except super actions. <b>Manager</b> = orders + creators + services. <b>Support</b> = view orders + chat. <b>Finance</b> = payouts + escrow releases. Super Admin can assign any role; Admin cannot create Super Admin.</div>
</div>

<div class="mt-4 grid lg:grid-cols-[1.7fr_0.9fr] gap-6">
  <div class="bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-[#F0F0EE] flex items-center justify-between"><div class="font-black text-[14px]">Team Members</div><span class="text-[11px] font-bold bg-[#F8F8F7] border border-[#E8E8E6] px-2.5 py-1 rounded-full">{{ count($users) }} users</span></div>
    <div class="divide-y divide-[#F0F0EE]">
      @foreach($users as $u)
      <div class="flex flex-wrap items-center gap-3 px-5 py-4 hover:bg-[#F8F8F7]/50">
        <div class="w-9 h-9 rounded-full bg-[#0F0F0F] text-white grid place-items-center font-black text-[11px]">{{ strtoupper(substr($u['name'],0,1)) }}</div>
        <div class="flex-1 min-w-[180px]"><div class="text-[13px] font-bold">{{ $u['name'] }}</div><div class="text-[11px] font-semibold text-[#7A7A78]">{{ $u['email'] }}</div></div>
        <span class="px-2.5 py-1 rounded-full text-[11px] font-black border
          @if($u['role']=='super_admin') bg-[#0F0F0F] text-white border-[#0F0F0F]
          @elseif($u['role']=='admin') bg-violet-600 text-white border-violet-600
          @elseif($u['role']=='manager') bg-blue-600 text-white border-blue-600
          @elseif($u['role']=='support') bg-green-600 text-white border-green-600
          @elseif($u['role']=='finance') bg-amber-500 text-white border-amber-500
          @else bg-white border-[#E8E8E6] @endif
        ">{{ $roles[$u['role']] ?? $u['role'] }}</span>
        <form method="POST" action="{{ route('admin.users.role',$u['id']) }}" class="flex gap-1">@csrf
          <select name="role" class="h-8 px-2 rounded-full border border-[#E8E8E6] bg-white text-[12px] font-bold">
            @foreach($roles as $rk=>$rv)<option value="{{ $rk }}" {{ $u['role']==$rk ? 'selected':'' }}>{{ $rv }}</option>@endforeach
          </select>
          @if(auth()->user()->hasAnyRole(['super_admin','admin']))<button class="h-8 px-3 rounded-full bg-[#0F0F0F] text-white font-bold text-[12px]">Update</button>@endif
        </form>
        <form method="POST" action="{{ route('admin.users.toggle',$u['id']) }}">@csrf<button class="h-8 w-8 rounded-full border grid place-items-center bg-white border-[#E8E8E6] text-[12px]">{{ $u['is_active'] ? '✓' : '✕' }}</button></form>
        @if(auth()->user()->isSuperAdmin())<form method="POST" action="{{ route('admin.users.destroy',$u['id']) }}" onsubmit="return confirm('Delete user?')">@csrf @method('DELETE')<button class="h-8 w-8 rounded-full border border-red-200 bg-red-50 text-red-600 grid place-items-center">✕</button></form>@endif
      </div>
      @endforeach
    </div>
  </div>

  @if(auth()->user()->hasAnyRole(['super_admin','admin']))
  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5 h-fit">
    <div class="font-black text-[14px]">Add Team Member</div>
    <div class="text-[12px] font-medium text-[#7A7A78]">Creates login for admin panel. Password is hashed. Sessions are file based by default.</div>
    <form method="POST" action="{{ route('admin.users.store') }}" class="mt-4 space-y-3">@csrf
      <input name="name" required placeholder="Full name" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
      <input name="email" required type="email" placeholder="Email" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
      <select name="role" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold">
        @foreach($roles as $rk=>$rv)<option value="{{ $rk }}">{{ $rv }}</option>@endforeach
      </select>
      <input name="password" required type="password" placeholder="Password (min 8 chars)" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
      <button class="w-full h-10 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Create User</button>
    </form>
    <div class="mt-3 text-[11px] font-semibold text-[#7A7A78]">Tip: after creating, test the login in a private window. File sessions need <code class="bg-[#F8F8F7] border border-[#E8E8E6] px-1 py-0.5 rounded">storage/framework/sessions</code> writable (755).</div>
  </div>
  @endif
</div>
@endsection
