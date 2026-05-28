@extends('layouts.app')

@section('content')
<div x-data="{ step: 1, email: '', otp: ['', '', '', ''], password: '', password_confirmation: '', loading: false }" class="w-full min-h-screen bg-white p-8 flex flex-col">
    <!-- Navigation -->
    <div class="mb-10">
        <a href="/login" class="flex items-center space-x-2 text-[#072ac6] text-[13.72px] font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Go Back</span>
        </a>
    </div>

    <!-- Step 1: Recover Password -->
    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" class="flex flex-col">
        <h1 class="text-[28px] font-bold text-[#072ac6] mb-3 tracking-tight">Password Recovery</h1>
        <p class="text-[#072ac6] text-[11px] mb-8 font-normal">Enter your email and we’ll send you a reset link.</p>

        <div class="relative mb-8">
            <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
            </span>
            <input type="email" x-model="email" placeholder="Email" 
                class="w-full pl-12 pr-4 py-2.5 border border-[#072ac6] rounded-full focus:outline-none focus:ring-2 focus:ring-[#072ac6]/20 transition-all text-[#072ac6] placeholder-[#072ac6]/50 text-[13.72px]">
        </div>

        <button @click="step = 2" class="w-full bg-[#f5c32f] text-[#072ac6] py-2.5 rounded-full font-medium text-[13.72px] hover:bg-[#e6b62c] transition-all">
            Recover Password
        </button>
    </div>

    <!-- Step 2: Check your email (OTP) -->
    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" class="flex flex-col" x-cloak>
        <h1 class="text-[28px] font-bold text-[#072ac6] mb-3 tracking-tight">Check your email</h1>
        <p class="text-[#072ac6] text-[11px] mb-8 font-normal">We've sent the code to your email</p>

        <div class="flex justify-between space-x-2 mb-6">
            <template x-for="(i, index) in otp" :key="index">
                <input type="text" maxlength="1" x-model="otp[index]" 
                    class="w-14 h-14 border border-[#072ac6] rounded-2xl text-center text-[24px] font-bold text-[#072ac6] focus:outline-none focus:ring-2 focus:ring-[#072ac6]/20 transition-all">
            </template>
        </div>

        <p class="text-center text-[11px] text-[#072ac6] mb-8 font-medium">Code expires in: <span class="font-bold">03:12</span></p>

        <div class="space-y-4">
            <button @click="step = 3" class="w-full bg-[#f5c32f] text-[#072ac6] py-2.5 rounded-full font-medium text-[13.72px] hover:bg-[#e6b62c] transition-all">
                Verify
            </button>
            <button class="w-full bg-white text-[#072ac6]/60 py-2.5 rounded-full font-medium text-[13.72px] border border-[#072ac6]/20 hover:bg-[#072ac6]/5 transition-all">
                Send again
            </button>
        </div>
    </div>

    <!-- Step 3: Reset Password -->
    <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" class="flex flex-col" x-cloak>
        <h1 class="text-[28px] font-bold text-[#072ac6] mb-3 tracking-tight">Reset your password</h1>
        <p class="text-[#072ac6] text-[11px] mb-8 font-normal">Please enter your new password</p>

        <div class="space-y-4 mb-8">
            <div class="relative">
                <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 17a2 2 0 002-2 2 2 0 00-2-2 2 2 0 00-2 2 2 2 0 002 2zm6-9h-1V6a5 5 0 00-10 0v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 016 0v2H9V6z"/>
                    </svg>
                </span>
                <input type="password" x-model="password" placeholder="New Password" 
                    class="w-full pl-12 pr-4 py-2.5 border border-[#072ac6] rounded-full focus:outline-none focus:ring-2 focus:ring-[#072ac6]/20 transition-all text-[#072ac6] placeholder-[#072ac6]/50 text-[13.72px]">
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 17a2 2 0 002-2 2 2 0 00-2-2 2 2 0 00-2 2 2 2 0 002 2zm6-9h-1V6a5 5 0 00-10 0v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 016 0v2H9V6z"/>
                    </svg>
                </span>
                <input type="password" x-model="password_confirmation" placeholder="Confirm New Password" 
                    class="w-full pl-12 pr-4 py-2.5 border border-[#072ac6] rounded-full focus:outline-none focus:ring-2 focus:ring-[#072ac6]/20 transition-all text-[#072ac6] placeholder-[#072ac6]/50 text-[13.72px]">
            </div>
        </div>

        <button @click="window.location.href='/login'" class="w-full bg-[#f5c32f] text-[#072ac6] py-2.5 rounded-full font-medium text-[13.72px] hover:bg-[#e6b62c] transition-all">
            Done
        </button>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
