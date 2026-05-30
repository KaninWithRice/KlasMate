@extends('layouts.app')

@section('content')
<div class="flex flex-col h-screen bg-white">
    <!-- Chat Header -->
    <div class="p-6 border-b border-black flex items-center space-x-4">
        <a href="{{ route('friends') }}" class="text-black p-1 hover:bg-gray-100 rounded-full transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </a>
        <div class="flex items-center space-x-3">
            <div class="w-[40px] h-[40px] bg-[#f5c32f] rounded-full flex items-center justify-center border border-black overflow-hidden shadow-sm">
                @if($friend->avatar)<img src="{{ $friend->avatar }}" class="w-full h-full object-cover">@else<span class="text-[14px] font-bold text-black uppercase">{{ $friend->name[0] }}</span>@endif
            </div>
            <div>
                <p class="text-[16px] font-bold text-black leading-tight">{{ $friend->name }}</p>
                <p class="text-[10px] text-[#07a954] font-bold uppercase tracking-wider">Online</p>
            </div>
        </div>
    </div>

    <!-- Messages Area -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6 no-scrollbar" id="message-container">
        @forelse($messages as $msg)
            <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%]">
                    @if($msg->type === 'folder' && $msg->folder)
                        <div class="border border-black rounded-[15px] p-4 bg-white shadow-sm space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 {{ $msg->folder->color ?? 'bg-[#f5c32f]' }} rounded-[8px] flex items-center justify-center border border-black shadow-sm">
                                    <svg class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[14px] font-bold text-black leading-tight">{{ $msg->folder->name }}</p>
                                    <p class="text-[10px] text-[#787878] font-medium uppercase tracking-tighter">Course Folder Invite</p>
                                </div>
                            </div>
                            <a href="{{ route('repository.index', $msg->folder->id) }}" class="block w-full bg-[#072ac6] text-white text-center py-2 rounded-full text-[12px] font-bold shadow-sm active:scale-95 transition-all">
                                Join Course
                            </a>
                        </div>
                    @elseif($msg->type === 'file' && $msg->file)
                        <div class="border border-black rounded-[15px] p-4 bg-white shadow-sm space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-[#072ac6]/10 text-[#072ac6] rounded-[8px] flex items-center justify-center border border-black/10">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[14px] font-bold text-black leading-tight">{{ $msg->file->name }}</p>
                                    <p class="text-[10px] text-[#787878] font-medium uppercase tracking-tighter">File Shared</p>
                                </div>
                            </div>
                            <a href="{{ route('files.view', $msg->file->id) }}" class="block w-full bg-[#f5c32f] text-[#072ac6] text-center py-2 rounded-full text-[12px] font-bold shadow-sm active:scale-95 transition-all border border-black">
                                View File
                            </a>
                        </div>
                    @else
                        <div class="px-4 py-2.5 rounded-[20px] {{ $msg->sender_id === auth()->id() ? 'bg-black text-white rounded-tr-none' : 'bg-[#f0f0f0] text-black rounded-tl-none border border-black/5' }}">
                            <p class="text-[14.5px] font-medium">{{ $msg->message }}</p>
                        </div>
                    @endif
                    <p class="text-[9px] text-[#787878] mt-1 {{ $msg->sender_id === auth()->id() ? 'text-right' : 'text-left' }} font-bold uppercase tracking-widest">{{ $msg->created_at->format('h:i A') }}</p>
                </div>
            </div>
        @empty
            <div class="h-full flex flex-col items-center justify-center text-center px-10">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-[#d9d9d9]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                </div>
                <p class="text-[16px] font-bold text-black">No messages yet</p>
                <p class="text-[12px] text-[#929292] mt-1">Start a conversation or share a course with {{ $friend->name }}!</p>
            </div>
        @endforelse
    </div>

    <!-- Message Input -->
    <div class="p-6 bg-white border-t border-black pb-10">
        <form action="{{ route('chat.store', $friend->id) }}" method="POST" class="flex items-center space-x-3">
            @csrf
            <input type="text" name="message" placeholder="Type a message..." required
                class="flex-1 bg-[#f0f0f0] border-none rounded-full py-3 px-6 text-[14px] font-medium text-black focus:ring-1 focus:ring-black">
            <button type="submit" class="w-[45px] h-[45px] bg-black text-white rounded-full flex items-center justify-center shadow-lg active:scale-95 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </form>
    </div>
</div>

<script>
    // Auto-scroll to bottom
    const container = document.getElementById('message-container');
    container.scrollTop = container.scrollHeight;
</script>
@endsection
