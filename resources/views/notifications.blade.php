@extends('layouts.app')

@section('content')
<div class="p-6 pb-24">
    <h1 class="text-[31px] font-bold text-black leading-tight mt-4 mb-8">Notifications</h1>

    <div class="space-y-4">
        @forelse($requests as $request)
            <div class="p-4 border border-black rounded-[15px] bg-white shadow-sm flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-[45px] h-[45px] bg-[#f5c32f] rounded-full flex items-center justify-center border border-black overflow-hidden shadow-sm flex-shrink-0">
                        @if($request->avatar)<img src="{{ $request->avatar }}" class="w-full h-full object-cover">@else<span class="text-[18px] font-bold text-black uppercase">{{ $request->name[0] }}</span>@endif
                    </div>
                    <div>
                        <p class="text-[16px] font-bold text-black leading-tight">{{ $request->name }}</p>
                        <p class="text-[11px] text-[#787878] font-medium mt-0.5">Sent you a friend request</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <form action="{{ route('friends.accept', $request->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-[#072ac6] text-white px-4 py-1.5 rounded-full text-[11px] font-bold shadow-sm active:scale-95 transition-all">Accept</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-16 text-center border-2 border-dashed border-[#d9d9d9] rounded-[20px] bg-gray-50/50">
                <p class="text-[#787878] font-bold text-[16px]">No new notifications</p>
            </div>
        @endforelse
    </div>
</div>

<x-navigation />
@endsection
