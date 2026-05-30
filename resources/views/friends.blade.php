@extends('layouts.app')

@section('content')
<div class="p-6 pb-24" x-data="{ 
    search: '',
    allUsers: [],
    loading: true,

    async init() {
        try {
            const response = await fetch('/api/users/search?q=');
            this.allUsers = await response.json();
        } catch (e) {
            console.error('Failed to load users:', e);
        }
        this.loading = false;
    },

    get filteredUsers() {
        if (!this.search) {
            return this.allUsers;
        }
        const term = this.search.toLowerCase();
        return this.allUsers.filter(user => {
            return user.name.toLowerCase().includes(term) || 
                   user.email.toLowerCase().includes(term);
        }).sort((a, b) => {
            // Priority: Name starts with search term
            const aStarts = a.name.toLowerCase().startsWith(term);
            const bStarts = b.name.toLowerCase().startsWith(term);
            if (aStarts && !bStarts) return -1;
            if (!aStarts && bStarts) return 1;
            return a.name.localeCompare(b.name);
        });
    }
}" x-init="init()">
    <h1 class="text-[31px] font-bold text-black leading-tight mt-4 mb-6">Friends</h1>

    <!-- Search Bar -->
    <div class="relative mb-6">
        <span class="absolute inset-y-0 left-4 flex items-center text-[#787878]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <input type="text" placeholder="Search KlasMate" x-model="search"
            class="w-full pl-12 pr-4 py-2 border border-[#787878] rounded-full focus:outline-none focus:ring-1 focus:ring-black text-[10.3px] font-medium text-black placeholder-black/50">
    </div>

    <!-- Results / Friends List -->
    <div class="space-y-4">
        <div>
            <h2 class="text-[12px] font-bold text-[#787878] mb-3 uppercase tracking-wider" 
                x-text="loading ? 'Loading KlasMates...' : (search ? 'Search Results' : 'All KlasMates')"></h2>
            
            <div class="space-y-3">
                <template x-for="user in filteredUsers" :key="user.id">
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
                                <p class="text-[10px] text-[#787878] font-medium" x-text="user.school || 'Active KlasMate'"></p>
                            </div>
                        </div>
                        <button @click="window.location.href='/profile/' + user.id" class="text-[10px] font-bold text-[#072ac6] underline underline-offset-2">add KlasMate</button>
                    </div>
                </template>
                
                <template x-if="!loading && filteredUsers.length === 0">
                    <div class="py-10 text-center border-[2px] border-dashed border-[#787878] rounded-[10px]">
                        <p class="text-[#787878] font-bold text-[15.4px]">No matches found</p>
                    </div>
                </template>

                <template x-if="loading">
                    <div class="text-center py-10">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-b-transparent border-[#072ac6]"></div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<x-navigation />
@endsection
