@extends('layouts.app')

@section('content')
<div class="p-6 pb-24" x-data="{ 
    search: '',
    users: [],
    loading: false,

    async searchUsers() {
        if (this.search.length < 1) {
            this.users = [];
            return;
        }
        this.loading = true;
        try {
            const response = await fetch(`/friends/search?q=${encodeURIComponent(this.search)}`);
            const data = await response.json();
            this.users = data.results || [];
        } catch (e) {
            console.error(e);
        }
        this.loading = false;
    },

    async sendRequest(user) {
        try {
            const response = await fetch(`/friends/${user.id}/request`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            });
            if (response.ok) {
                user.friend_status = 'pending';
                user.is_requester = true;
                alert('Friend request sent!');
            }
        } catch (e) {
            console.error(e);
        }
    }
}">
    <!-- Header -->
    <h1 class="text-[31px] font-bold text-black leading-tight mt-4 mb-6">Friends</h1>

    <!-- Search Bar -->
    <div class="relative mb-6">
        <span class="absolute inset-y-0 left-4 flex items-center text-[#787878]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <input type="text" placeholder="Search KlasMate" x-model="search" @input.debounce.300ms="searchUsers"
            class="w-full pl-12 pr-4 py-3 border border-black rounded-full focus:outline-none focus:ring-1 focus:ring-black text-[14px] font-medium text-black placeholder-black/50 shadow-sm">
    </div>

    <!-- Friends Section -->
    <div class="space-y-6">
        <!-- Search Results -->
        <template x-if="search.length > 0">
            <div>
                <h2 class="text-[12px] font-bold text-[#787878] mb-4 uppercase tracking-widest">Search Results</h2>
                <div class="space-y-3">
                    <template x-for="user in users" :key="user.id">
                        <div class="flex items-center justify-between p-4 border border-black rounded-[15px] bg-white shadow-sm">
                            <div class="flex items-center space-x-4">
                                <div class="w-[45px] h-[45px] bg-[#f5c32f] rounded-full flex items-center justify-center border border-black overflow-hidden shadow-sm flex-shrink-0">
                                    <template x-if="user.avatar"><img :src="user.avatar" class="w-full h-full object-cover"></template>
                                    <template x-if="!user.avatar"><span class="text-[18px] font-bold text-black uppercase" x-text="user.name.charAt(0)"></span></template>
                                </div>
                                <div>
                                    <p class="text-[16px] font-bold text-black leading-tight" x-text="user.name"></p>
                                    <p class="text-[11px] text-[#787878] font-medium mt-0.5" x-text="user.friend_status === 'accepted' ? 'Already Friends' : 'KlasMate User'"></p>
                                </div>
                            </div>
                            
                            <div>
                                <template x-if="user.friend_status === 'none'">
                                    <button @click="sendRequest(user)" class="text-[11px] font-bold text-[#072ac6] underline underline-offset-4 decoration-2">Add Friend</button>
                                </template>
                                <template x-if="user.friend_status === 'pending' && user.is_requester">
                                    <span class="text-[11px] font-bold text-[#787878] italic">Requested</span>
                                </template>
                                <template x-if="user.friend_status === 'pending' && !user.is_requester">
                                    <button @click="window.location.href='/notifications'" class="text-[11px] font-bold text-[#f5c32f] underline underline-offset-4 decoration-2">Respond to Request</button>
                                </template>
                                <template x-if="user.friend_status === 'accepted'">
                                    <button @click="window.location.href='/profile/' + user.id" class="text-[11px] font-bold text-[#072ac6] underline underline-offset-4 decoration-2">View Profile</button>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="!loading && users.length === 0">
                        <p class="text-center text-[#787878] py-10">No KlasMate found</p>
                    </template>
                </div>
            </div>
        </template>

        <!-- Current Friends (Visible only when NOT searching) -->
        <template x-if="search.length === 0">
            <div>
                <h2 class="text-[12px] font-bold text-[#787878] mb-4 uppercase tracking-widest">My Friends</h2>
                <div class="space-y-3">
                    @forelse($friends as $friend)
                        <div class="flex items-center justify-between p-4 border border-black rounded-[15px] bg-white shadow-sm active:scale-[0.98] transition-transform">
                            <div class="flex items-center space-x-4">
                                <div class="w-[45px] h-[45px] bg-[#f5c32f] rounded-full flex items-center justify-center border border-black overflow-hidden shadow-sm flex-shrink-0">
                                    @if($friend->avatar)<img src="{{ $friend->avatar }}" class="w-full h-full object-cover">@else<span class="text-[18px] font-bold text-black uppercase">{{ $friend->name[0] }}</span>@endif
                                </div>
                                <div>
                                    <p class="text-[16px] font-bold text-black leading-tight">{{ $friend->name }}</p>
                                    <p class="text-[11px] text-[#787878] font-medium mt-0.5">KlasMate Friend</p>
                                </div>
                            </div>
                            <button @click="window.location.href='/profile/{{ $friend->id }}'" 
                                    class="text-[11px] font-bold text-[#072ac6] underline underline-offset-4 decoration-2">
                                View Profile
                            </button>
                        </div>
                    @empty
                        <div class="py-16 text-center border-2 border-dashed border-[#d9d9d9] rounded-[20px] bg-gray-50/50">
                            <p class="text-[#787878] font-bold text-[16px]">No Friends yet</p>
                            <p class="text-[#929292] text-[12px] mt-1">Search above to find and add your KlasMates!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </template>
    </div>
</div>

<x-navigation />
@endsection
