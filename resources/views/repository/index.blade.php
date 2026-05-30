@extends('layouts.app')

@section('content')
@php
    $isOwner = auth()->check();
@endphp

<div class="p-6 pb-24" x-data="{ 
    filter: 'ALL',
    openFileDropdown: null,
    showRenameModal: false,
    showDeleteModal: false,
    showShareModal: false,
    showShareToFriends: false,
    shareSearch: '',
    shareUsers: [],
    sharedUsers: [],
    sharingFile: { id: '', name: '', link: '' },
    activeFile: { id: '', name: '' },
    
    async searchShareUsers() {
        if (this.shareSearch.length < 1) {
            // If search is empty, we could show all friends or keep it empty
            this.shareUsers = [];
            return;
        }
        try {
            const response = await fetch(`/api/users/search?q=${this.shareSearch}`);
            this.shareUsers = await response.json();
        } catch (e) {
            console.error(e);
        }
    },

    openShare(file) {
        this.sharingFile = {
            id: file.id,
            name: file.name,
            link: window.location.origin + '/files/' + file.id
        };
        this.showShareModal = true;
    },

    copyShareLink() {
        navigator.clipboard.writeText(this.sharingFile.link).then(() => {
            alert('Link copied!');
        });
    },

    copyInviteLink() {
        const link = window.location.origin + window.location.pathname + '?token={{ $folder->invite_token ?? '' }}';
        navigator.clipboard.writeText(link).then(() => {
            alert('Invite link copied to clipboard!');
        });
    },

    async sendToFile(user) {
        // Logic for sharing would go here
        this.sharedUsers.push(user.id);
        // Simulate a delay or success
        setTimeout(() => {
            // Optional: keep the checkmark or reset
        }, 2000);
    }
}">
    <!-- Navigation Back -->
    <div class="mb-6">
        @if(!$isOwner && $folder && $folder->user)
            <a href="{{ route('profile', $folder->user_id) }}" class="flex items-center space-x-2 text-black text-[13.7px] font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Back to {{ $folder->user->name }}'s Profile</span>
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-black text-[13.7px] font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Back to Courses</span>
            </a>
        @endif
    </div>

    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div class="flex-1">
            <h1 class="text-[31px] font-bold text-black leading-tight">{{ $folder ? ($folder->code ?? 'Course Code') : 'Course Code' }}</h1>
            <p class="text-[#787878] text-[15.4px] font-medium mt-1">{{ $folder ? $folder->name : 'Course Name' }}</p>
        </div>
        
        @if($isOwner)
            <div class="flex items-center space-x-3 mt-1">
                <button @click="$dispatch('open-upload-sheet')" class="w-[38px] h-[38px] bg-black text-white rounded-full flex items-center justify-center shadow-sm active:scale-95 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button @click="openShare({ id: 'folder', name: '{{ addslashes($folder->name ?? 'Course') }}' })" class="w-[38px] h-[38px] bg-black text-white rounded-full flex items-center justify-center shadow-sm active:scale-95 transition-transform">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92c0-1.61-1.31-2.92-2.92-2.92z"/></svg>
                </button>
            </div>
        @endif
    </div>

    <!-- Search Bar -->
    <div class="relative mb-6">
        <span class="absolute inset-y-0 left-4 flex items-center text-[#787878]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <input type="text" placeholder="Search File Name" 
            class="w-full pl-12 pr-4 py-2 border border-[#787878] rounded-full focus:outline-none focus:ring-1 focus:ring-black text-[10.3px] font-medium text-black placeholder-black/50">
    </div>

    <!-- Filter Tabs -->
    <div class="flex space-x-2 overflow-x-auto no-scrollbar mb-8 pb-1">
        @foreach(['ALL', 'IMAGES', 'PPTX', 'DOCX', 'PDF'] as $f)
            <button @click="filter = '{{ $f }}'"
                :class="filter === '{{ $f }}' ? 'bg-[#072ac6] text-white border-[#072ac6]' : 'bg-white text-black border-black'"
                class="px-6 py-1.5 rounded-full border text-[10.3px] font-bold whitespace-nowrap transition-all shadow-sm">
                {{ $f }}
            </button>
        @endforeach
    </div>

    <!-- Files List -->
    <div class="space-y-4">
        @forelse($files as $file)
            <div class="border border-black rounded-[10px] p-4 relative group bg-white shadow-sm"
                 x-show="filter === 'ALL' || (filter === 'IMAGES' && '{{ $file->type }}' === 'image') || (filter === 'PPTX' && '{{ strtolower(pathinfo($file->path, PATHINFO_EXTENSION)) }}' === 'pptx') || (filter === 'DOCX' && '{{ strtolower(pathinfo($file->path, PATHINFO_EXTENSION)) }}' === 'docx') || (filter === 'PDF' && '{{ strtolower(pathinfo($file->path, PATHINFO_EXTENSION)) }}' === 'pdf')">
                <div class="flex items-start justify-between">
                    <div class="flex space-x-3 cursor-pointer" @click="window.location.href='{{ route('files.view', $file) }}'">
                        <div class="w-7 h-7 flex-shrink-0 text-[#072ac6]">
                            @if($file->type === 'image')
                                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                            @elseif($file->type === 'link')
                                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                            @else
                                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-[19.3px] font-bold text-black leading-tight">{{ $file->name }}</h3>
                            <div class="flex items-center space-x-2 text-[7.3px] text-[#787878] font-medium mt-1">
                                <span>Uploaded By {{ $file->user->name }}</span>
                            </div>
                        </div>
                    </div>

                    @if($isOwner)
                    <div class="relative">
                        <button class="text-black/50 hover:text-black p-1 rounded-full hover:bg-gray-100 transition-colors" 
                                @click.stop="openFileDropdown = (openFileDropdown === {{ $file->id }} ? null : {{ $file->id }})">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                        </button>

                        <div x-show="openFileDropdown === {{ $file->id }}" 
                             @click.away="openFileDropdown = null"
                             x-cloak
                             class="absolute right-0 top-8 z-30 w-[140px] bg-white border border-black rounded-[5px] shadow-lg py-1">
                            
                            <button class="w-full text-left px-3 py-2 text-[12px] flex items-center space-x-2 hover:bg-gray-100" 
                                    @click.stop="openShare({ id: {{ $file->id }}, name: '{{ addslashes($file->name) }}' }); openFileDropdown = null">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <span>Share</span>
                            </button>
                            
                            <a href="/files/{{ $file->id }}/force-delete" class="w-full text-left px-3 py-2 text-[12px] flex items-center space-x-2 text-red-600 hover:bg-red-50" onclick="return confirm('Are you sure you want to delete this file?')">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <span>Delete</span>
                            </a>

                            <button class="w-full text-left px-3 py-2 text-[12px] flex items-center space-x-2 hover:bg-gray-100" 
                                    @click.stop="activeFile = { id: {{ $file->id }}, name: '{{ addslashes($file->name) }}' }; showRenameModal = true; openFileDropdown = null">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <span>Rename</span>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        @empty
            @if($isOwner)
                <div @click="$dispatch('open-upload-sheet')" class="mt-8 border-[2px] border-dashed border-[#787878] rounded-[10px] h-[155px] flex flex-col items-center justify-center space-y-3 cursor-pointer hover:bg-black/5 transition-colors">
                    <svg class="w-10 h-10 text-[#787878]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="text-[#787878] font-bold text-[19.3px]">Upload a file</p>
                </div>
            @endif
        @endforelse
    </div>
</div>

<x-navigation />

<!-- Bottom Sheets -->
<x-bottom-sheet id="uploadSheet" title="Upload to {{ $folder?->code ?? 'Course' }}" @open-upload-sheet.window="open = true">
    <div class="space-y-6">
        <button class="w-full flex items-center space-x-4 group text-left" onclick="document.getElementById('cameraInput').click()">
            <div class="w-[41px] h-[41px] text-black">
                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <div>
                <p class="text-[16px] font-bold text-black">Camera Roll</p>
                <p class="text-[11.8px] text-[#929292] font-medium">Photos from gallery</p>
            </div>
            <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="hidden">
                @csrf
                <input type="hidden" name="folder_id" value="{{ $folder?->id }}">
                <input type="hidden" name="type" value="image">
                <input type="file" id="cameraInput" name="file" accept="image/*" onchange="this.form.submit()">
            </form>
        </button>

        <button class="w-full flex items-center space-x-4 group text-left" onclick="document.getElementById('fileInput').click()">
            <div class="w-[38px] h-[38px] text-black">
                <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
            </div>
            <div>
                <p class="text-[16px] font-bold text-black">File Picker</p>
                <p class="text-[11.8px] text-[#929292] font-medium">PDF, PPTX, DOCX</p>
            </div>
            <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="hidden">
                @csrf
                <input type="hidden" name="folder_id" value="{{ $folder?->id }}">
                <input type="hidden" name="type" value="file">
                <input type="file" id="fileInput" name="file" onchange="this.form.submit()">
            </form>
        </button>
    </div>
</x-bottom-sheet>

<!-- Rename Bottom Sheet -->
<div x-show="showRenameModal" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
    <div class="absolute inset-0 bg-white/70 backdrop-blur-[2px]" @click="showRenameModal = false"></div>
    <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-black rounded-t-[50px] shadow-2xl transition-transform duration-300 transform"
         :class="showRenameModal ? 'translate-y-0' : 'translate-y-full'">
        <div class="p-8 pb-12">
            <div class="w-[102px] h-[6px] bg-[#d9d9d9] rounded-full mx-auto mb-8"></div>
            <h2 class="text-[22.5px] font-bold text-black text-center mb-8 leading-tight">Rename a File</h2>
            
            <form :action="'/files/' + activeFile.id" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                <div class="relative mt-4">
                    <label class="absolute -top-2 left-4 bg-white px-1 text-[10.5px] text-[#787878] font-medium uppercase tracking-wider">Name</label>
                    <input type="text" name="name" x-model="activeFile.name" 
                        class="w-full px-4 py-3 border-[0.5px] border-black rounded-[8px] focus:outline-none focus:ring-1 focus:ring-black text-[16px] font-medium">
                </div>
                
                <div class="flex space-x-3">
                    <button type="button" @click="showRenameModal = false" 
                        class="flex-1 border border-black py-3 rounded-full font-bold text-[16.4px] text-black">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="flex-1 bg-[#072ac6] text-white py-3 rounded-full font-bold text-[16.4px]">
                        Rename
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Share Bottom Sheet -->
<div x-show="showShareModal" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
    <div class="absolute inset-0 bg-white/70 backdrop-blur-[2px]" @click="showShareModal = false"></div>
    <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-black rounded-t-[50px] shadow-2xl transition-transform duration-300 transform"
         :class="showShareModal ? 'translate-y-0' : 'translate-y-full'">
        <div class="p-8 pb-12">
            <div class="w-[102px] h-[6px] bg-[#d9d9d9] rounded-full mx-auto mb-8"></div>
            <h2 class="text-[22.5px] font-bold text-black text-center mb-1 leading-tight">Share a File</h2>
            <p class="text-[13.1px] text-black text-center mb-8" x-text="sharingFile.name + ' | {{ $folder->code ?? '' }}'"></p>
            
            <div class="space-y-6">
                <!-- Share with KlasMate Button -->
                <button class="w-full flex items-center justify-between group" @click="showShareToFriends = true; showShareModal = false; shareSearch = ''; searchShareUsers()">
                    <div class="flex items-center space-x-4">
                        <div class="w-[30px] h-[30px] text-black">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m8-10a4 4 0 100-8 4 4 0 000 8zm8 7v2m0 0v2m0-2h2m-2 0h-2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-[16px] font-bold text-black">Share with a KlasMate</p>
                            <p class="text-[11.8px] text-[#929292] font-medium">Send to people in your friends list</p>
                        </div>
                    </div>
                    <div class="rotate-180">
                        <svg class="h-[24px] w-[15px] text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg>
                    </div>
                </button>

                <div class="pt-6 border-t border-[#d9d9d9]">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-[30px] h-[30px] text-black">
                            <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                        </div>
                        <p class="text-[16px] font-bold text-black">Share via URL</p>
                    </div>
                    <div class="relative">
                        <p class="absolute -top-2 left-4 bg-white px-1 text-[10.5px] text-[#787878] font-medium uppercase tracking-wider">Link</p>
                        <input type="text" readonly :value="sharingFile.link" 
                            class="w-full border-[0.5px] border-black rounded-[8px] py-3 pl-4 pr-12 text-[12px] font-medium text-black focus:ring-0">
                        <button @click="copyShareLink()" class="absolute right-3 top-1/2 -translate-y-1/2 text-black hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H10c-1.103 0-2 .897-2 2v4H4c-1.103 0-2 .897-2 2v10c0 1.103.897 2 2 2h10c1.103 0 2-.897 2-2v-4h4c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2zM4 20V10h10l.002 10H4zm16-6h-4v-4c0-1.103-.897-2-2-2h-4V4h10v10z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share to Friends Bottom Sheet -->
<div x-show="showShareToFriends" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
    <div class="absolute inset-0 bg-white/70 backdrop-blur-[2px]" @click="showShareToFriends = false"></div>
    <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-black rounded-t-[50px] shadow-2xl transition-transform duration-300 transform"
         :class="showShareToFriends ? 'translate-y-0' : 'translate-y-full'">
        <div class="p-8 pb-12">
            <div class="w-[102px] h-[6px] bg-[#d9d9d9] rounded-full mx-auto mb-8"></div>
            <h2 class="text-[22.5px] font-bold text-black text-center mb-8 leading-tight">Share the file to</h2>
            
            <div class="relative mb-6">
                <input type="text" placeholder="Search friends..." x-model="shareSearch" @input.debounce.300ms="searchShareUsers()"
                    class="w-full px-4 py-2 border border-black rounded-full focus:outline-none text-[12px] font-medium">
            </div>

            <div class="space-y-4 max-h-[50vh] overflow-y-auto no-scrollbar">
                <template x-for="user in shareUsers" :key="user.id">
                    <div class="flex items-center justify-between border-b border-[#f0f0f0] pb-4">
                        <span class="text-[16px] font-bold text-black" x-text="user.name"></span>
                        
                        <button x-show="!sharedUsers.includes(user.id)" 
                                class="bg-[#072ac6] text-white px-6 py-1.5 rounded-full text-[11.7px] font-medium active:scale-95 transition-all shadow-sm"
                                @click="sendToFile(user)">
                            Send
                        </button>
                        
                        <div x-show="sharedUsers.includes(user.id)" 
                             class="bg-[#f5c32f] w-[75px] h-[26px] rounded-[16px] flex items-center justify-center">
                            <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        </div>
                    </div>
                </template>
                <template x-if="shareUsers.length === 0">
                    <p class="text-center text-[#929292] py-10 font-medium">Search for friends to share with</p>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
