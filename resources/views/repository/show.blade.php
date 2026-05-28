@extends('layouts.app')

@section('content')
<div class="w-full max-w-4xl">
    <div class="flex items-center space-x-2 text-gray-400 mb-8 font-bold">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('repository.index', $file->folder) }}" class="hover:text-blue-600">{{ $file->folder->name }}</a>
        <span>/</span>
        <span class="text-blue-900">{{ $file->name }}</span>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="p-8 flex items-start justify-between bg-blue-50/30">
            <div class="flex items-center space-x-6">
                <div class="bg-blue-600 p-4 rounded-2xl text-white shadow-lg">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-blue-900 mb-1">{{ $file->name }}</h1>
                    <p class="text-gray-500 font-medium">Uploaded by <span class="text-blue-700">{{ $file->user->name }}</span> • {{ $file->created_at->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('files.view', $file) }}" target="_blank" class="bg-white text-blue-700 px-6 py-3 rounded-xl font-bold border-2 border-blue-100 hover:bg-blue-50 transition">View PDF</a>
                <a href="{{ route('files.download', $file) }}" class="bg-blue-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-800 transition">Download</a>
            </div>
        </div>

        <div class="p-8">
            <h2 class="text-xl font-bold text-blue-900 mb-6 flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                <span>Discussion Thread</span>
            </h2>

            <div class="space-y-6 mb-10">
                @foreach($file->comments as $comment)
                    <div class="flex space-x-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-black text-xs uppercase">
                            {{ substr($comment->user->name, 0, 2) }}
                        </div>
                        <div class="flex-1 bg-gray-50 p-4 rounded-2xl rounded-tl-none border border-gray-100">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-blue-900 text-sm">{{ $comment->user->name }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-600 text-sm leading-relaxed">{{ $comment->body }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('comments.store', $file) }}" method="POST" class="relative">
                @csrf
                <textarea name="body" placeholder="Write a comment..." required class="w-full p-5 bg-blue-50/50 border-2 border-blue-50 rounded-3xl focus:border-blue-500 outline-none transition-all text-gray-700 placeholder-blue-300 resize-none h-32"></textarea>
                <button type="submit" class="absolute bottom-4 right-4 bg-blue-700 text-white px-6 py-2 rounded-full font-bold shadow-md hover:bg-blue-800 transition">Post</button>
            </form>
        </div>
    </div>

    <!-- Sharing Section -->
    <div class="bg-yellow-50 p-8 rounded-3xl border-2 border-yellow-100 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="bg-yellow-400 p-3 rounded-xl text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-yellow-900">Share this Reviewer</h3>
                <p class="text-xs text-yellow-700 font-medium opacity-70">Anyone with the link can view this file</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 bg-white p-2 rounded-2xl border border-yellow-200">
            <input type="text" readonly value="{{ route('files.show', $file) }}" id="shareLink" class="bg-transparent border-none outline-none text-sm font-bold text-gray-500 px-2 w-64">
            <button onclick="navigator.clipboard.writeText(document.getElementById('shareLink').value); this.innerText = 'Copied!'" class="bg-yellow-400 text-white px-4 py-2 rounded-xl font-bold hover:bg-yellow-500 transition">Copy</button>
        </div>
    </div>
</div>
@endsection
