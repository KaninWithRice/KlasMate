@extends('layouts.app')

@section('content')
<div class="p-6 pb-24" x-data="{ 
    filter: 'ALL',
    notifications: [] 
}">
    <!-- Header -->
    <div class="flex justify-between items-center mb-10 pt-4">
        <h1 class="text-[31px] font-bold text-black leading-tight">Notifications</h1>
        <button class="flex items-center space-x-2 border border-[#464646] px-4 py-1.5 rounded-full text-[10.3px] font-bold">
            <span>DATE</span>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
        </button>
    </div>

    <!-- Filter Icons -->
    <div class="flex items-center space-x-2 mb-10 overflow-x-auto no-scrollbar pb-2">
        <button @click="filter = 'ALL'" 
            :class="filter === 'ALL' ? 'bg-[#072ac6] border-[#072ac6] text-white' : 'bg-white border-black text-black'"
            class="border px-5 py-2 h-[38px] rounded-full text-[10.3px] font-bold shadow-sm transition-all flex items-center justify-center">
            ALL
        </button>
        
        @php
            $filters = [
                ['name' => 'ADD_FRIEND', 'svg' => '<path d="M15 14c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-9-4V7H4v3H1v2h3v3h2v-3h3v-2H6zm9-2c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/>'],
                ['name' => 'FRIENDS', 'svg' => '<path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>'],
                ['name' => 'INBOX', 'svg' => '<path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>'],
                ['name' => 'FILE_ADD', 'svg' => '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11zM8 15h2v2h2v-2h2v-2h-2v-2h-2v2H8v2z"/>'],
                ['name' => 'FILE', 'svg' => '<path d="M13 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V7l-7-5zM6 20V4h6v4h5v12H6z"/>'],
                ['name' => 'FOLDER', 'svg' => '<path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>'],
            ];
        @endphp

        @foreach($filters as $f)
            <button @click="filter = '{{ $f['name'] }}'"
                :class="filter === '{{ $f['name'] }}' ? 'bg-[#072ac6] border-[#072ac6] text-white scale-110' : 'bg-white border-black text-black scale-100'"
                class="w-[38px] h-[38px] rounded-full flex-shrink-0 flex items-center justify-center border shadow-sm transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    {!! $f['svg'] !!}
                </svg>
            </button>
        @endforeach
    </div>

    <!-- Notifications List -->
    <div class="space-y-4">
        @forelse($notifications ?? [] as $notification)
            <!-- Template for notification items when implemented -->
            <div class="p-4 border border-black rounded-[10px] bg-white shadow-sm flex items-center space-x-4">
                <div class="w-10 h-10 rounded-full bg-[#f5c32f] flex items-center justify-center flex-shrink-0 border border-black">
                    <svg class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                </div>
                <div>
                    <p class="text-[14px] font-bold text-black leading-tight">Notification Title</p>
                    <p class="text-[11px] text-[#787878] font-medium mt-0.5">Notification description or message goes here.</p>
                </div>
            </div>
        @empty
            <!-- Empty State from NOTIFICATION MODULE EMPTY.png -->
            <div class="mt-20 border-[2px] border-[#787878] border-dashed rounded-[10px] h-[89px] flex items-center justify-center">
                <p class="text-[15.4px] text-[#787878] font-bold">No Notifications yet</p>
            </div>
        @endforelse
    </div>
</div>

<x-navigation />

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
