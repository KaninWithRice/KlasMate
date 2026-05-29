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
        @if($isViewable)
            @if($extension === 'pdf')
                <iframe src="{{ $streamUrl }}" class="w-full h-full border-none" title="{{ $file->name }}"></iframe>
            @else
                <div class="w-full h-full flex items-center justify-center p-4">
                    <img src="{{ $streamUrl }}" class="max-w-full max-h-full object-contain shadow-2xl" alt="{{ $file->name }}">
                </div>
            @endif
        @else
            @if(in_array($extension, ['docx', 'pptx', 'xlsx', 'doc', 'ppt', 'xls']))
                <iframe src="https://docs.google.com/viewer?url={{ urlencode(Storage::url($file->path)) }}&embedded=true" class="w-full h-full border-none"></iframe>
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-white space-y-6">
                    <div class="bg-white/10 p-8 rounded-full">
                        <svg class="w-20 h-20 text-blue-400" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-bold">Preview not available for this file type</p>
                        <p class="text-white/60 mt-2">Please download the file to view it.</p>
                    </div>
                    <a href="{{ route('files.download', $file) }}" class="bg-white text-black px-8 py-3 rounded-full font-bold hover:bg-blue-50 transition">Download Now</a>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
