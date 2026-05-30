@extends('layouts.app')

@section('content')
<div class="p-6 pb-24" x-data="{ 
    isEditing: false,
    profileData: { 
        name: '{{ auth()->user()->name }}', 
        school: '{{ auth()->user()->school ?? '' }}', 
        program: '{{ auth()->user()->program ?? '' }}' 
    }
}">
    <h1 class="text-[31px] font-bold text-black leading-tight mt-4 mb-10">Settings</h1>

    <!-- Profile Info -->
    <div class="flex flex-col items-center mb-10">
        <div class="relative">
            <div class="w-[50px] h-[50px] bg-[#f5c32f] rounded-full flex items-center justify-center border border-black overflow-hidden shadow-sm">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" class="w-full h-full object-cover">
                @else
                    <span class="text-[20px] font-bold text-black uppercase">{{ substr(auth()->user()->name, 0, 1) }}</span>
                @endif
            </div>
            <button x-show="isEditing" @click="document.getElementById('profilePicSheet').__x.$data.open = true" 
                    class="absolute -bottom-1 -right-1 w-5 h-5 bg-white border border-black rounded-full flex items-center justify-center shadow-sm" x-cloak>
                <svg class="w-3 h-3 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </button>
        </div>
        <p class="text-[19.6px] font-bold text-black mt-3">{{ auth()->user()->name }}</p>
    </div>

    <!-- Upload Profile Pic Bottom Sheet -->
    <x-bottom-sheet id="profilePicSheet" title="Upload a display picture">
        <div class="space-y-6">
            <button class="w-full flex items-center space-x-4 group text-left" onclick="document.getElementById('avatarInput').click()">
                <div class="w-[41px] h-[41px] text-black">
                    <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div>
                    <p class="text-[16px] font-bold text-black">Camera Roll</p>
                    <p class="text-[11.8px] text-[#929292] font-medium">Upload photos from your gallery</p>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    @method('PUT')
                    <input type="file" id="avatarInput" name="avatar" onchange="this.form.submit()">
                </form>
            </button>
        </div>
    </x-bottom-sheet>

    <!-- Account Section -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-[#787878] text-[13.8px] font-bold tracking-wider uppercase">Account</h2>
        <button @click="isEditing = !isEditing" class="flex items-center space-x-1 text-[#072ac6] font-bold text-[11.2px] underline">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            <span x-text="isEditing ? 'Cancel' : 'Edit'"></span>
        </button>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" class="space-y-3 mb-10">
        @csrf
        @method('PUT')
        
        <div class="border border-black rounded-[8.6px] p-2.5 px-4 bg-white">
            <p class="text-[11.1px] font-bold text-black">Display Name</p>
            <template x-if="!isEditing">
                <p class="text-[8.6px] text-[#787878] font-bold">{{ auth()->user()->name }}</p>
            </template>
            <template x-if="isEditing">
                <input type="text" name="name" x-model="profileData.name" class="w-full text-[8.6px] text-black font-bold focus:outline-none bg-transparent">
            </template>
        </div>

        <div class="border border-black rounded-[8.6px] p-2.5 px-4 bg-gray-50 opacity-70">
            <p class="text-[11.1px] font-bold text-black">Email</p>
            <p class="text-[8.6px] text-[#787878] font-bold">{{ auth()->user()->email }}</p>
        </div>

        <div class="border border-black rounded-[8.6px] p-2.5 px-4 bg-white">
            <p class="text-[11.1px] font-bold text-black">School/University</p>
            <template x-if="!isEditing">
                <p class="text-[8.6px] text-[#787878] font-bold">{{ auth()->user()->school ?? 'Not set' }}</p>
            </template>
            <template x-if="isEditing">
                <input type="text" name="school" x-model="profileData.school" class="w-full text-[8.6px] text-black font-bold focus:outline-none bg-transparent">
            </template>
        </div>

        <div class="border border-black rounded-[8.6px] p-2.5 px-4 bg-white">
            <p class="text-[11.1px] font-bold text-black">Program</p>
            <template x-if="!isEditing">
                <p class="text-[8.6px] text-[#787878] font-bold">{{ auth()->user()->program ?? 'Not set' }}</p>
            </template>
            <template x-if="isEditing">
                <input type="text" name="program" x-model="profileData.program" class="w-full text-[8.6px] text-black font-bold focus:outline-none bg-transparent">
            </template>
        </div>

        <template x-if="isEditing">
            <button type="submit" class="w-full bg-[#072ac6] text-white py-2 rounded-full font-bold text-[13.7px] mt-2 shadow-sm">
                Save Changes
            </button>
        </template>
    </form>

    <!-- Password Section -->
    <h2 class="text-[#787878] text-[13.8px] font-bold tracking-wider mb-4 uppercase">Change Password</h2>
    <form action="{{ route('password.update') }}" method="POST" class="space-y-3">
        @csrf
        @method('PUT')
        <div class="border border-black rounded-[8.6px] p-2.5 px-4 bg-white">
            <p class="text-[11.1px] font-bold text-black uppercase">Current Password</p>
            <input type="password" name="current_password" class="w-full text-[8.6px] text-[#787878] font-bold focus:outline-none bg-transparent" placeholder="********">
        </div>
        <div class="border border-black rounded-[8.6px] p-2.5 px-4 bg-white">
            <p class="text-[11.1px] font-bold text-black uppercase">New Password</p>
            <input type="password" name="password" class="w-full text-[8.6px] text-[#787878] font-bold focus:outline-none bg-transparent" placeholder="********">
        </div>
        <div class="border border-black rounded-[8.6px] p-2.5 px-4 bg-white">
            <p class="text-[11.1px] font-bold text-black uppercase">Re-Enter Password</p>
            <input type="password" name="password_confirmation" class="w-full text-[8.6px] text-[#787878] font-bold focus:outline-none bg-transparent" placeholder="********">
        </div>

        <button type="submit" class="w-full bg-[#f5c32f] text-[#072ac6] py-2 rounded-full font-bold text-[13.7px] mt-6 hover:bg-[#e6b62c] transition-all border border-black shadow-sm">
            Change Password
        </button>
    </form>

    <!-- Logout -->
    <form action="{{ route('logout') }}" method="POST" class="mt-12 text-center">
        @csrf
        <button type="submit" class="text-[#f50220] font-bold text-[14px] hover:opacity-70 underline uppercase">
            Log Out Account
        </button>
    </form>
</div>

<x-navigation />
@endsection
