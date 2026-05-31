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
            <div class="w-full h-full flex flex-col bg-white" x-data="{ loading: true }">
                <div class="flex-1 relative bg-gray-100">
                    <!-- Loading Indicator -->
                    <div x-show="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 z-10">
                        <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-4"></div>
                        <p class="text-gray-500 font-bold animate-pulse text-sm">Preparing Preview...</p>
                    </div>

                    <iframe 
                        src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($publicUrl) }}" 
                        class="absolute inset-0 w-full h-full border-none z-0"
                        @load="loading = false">
                    </iframe>
                </div>
                
                <!-- Mobile Control Panel -->
                <div class="bg-[#1a1a1a] p-5 pb-8 text-center border-t border-white/10 backdrop-blur-xl">
                    <p class="text-white font-bold text-[14px] mb-1 truncate px-4">{{ $file->name }}</p>
                    <p class="text-white/40 text-[10px] mb-4 uppercase tracking-widest font-black">Document Preview</p>
                    
                    <div class="grid grid-cols-2 gap-3 px-2">
                        <a href="{{ $publicUrl }}" target="_blank" class="flex items-center justify-center space-x-2 bg-blue-600 text-white py-3 rounded-xl font-bold text-[13px] active:scale-95 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            <span>Open Original</span>
                        </a>
                        <a href="https://docs.google.com/viewer?url={{ urlencode($publicUrl) }}&embedded=true" target="_blank" class="flex items-center justify-center space-x-2 bg-white/10 text-white py-3 rounded-xl font-bold text-[13px] border border-white/20 active:scale-95 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            <span>Google Viewer</span>
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('files.download', $file) }}" class="text-white/40 text-[11px] font-bold hover:text-white transition-colors underline decoration-white/20 underline-offset-4">
                            Or download the file directly
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
