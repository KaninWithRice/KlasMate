@extends('layouts.app')

@section('content')
<div class="fixed inset-0 bg-black flex flex-col z-[60]">
    <!-- Header -->
    <div class="h-16 bg-[#1a1a1a] flex items-center justify-between px-6 border-b border-white/10">
        <div class="flex items-center space-x-4">
            <button onclick="window.history.back()" class="text-white hover:text-blue-400 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg>
            </button>
            <h1 class="text-white font-bold text-lg truncate max-w-[200px] md:max-w-md">{{ $file->name }}</h1>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('files.download', $file) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">Download</a>
        </div>
    </div>

    <!-- Viewer -->
    <div class="flex-1 w-full bg-[#333] relative">
        @if($extension === 'pdf')
            {{-- Direct Stream for PDF --}}
            <iframe src="{{ $streamUrl }}" class="w-full h-full border-none" title="{{ $file->name }}"></iframe>
        @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
            {{-- Direct Stream for Images --}}
            <div class="w-full h-full flex items-center justify-center p-4">
                <img src="{{ $streamUrl }}" class="max-w-full max-h-full object-contain shadow-2xl" alt="{{ $file->name }}">
            </div>
        @else
            {{-- Office Files and others via Google Docs Viewer --}}
            <iframe src="https://docs.google.com/viewer?url={{ urlencode($publicUrl) }}&embedded=true" 
                class="w-full h-full border-none bg-white">
            </iframe>
        @endif
    </div>
</div>
@endsection
