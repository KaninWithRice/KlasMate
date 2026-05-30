@extends('layouts.app')

@section('content')
<div class="p-8 pb-24" x-data='{ 
    openDropdown: null, 
    showModal: false, 
    showDeleteModal: false,
    showShareModal: false,
    showShareToFriends: false,
    showCustomAlert: false,
    alertMessage: "",
    isEdit: false,
    search: "",
    shareSearch: "",
    friends: @json($friends),
    sharedUsers: [],
    searchResults: [],
    sharingFolder: { id: "", name: "", link: "" },
    modalData: { id: "", name: "", code: "", semester: "", color: "bg-[#f5c32f]", is_public: 1 },
    colors: ["bg-[#f5c32f]", "bg-[#072ac6]", "bg-[#07a954]", "bg-[#f50220]", "bg-[#ff5aa9]", "bg-[#af78d3]", "bg-[#000000]", "bg-[#ffffff]"],
    allFolders: {{ $allFolders->toJson() }},
    currentUserId: {{ auth()->id() }},

    triggerAlert(msg) {
        this.alertMessage = msg;
        this.showCustomAlert = true;
        setTimeout(() => this.showCustomAlert = false, 4000);
    },

    async searchCourses() {
        if (!this.search) {
            this.searchResults = [];
            return;
        }
        try {
            const response = await fetch("/courses/search?q=" + encodeURIComponent(this.search));
            const data = await response.json();
            this.searchResults = data.results;
        } catch (e) {
            console.error(e);
        }
    },

    get displayedFolders() {
        if (this.search) {
            return this.searchResults;
        }
        // Local filtering for own courses
        return this.allFolders.slice(0, 6);
    },

    get filteredShareUsers() {
        if (!this.shareSearch) return this.friends;
        return this.friends.filter(u => u.name.toLowerCase().includes(this.shareSearch.toLowerCase()));
    },

    openShare(folder) {
        this.sharingFolder = {
            id: folder.id,
            name: folder.name,
            link: window.location.origin + "/repository/" + folder.id + (folder.invite_token ? "?token=" + folder.invite_token : "")
        };
        this.showShareModal = true;
        this.openDropdown = null;
    },

    copyShareLink() {
        navigator.clipboard.writeText(this.sharingFolder.link).then(() => {
            this.triggerAlert("Link copied!");
        });
    },

    openAddModal() {
        this.isEdit = false;
        this.modalData = { id: "", name: "", code: "", semester: "", color: "bg-[#f5c32f]", is_public: 1 };
        this.showModal = true;
    },
    openEditModal(folder) {
        this.isEdit = true;
        this.modalData = { 
            id: folder.id, 
            name: folder.name, 
            code: folder.code || "", 
            semester: folder.semester || "", 
            color: folder.color || "bg-[#f5c32f]",
            is_public: folder.is_public ? 1 : 0
        };
        this.showModal = true;
        this.openDropdown = null;
    },
    confirmDelete(folder) {
        this.modalData = { id: folder.id, name: folder.name };
        this.showDeleteModal = true;
        this.openDropdown = null;
    },
    copyInviteLink(folder) {
        const link = window.location.origin + "/repository/" + folder.id + "?token=" + folder.invite_token;
        navigator.clipboard.writeText(link).then(() => {
            this.triggerAlert("Invite link copied to clipboard!");
        });
        this.openDropdown = null;
    },

    async sendToChat(user) {
        try {
            const response = await fetch("{{ route("share.send") }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content,
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    receiver_id: user.id,
                    type: "folder",
                    item_id: this.sharingFolder.id
                })
            });
            if (response.ok) {
                this.sharedUsers.push(user.id);
                this.triggerAlert("Course shared with " + user.name + " via chat!");
            }
        } catch (e) {
            console.error(e);
        }
    }
}'>
    <!-- Custom Yellow Alert UI -->
    <template x-if="showCustomAlert">
        <div class="fixed top-20 left-1/2 -translate-x-1/2 w-[340px] bg-[#f5c32f] border-[1.5px] border-black rounded-[15px] p-8 shadow-2xl z-[200] flex flex-col items-center text-center animate-fade-in-down" x-cloak>
            <button @click="showCustomAlert = false" class="absolute top-4 right-4 text-black hover:opacity-70">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div class="w-24 h-16 mb-6">
                <img src="{{ asset('images/message-popup.png') }}" class="w-full h-full object-contain">
            </div>
            <h2 class="font-black text-[22px] text-black leading-tight tracking-tight" x-text="alertMessage"></h2>
        </div>
    </template>

    <!-- Header -->
    <div class="flex items-center justify-between mb-10">
        <div class="flex items-center space-x-4">
            <div class="w-[50px] h-[50px] bg-[#f5c32f] rounded-full flex items-center justify-center border border-black overflow-hidden shadow-sm">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" class="w-full h-full object-cover">
                @else
                    <span class="text-[20px] font-bold text-black uppercase">{{ substr(auth()->user()->name, 0, 1) }}</span>
                @endif
            </div>
            <div>
                <p class="text-[13.6px] text-black">Hello,</p>
                <p class="text-[19.6px] font-bold text-black leading-tight">{{ auth()->user()->name }}</p>
            </div>
        </div>
        <img src="{{ asset('images/mascot.png') }}" class="w-[60px] h-auto" onerror="this.style.display='none'">
    </div>

    <!-- Search Bar -->
    <div class="relative mb-10">
        <span class="absolute inset-y-0 left-4 flex items-center text-[#787878]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <input type="text" placeholder="Search Course Name or Code" x-model="search" @input.debounce.300ms="searchCourses"
            class="w-full pl-12 pr-4 py-2.5 border border-black rounded-full focus:outline-none focus:ring-1 focus:ring-black text-[12px] font-medium text-black placeholder-black/50 shadow-sm">
    </div>

    <!-- All Courses Title -->
    <h1 class="text-[31px] font-bold text-black mb-6" x-text="search ? 'Search Results' : 'Courses'"></h1>

    <!-- Courses Grid -->
    <div class="grid grid-cols-2 gap-4">
        <template x-for="folder in displayedFolders" :key="folder.id">
            <div class="relative border border-black rounded-[10px] h-[119px] p-3 flex flex-col justify-between shadow-sm group cursor-pointer" 
                 :class="folder.color || 'bg-[#f5c32f]'"
                 @click="folder.has_access ? window.location.href='/repository/' + folder.id : triggerAlert('This course is private. You need an invite from the owner to join.')">
                
                <div class="flex justify-between items-start">
                    <div class="flex flex-col">
                        <p class="text-[15.4px] font-bold leading-tight" 
                           :class="in_array(folder.color, ['bg-[#072ac6]', 'bg-[#07a954]', 'bg-[#f50220]', 'bg-[#000000]']) ? 'text-white' : 'text-black'"
                           x-text="folder.name"></p>
                        
                        <!-- Show Private label ONLY if is_public is false/0 -->
                        <template x-if="folder.is_public == false || folder.is_public == 0">
                            <div class="mt-1 flex items-center space-x-1">
                                <svg class="w-3 h-3 opacity-60" :class="in_array(folder.color, ['bg-[#072ac6]', 'bg-[#07a954]', 'bg-[#f50220]', 'bg-[#000000]']) ? 'text-white' : 'text-black'" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 17a2 2 0 0 0 2-2 2 2 0 0 0-2-2 2 2 0 0 0-2 2 2 2 0 0 0 2 2m6-9h-1V6a5 5 0 0 0-10 0v2H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V10a2 2 0 0 0-2-2m-6-8a3 3 0 0 1 3 3v2H9V5a3 3 0 1 3-3z"/>
                                </svg>
                                <span class="text-[8px] opacity-60 font-bold uppercase" :class="in_array(folder.color, ['bg-[#072ac6]', 'bg-[#07a954]', 'bg-[#f50220]', 'bg-[#000000]']) ? 'text-white' : 'text-black'" x-text="folder.has_access ? 'Private' : 'Invite Only'"></span>
                            </div>
                        </template>
                    </div>
                    
                    <!-- 3-Dot Menu Trigger (Only show if owner) -->
                    <template x-if="folder.user_id === currentUserId">
                        <button class="relative z-10 p-1 -mr-1 hover:bg-black/10 rounded-full transition-colors" 
                                @click.stop="openDropdown = (openDropdown === folder.id ? null : folder.id)">
                            <svg class="w-4 h-4" :class="in_array(folder.color, ['bg-[#072ac6]', 'bg-[#07a954]', 'bg-[#f50220]', 'bg-[#000000]']) ? 'text-white' : 'text-black'" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                        </button>
                    </template>

                    <!-- Dropdown Menu -->
                    <div x-show="openDropdown === folder.id" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         @click.away="openDropdown = null"
                         x-cloak
                         class="absolute right-0 top-8 z-30 w-[140px] bg-white border-[0.5px] border-[#787878] rounded-[5px] shadow-lg py-1">
                        
                        <button class="w-full text-left px-3 py-2 text-[12px] flex items-center space-x-2 hover:bg-gray-100 text-black" @click.stop="openShare(folder)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            <span>Share</span>
                        </button>
                        
                        <button class="w-full text-left px-3 py-2 text-[12px] flex items-center space-x-2 text-red-600 hover:bg-gray-100" 
                                @click.stop="confirmDelete(folder)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            <span>Delete</span>
                        </button>
                        
                        <button class="w-full text-left px-3 py-2 text-[12px] flex items-center space-x-2 hover:bg-gray-100 text-black" 
                                @click.stop="openEditModal(folder)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            <span>Edit</span>
                        </button>
                    </div>
                </div>

                <div class="flex justify-end">
                    <template x-if="!folder.is_public">
                        <svg class="w-6 h-6 opacity-80" :class="in_array(folder.color, ['bg-[#072ac6]', 'bg-[#07a954]', 'bg-[#f50220]', 'bg-[#000000]']) ? 'text-white' : 'text-black'" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                        </svg>
                    </template>
                    <template x-if="folder.is_public">
                        <svg class="w-6 h-6 opacity-80" :class="in_array(folder.color, ['bg-[#072ac6]', 'bg-[#07a954]', 'bg-[#f50220]', 'bg-[#000000]']) ? 'text-white' : 'text-black'" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
                        </svg>
                    </template>
                </div>
            </div>
        </template>

        <!-- Add Course Card -->
        <div x-show="!search" class="border border-black border-dashed rounded-[10px] h-[119px] flex flex-col items-center justify-center space-y-2 cursor-pointer hover:bg-black/5 transition-colors"
             @click="openAddModal()">
            <svg class="w-8 h-8 text-[#787878]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M12 5v14M5 12h14" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <p class="text-[15.4px] text-[#787878]">Add a course</p>
        </div>

        <!-- No Results -->
        <template x-if="search && displayedFolders.length === 0">
            <div class="col-span-2 py-10 text-center border-2 border-dashed border-[#787878] rounded-[10px]">
                <p class="text-[#787878] font-bold text-[15.4px]">No courses found matching "<span x-text="search"></span>"</p>
            </div>
        </template>
    </div>

    <!-- Add/Edit Course Modal -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-white/70 backdrop-blur-[2px]"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-cloak>
        
        <div class="bg-white border border-black rounded-[10px] w-full max-w-[326px] p-6 shadow-2xl" @click.away="showModal = false">
            <h2 class="text-[31px] font-bold text-black mb-6 leading-tight" x-text="isEdit ? 'Edit Course' : 'Add Course'"></h2>
            
            <form :action="isEdit ? '/folders/' + modalData.id : '{{ route('folders.store') }}'" method="POST" class="space-y-4">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                
                <div>
                    <label class="block text-[10.3px] font-bold text-black mb-1 uppercase tracking-wider">Course Name</label>
                    <input type="text" name="name" x-model="modalData.name" placeholder="e.g. Software Design" required 
                        class="w-full px-4 py-2 border border-black rounded-full focus:outline-none focus:ring-1 focus:ring-black text-[10.3px] font-medium placeholder:text-[#787878]">
                </div>

                <div>
                    <label class="block text-[10.3px] font-bold text-black mb-1 uppercase tracking-wider">Course Code</label>
                    <input type="text" name="code" x-model="modalData.code" placeholder="e.g. CMPE 406" 
                        class="w-full px-4 py-2 border border-black rounded-full focus:outline-none focus:ring-1 focus:ring-black text-[10.3px] font-medium placeholder:text-[#787878]">
                </div>

                <div>
                    <label class="block text-[10.3px] font-bold text-black mb-1 uppercase tracking-wider">Semester/Term</label>
                    <input type="text" name="semester" x-model="modalData.semester" placeholder="e.g. Second Sem S.Y. 2025-2026" 
                        class="w-full px-4 py-2 border border-black rounded-full focus:outline-none focus:ring-1 focus:ring-black text-[10.3px] font-medium placeholder:text-[#787878]">
                </div>

                <!-- Color Picker -->
                <div>
                    <label class="block text-[10.3px] font-bold text-black mb-2 uppercase tracking-wider">Folder Color</label>
                    <div class="grid grid-cols-4 gap-3">
                        <template x-for="color in colors">
                            <div class="w-full aspect-square rounded-full border border-black cursor-pointer transition-transform hover:scale-110 relative"
                                 :class="color"
                                 @click="modalData.color = color">
                                <div x-show="modalData.color === color" class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-4 h-4" :class="in_array(color, ['bg-[#072ac6]', 'bg-[#f50220]', 'bg-[#000000]']) ? 'text-white' : 'text-black'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </template>
                    </div>
                    <input type="hidden" name="color" :value="modalData.color">
                </div>

                <!-- Visibility Toggle -->
                <div>
                    <label class="block text-[10.3px] font-bold text-black mb-3 uppercase tracking-wider">Visibility</label>
                    <div class="flex space-x-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="is_public" value="1" x-model="modalData.is_public" class="w-4 h-4 text-[#072ac6] border-black focus:ring-0">
                            <span class="text-[12px] font-bold text-black">Public</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="is_public" value="0" x-model="modalData.is_public" class="w-4 h-4 text-[#072ac6] border-black focus:ring-0">
                            <span class="text-[12px] font-bold text-black">Private</span>
                        </label>
                    </div>
                    <p class="text-[9px] text-[#787878] mt-2 italic" x-show="modalData.is_public == 0">
                        * Private courses can only be accessed via a unique invite link.
                    </p>
                </div>
                
                <div class="flex space-x-3 pt-4">
                    <button type="button" @click="showModal = false" 
                        class="flex-1 border-[1.5px] border-black py-2 rounded-full font-bold text-[10.2px] hover:bg-black/5 transition-all text-[#072ac6]">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="flex-1 bg-[#f5c32f] text-[#072ac6] py-2 rounded-full font-bold text-[10.2px] border-[1.5px] border-black shadow-sm hover:bg-[#e6b62c] transition-all"
                        x-text="isEdit ? 'Save Changes' : 'Add Course'">
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-white/70 backdrop-blur-[2px]" x-cloak>
        <div class="bg-white border border-black rounded-[10px] w-full max-w-[326px] p-6 shadow-2xl text-center">
            <h2 class="text-[31px] font-bold text-black mb-2 leading-tight">Delete Course</h2>
            <p class="text-[14px] text-[#787878] mb-8" x-text="modalData.name"></p>
            <p class="text-[16.4px] font-bold text-black mb-10">Are you sure you want to delete?</p>
            <div class="flex space-x-3">
                <button type="button" @click="showDeleteModal = false" class="flex-1 border-[1.5px] border-black py-2 rounded-full font-bold text-[14px]">Cancel</button>
                <form :action="'/folders/' + modalData.id" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-[#f50220] text-white py-2 rounded-full font-bold text-[14px]">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Share Bottom Sheet -->
    <div x-show="showShareModal" class="fixed inset-0 z-[100] overflow-hidden" x-cloak x-transition>
        <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="showShareModal = false"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-black rounded-t-[50px] shadow-2xl p-8 pb-12 transition-transform duration-300 transform"
             :class="showShareModal ? 'translate-y-0' : 'translate-y-full'">
            <div class="w-[102px] h-[6px] bg-[#d9d9d9] rounded-full mx-auto mb-8"></div>
            <h2 class="text-[22.5px] font-bold text-black text-center mb-1 leading-tight">Share a Course</h2>
            <p class="text-[13.1px] text-black text-center mb-8" x-text="sharingFolder.name + ' | Course'"></p>
            
            <div class="space-y-6">
                <button class="w-full flex items-center justify-between group" @click="showShareToFriends = true; showShareModal = false; shareSearch = ''">
                    <div class="flex items-center space-x-4 text-left">
                        <div class="w-[30px] h-[30px] text-black">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m8-10a4 4 0 100-8 4 4 0 000 8zm8 7v2m0 0v2m0-2h2m-2 0h-2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div>
                            <p class="text-[16px] font-bold text-black">Share with a KlasMate</p>
                            <p class="text-[11.8px] text-[#929292] font-medium">Send to friends</p>
                        </div>
                    </div>
                    <div class="rotate-180"><svg class="h-[24px] w-[15px] text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"/></svg></div>
                </button>

                <div class="pt-6 border-t border-[#d9d9d9]">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-[30px] h-[30px] text-black">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                        </div>
                        <p class="text-[16px] font-bold text-black">Share via URL</p>
                    </div>
                    <div class="relative">
                        <p class="absolute -top-2 left-4 bg-white px-1 text-[10.5px] text-[#787878] font-medium uppercase tracking-wider">Link</p>
                        <input type="text" readonly :value="sharingFolder.link" 
                            class="w-full border-[0.5px] border-black rounded-[8px] py-3 pl-4 pr-12 text-[12px] font-medium text-black focus:ring-0">
                        <button @click="copyShareLink()" class="absolute right-3 top-1/2 -translate-y-1/2 text-black hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Share to Friends Bottom Sheet -->
    <div x-show="showShareToFriends" class="fixed inset-0 z-[100] overflow-hidden" x-cloak x-transition>
        <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="showShareToFriends = false"></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-black rounded-t-[50px] shadow-2xl p-8 pb-12 transition-transform duration-300 transform"
             :class="showShareToFriends ? 'translate-y-0' : 'translate-y-full'">
            <div class="w-[102px] h-[6px] bg-[#d9d9d9] rounded-full mx-auto mb-8"></div>
            <h2 class="text-[22.5px] font-bold text-black text-center mb-8 leading-tight">Share to friends</h2>
            
            <!-- Search in Share -->
            <div class="relative mb-6">
                <input type="text" placeholder="Search friends..." x-model="shareSearch"
                    class="w-full px-4 py-2 border border-black rounded-full focus:outline-none focus:ring-1 focus:ring-black text-[12px] font-medium">
            </div>

            <div class="space-y-4 max-h-[50vh] overflow-y-auto no-scrollbar">
                <template x-for="user in filteredShareUsers" :key="user.id">
                    <div class="flex items-center justify-between border-b border-[#f0f0f0] pb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-[40px] h-[40px] bg-[#f5c32f] rounded-full flex items-center justify-center border border-black overflow-hidden shadow-sm">
                                <template x-if="user.avatar"><img :src="user.avatar" class="w-full h-full object-cover"></template>
                                <template x-if="!user.avatar"><span class="text-[14px] font-bold text-black uppercase" x-text="user.name.charAt(0)"></span></template>
                            </div>
                            <span class="text-[16px] font-bold text-black" x-text="user.name"></span>
                        </div>
                        <button x-show="!sharedUsers.includes(user.id)" 
                                class="bg-[#072ac6] text-white px-6 py-1.5 rounded-full text-[11.7px] font-medium active:scale-95 transition-all shadow-sm"
                                @click="sendToChat(user)">
                            Send
                        </button>
                        <div x-show="sharedUsers.includes(user.id)" class="bg-[#f5c32f] w-[75px] h-[26px] rounded-[16px] flex items-center justify-center">
                            <svg class="w-5 h-5 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        </div>
                    </div>
                </template>
                <template x-if="filteredShareUsers.length === 0">
                    <p class="text-center text-[#929292] py-4">No friends found</p>
                </template>
            </div>
        </div>
    </div>
</div>

<x-navigation />

<script>
    function in_array(needle, haystack) {
        return haystack && haystack.includes(needle);
    }
</script>

<style>
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translate(-50%, -20px); }
        100% { opacity: 1; transform: translate(-50%, 0); }
    }
    .animate-fade-in-down {
        animation: fade-in-down 0.3s ease-out;
    }
</style>
@endsection
