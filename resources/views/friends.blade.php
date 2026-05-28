@extends('layouts.app')

@section('content')
<div class="p-6 pb-24" x-data="{ 
    search: '',
    users: [],
    loading: false,

    async searchUsers() {
        if (this.search.length < 2) {
            this.users = [];
            return;
        }
        this.loading = true;
        try {
            const response = await fetch(`/api/users/search?q=${this.search}`);
            this.users = await response.json();
        } catch (e) {
            console.error(e);
        }
        this.loading = false;
    }
}">
    <h1 class="text-[31px] font-bold text-black leading-tight mt-4 mb-6">Friends</h1>

    <!-- Search Bar -->
    <div class="relative mb-6">
        <span class="absolute inset-y-0 left-4 flex items-center text-[#787878]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <input type="text" placeholder="Search KlasMate" x-model="search" @input.debounce.500ms="searchUsers"
            class="w-full pl-12 pr-4 py-2 border border-[#787878] rounded-full focus:outline-none focus:ring-1 focus:ring-black text-[10.3px] font-medium text-black placeholder-black/50">
    </div>

    <!-- Results / Friends List -->
    <div class="space-y-4">
        <template x-if="search.length >= 2">
            <div>
                <h2 class="text-[12px] font-bold text-[#787878] mb-3 uppercase tracking-wider">Search Results</h2>
                <div class="space-y-3">
                    <template x-for="user in users" :key="user.id">
                        <div class="flex items-center justify-between p-3 border border-black rounded-[10px] bg-white shadow-sm">
                            <div class="flex items-center space-x-3">
                                <div class="w-[38px] h-[38px] bg-[#f5c32f] rounded-full flex items-center justify-center border border-black overflow-hidden shadow-sm">
                                    <template x-if="user.avatar">
                                        <img :src="user.avatar" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!user.avatar">
                                        <span class="text-[15px] font-bold text-black uppercase" x-text="user.name[0]"></span>
                                    </template>
                                </div>
                                <div>
                                    <p class="text-[15.4px] font-bold text-black leading-tight" x-text="user.name"></p>
                                    <p class="text-[10px] text-[#787878] font-medium" x-text="user.school || 'Unknown School'"></p>
                                </div>
                            </div>
                            <button @click="window.location.href='/profile/' + user.id" class="text-[10px] font-bold text-[#072ac6] underline underline-offset-2">add KlasMate</button>
                        </div>
                    </template>
                    <template x-if="users.length === 0 && !loading">
                        <div class="py-10 text-center border-[2px] border-dashed border-[#787878] rounded-[10px]">
                            <p class="text-[#787878] font-bold text-[15.4px]">No KlasMate found</p>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <template x-if="search.length < 2">
            <div>
                <h2 class="text-[12px] font-bold text-[#787878] mb-3 uppercase tracking-wider">My Friends</h2>
                <div class="py-20 text-center border-[2px] border-dashed border-[#787878] rounded-[10px]">
                    <p class="text-[#787878] font-bold text-[15.4px]">No Friends yet</p>
                </div>
            </div>
        </template>
    </div>
</div>

<x-navigation />
@endsection
