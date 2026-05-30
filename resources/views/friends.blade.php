@extends('layouts.app')

@section('content')
<div class="p-6 pb-24" x-data="{ 
    search: '',
    allUsers: @js($users),
    
    get filteredUsers() {
        if (!this.search) return this.allUsers;
        const term = this.search.toLowerCase();
        return this.allUsers.filter(u => 
            u.name.toLowerCase().includes(term) || 
            u.email.toLowerCase().includes(term)
        );
    }
}">
    <!-- Header -->
    <h1 class="text-[31px] font-bold text-black leading-tight mt-4 mb-6">Friends</h1>

    <!-- Search Bar -->
    <div class="relative mb-6">
        <span class="absolute inset-y-0 left-4 flex items-center text-[#787878]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <input type="text" placeholder="Search KlasMate" x-model="search"
            class="w-full pl-12 pr-4 py-3 border border-black rounded-full focus:outline-none focus:ring-1 focus:ring-black text-[14px] font-medium text-black placeholder-black/50 shadow-sm">
    </div>

    <!-- Friends List -->
    <div class="space-y-4">
        <div>
            <h2 class="text-[12px] font-bold text-[#787878] mb-4 uppercase tracking-widest" 
                x-text="search ? 'Search Results' : 'All KlasMates'"></h2>
            
            <div class="space-y-3">
                <template x-for="user in filteredUsers" :key="user.id">
                    <div class="flex items-center justify-between p-4 border border-black rounded-[15px] bg-white shadow-sm active:scale-[0.98] transition-transform">
                        <div class="flex items-center space-x-4">
                            <!-- Avatar -->
                            <div class="w-[45px] h-[45px] bg-[#f5c32f] rounded-full flex items-center justify-center border border-black overflow-hidden shadow-sm flex-shrink-0">
                                <template x-if="user.avatar">
                                    <img :src="user.avatar" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!user.avatar">
                                    <span class="text-[18px] font-bold text-black uppercase" x-text="user.name.charAt(0)"></span>
                                </template>
                            </div>
                            <!-- Name/School -->
                            <div>
                                <p class="text-[16px] font-bold text-black leading-tight" x-text="user.name"></p>
                                <p class="text-[11px] text-[#787878] font-medium mt-0.5">KlasMate User</p>
                            </div>
                        </div>
                        <!-- Action -->
                        <button @click="window.location.href='/profile/' + user.id" 
                                class="text-[11px] font-bold text-[#072ac6] underline underline-offset-4 decoration-2">
                            View Profile
                        </button>
                    </div>
                </template>
                
                <!-- Empty State -->
                <template x-if="filteredUsers.length === 0">
                    <div class="py-16 text-center border-2 border-dashed border-[#d9d9d9] rounded-[20px] bg-gray-50/50">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                        </div>
                        <p class="text-[#787878] font-bold text-[16px]">No KlasMate found</p>
                        <p class="text-[#929292] text-[12px] mt-1">Try a different name or email</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<x-navigation />

<style>
    /* Prevent layout shift during Alpine load */
    [x-cloak] { display: none !important; }
</style>
@endsection
