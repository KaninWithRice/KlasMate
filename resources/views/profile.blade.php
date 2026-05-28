@extends('layouts.app')

@section('content')
<div class="p-6 pb-24">
    <!-- Navigation Back -->
    <div class="mb-6">
        <a href="{{ route('friends') }}" class="flex items-center space-x-2 text-black text-[13.7px] font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Back to Friends</span>
        </a>
    </div>

    <!-- Profile Header -->
    <div class="flex flex-col items-center mb-10">
        <div class="w-[50px] h-[50px] bg-[#f5c32f] rounded-full flex items-center justify-center border border-black overflow-hidden shadow-sm mb-3">
            <span class="text-[20px] font-bold text-black uppercase">D</span>
        </div>
        <h1 class="text-[19.6px] font-bold text-black">Display Name</h1>
        <p class="text-[#787878] text-[14.7px] font-medium">6 courses</p>
    </div>

    <!-- Courses Grid -->
    <div class="grid grid-cols-2 gap-4">
        @foreach(['Mathematics', 'Fieldtrip', 'Computer Drafting', 'English', 'Economics'] as $course)
            @php
                $colors = ['bg-[#f5c32f]', 'bg-[#072ac6]', 'bg-[#07a954]', 'bg-[#f50220]', 'bg-[#ff5aa9]', 'bg-[#af78d3]'];
                $color = $colors[$loop->index % count($colors)];
                $textColor = in_array($color, ['bg-[#072ac6]', 'bg-[#07a954]', 'bg-[#f50220]', 'bg-[#af78d3]']) ? 'text-white' : 'text-black';
            @endphp
            <div class="relative {{ $color }} border border-black rounded-[10px] h-[119px] p-3 flex flex-col justify-between shadow-sm cursor-pointer hover:opacity-90">
                <p class="text-[15.4px] font-medium leading-tight {{ $textColor }}">{{ $course }}</p>
                <div class="flex justify-end">
                    <svg class="w-5 h-5 {{ $textColor }} opacity-80" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5s-3 1.34-3 3 1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </div>
            </div>
        @endforeach
    </div>
</div>

<x-navigation />
@endsection
