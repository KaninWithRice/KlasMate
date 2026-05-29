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
        @if($isPDF)
            {{-- Use Direct Public URL for PDF Iframe --}}
            <iframe src="{{ $publicUrl }}" class="w-full h-full border-none bg-white" title="{{ $file->name }}"></iframe>
        @elseif($isImage)
            {{-- High Quality Image Display --}}
            <div class="w-full h-full flex items-center justify-center p-4 bg-[#222]">
                <img src="{{ $streamUrl }}" class="max-w-full max-h-full object-contain shadow-2xl rounded-lg" alt="{{ $file->name }}">
            </div>
        @else
            {{-- Microsoft Office Viewer --}}
            <div class="w-full h-full flex flex-col">
                <iframe src="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode($publicUrl) }}" 
                    class="flex-1 w-full border-none bg-white">
                </iframe>
                <div class="bg-black/80 p-4 text-center border-t border-white/10 backdrop-blur-md">
                    <p class="text-white/80 text-xs mb-3">If the preview doesn't load, use the link below:</p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ $publicUrl }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 transition">Open Original File</a>
                        <a href="https://docs.google.com/viewer?url={{ urlencode($publicUrl) }}&embedded=true" target="_blank" class="bg-white/10 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-white/20 transition border border-white/20">Try Google Viewer</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
