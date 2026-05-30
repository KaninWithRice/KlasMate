@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen flex flex-col md:flex-row bg-white">
    <!-- Left Side: Mascot/Branding (Desktop Only) -->
    <div class="hidden md:flex flex-1 bg-[#fcf0cf] flex-col items-center justify-center p-12 text-center">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[300px] h-auto mb-8">
        <h2 class="text-[64px] font-black text-[#072ac6] leading-tight mb-4">Join <span class="text-[#f5c32f]">KlasMate!</span></h2>
        <p class="text-[22px] text-[#072ac6]/80 font-medium max-w-lg">Create your account and start organizing your academic resources today.</p>
    </div>

    <!-- Right Side: Register Form -->
    <div class="w-full md:w-[600px] lg:w-[700px] p-8 md:p-16 lg:p-24 flex flex-col justify-center">
            <!-- Navigation -->
            <div class="w-full mb-8">
                <a href="/login" class="flex items-center space-x-2 text-[#072ac6] text-[13.72px] font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Go Back</span>
                </a>
            </div>

            <!-- Mobile Logo -->
            <div class="md:hidden mb-6">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[180px] h-auto">
            </div>

            <!-- Title -->
            <h1 class="text-[31px] font-bold text-[#072ac6] mb-1 leading-tight">
                Join us, <span class="text-[#f5c32f]">KlasMate!</span>
            </h1>
            <p class="text-[#072ac6] text-[11.1px] mb-8">Create your account</p>

            <form action="/register" method="POST" class="w-full space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Display Name -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </span>
                        <input type="text" name="name" placeholder="Display Name" required 
                            class="w-full pl-12 pr-4 py-2.5 border border-[#072ac6] rounded-full focus:outline-none text-[10.3px] text-[#072ac6] placeholder-[#072ac6]/50">
                    </div>

                    <!-- Email -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        </span>
                        <input type="email" name="email" placeholder="Email" required 
                            class="w-full pl-12 pr-4 py-2.5 border border-[#072ac6] rounded-full focus:outline-none text-[10.3px] text-[#072ac6] placeholder-[#072ac6]/50">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- School (Optional) -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.89 9L12 13.47l4.59-2.5v3.42L12 17.5l-4.59-2.61V10.9L3.89 9z"/></svg>
                        </span>
                        <input type="text" name="school" placeholder="School (Optional)" 
                            class="w-full pl-12 pr-4 py-2.5 border border-[#072ac6] rounded-full focus:outline-none text-[10.3px] text-[#072ac6] placeholder-[#072ac6]/50">
                    </div>

                    <!-- Program (Optional) -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <input type="text" name="program" placeholder="Program (Optional)" 
                            class="w-full pl-12 pr-4 py-2.5 border border-[#072ac6] rounded-full focus:outline-none text-[10.3px] text-[#072ac6] placeholder-[#072ac6]/50">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17a2 2 0 002-2 2 2 0 00-2-2 2 2 0 00-2 2 2 2 0 002 2zm6-9h-1V6a5 5 0 00-10 0v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 016 0v2H9V6z"/></svg>
                        </span>
                        <input type="password" name="password" id="password" placeholder="Password" required 
                            class="w-full pl-12 pr-12 py-2.5 border border-[#072ac6] rounded-full focus:outline-none text-[10.3px] text-[#072ac6] placeholder-[#072ac6]/50">
                        <button type="button" onclick="toggleVisibility('password')" class="absolute inset-y-0 right-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </button>
                    </div>

                    <!-- Re-enter Password -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17a2 2 0 002-2 2 2 0 00-2-2 2 2 0 00-2 2 2 2 0 002 2zm6-9h-1V6a5 5 0 00-10 0v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 016 0v2H9V6z"/></svg>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required 
                            class="w-full pl-12 pr-12 py-2.5 border border-[#072ac6] rounded-full focus:outline-none text-[10.3px] text-[#072ac6] placeholder-[#072ac6]/50">
                        <button type="button" onclick="toggleVisibility('password_confirmation')" class="absolute inset-y-0 right-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#f5c32f] text-[#072ac6] py-2.5 rounded-full font-bold text-[13.7px] hover:bg-[#e6b62c] transition-all border border-black shadow-sm mt-4">
                    Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="relative w-full my-8 flex items-center justify-center">
                <div class="absolute inset-x-0 h-[1px] bg-[#072ac6]/30"></div>
                <span class="relative bg-white px-2 text-[#072ac6] text-[9px] font-medium uppercase tracking-widest">or</span>
            </div>

            <!-- Google Login -->
            <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center space-x-3 bg-[#fcf0cf] py-2.5 rounded-full font-medium text-[#072ac6] text-[11.1px] hover:bg-[#fbe7b1] transition-all">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-[14px] h-[14px]">
                <span>Continue with Google</span>
            </a>
        </div>
    </div>
</div>

<script>
    function toggleVisibility(id) {
        const el = document.getElementById(id);
        el.type = el.type === 'password' ? 'text' : 'password';
    }
</script>
@endsection
