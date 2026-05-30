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
    <div class="flex-1 w-full bg-[#333] relative overflow-y-auto overflow-x-hidden">
        @if($isPDF)
            {{-- Enhanced PDF Embedding for Mobile Scrolling --}}
            <div class="w-full h-full flex flex-col">
                <object data="{{ $publicUrl }}" type="application/pdf" class="w-full h-full min-h-[calc(100vh-64px)]">
                    <embed src="{{ $publicUrl }}" type="application/pdf" class="w-full h-full min-h-[calc(100vh-64px)]" />
                    <div class="p-8 text-center text-white">
                        <p class="mb-4">This PDF cannot be displayed directly.</p>
                        <a href="{{ $publicUrl }}" target="_blank" class="bg-blue-600 px-6 py-2 rounded-full font-bold">Open PDF in New Tab</a>
                    </div>
                </object>
            </div>
        @elseif($isImage)
            {{-- Integrated Image Display with Filename --}}
            <div class="w-full h-full flex flex-col items-center justify-center p-4 bg-[#111]">
                <div class="bg-white/5 px-4 py-2 rounded-full mb-4 border border-white/10">
                    <p class="text-white font-bold text-sm">{{ $file->name }}</p>
                </div>
                <img src="{{ $publicUrl }}" class="max-w-full max-h-[70vh] object-contain shadow-2xl rounded-lg" alt="{{ $file->name }}">
                <div class="mt-8">
                    <a href="{{ route('files.download', $file) }}" class="bg-white text-black px-6 py-2 rounded-full font-bold text-sm hover:bg-gray-200 transition">Download Image</a>
                </div>
            </div>
        @else
            {{-- Microsoft Office Viewer with Mobile Optimization --}}
            <div class="w-full h-full flex flex-col bg-white">
                <div class="flex-1 relative">
                    <iframe src="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode($publicUrl) }}" 
                        class="absolute inset-0 w-full h-full border-none">
                    </iframe>
                </div>
                <div class="bg-black/90 p-6 text-center border-t border-white/10 backdrop-blur-xl">
                    <p class="text-white font-bold text-[15px] mb-1">{{ $file->name }}</p>
                    <p class="text-white/60 text-[11px] mb-4">If the preview is blank, please use the options below:</p>
                    <div class="flex flex-col space-y-3 px-4">
                        <a href="{{ $publicUrl }}" target="_blank" class="w-full bg-blue-600 text-white py-3 rounded-full font-bold text-[14px] shadow-lg">Open Original File</a>
                        <a href="https://docs.google.com/viewer?url={{ urlencode($publicUrl) }}&embedded=true" target="_blank" class="w-full bg-white/10 text-white py-3 rounded-full font-bold text-[14px] border border-white/20">Try Google Viewer</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
