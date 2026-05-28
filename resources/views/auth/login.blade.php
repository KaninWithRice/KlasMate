@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen bg-white p-8 flex flex-col items-center justify-center">
    <!-- Mascot Image -->
    <div class="mb-6">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[125px] h-auto">
    </div>

    <h1 class="text-[36px] font-bold text-[#072ac6] mb-10 tracking-tight">Hi, KlasMate!</h1>

    <form action="/login" method="POST" class="w-full space-y-4">
        @csrf
        <!-- Email Field -->
        <div class="relative">
            <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
            </span>
            <input type="email" name="email" placeholder="Email" required 
                class="w-full pl-12 pr-4 py-2.5 border border-[#072ac6] rounded-full focus:outline-none focus:ring-2 focus:ring-[#072ac6]/20 transition-all text-[#072ac6] placeholder-[#072ac6]/50 text-[14px]">
        </div>

        <!-- Password Field -->
        <div class="relative">
            <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 17a2 2 0 002-2 2 2 0 00-2-2 2 2 0 00-2 2 2 2 0 002 2zm6-9h-1V6a5 5 0 00-10 0v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 016 0v2H9V6z"/>
                </svg>
            </span>
            <input type="password" name="password" id="password" placeholder="Password" required 
                class="w-full pl-12 pr-12 py-2.5 border border-[#072ac6] rounded-full focus:outline-none focus:ring-2 focus:ring-[#072ac6]/20 transition-all text-[#072ac6] placeholder-[#072ac6]/50 text-[14px]">
            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-4 flex items-center text-[#072ac6]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>

        <!-- Forgot Password -->
        <div class="text-center">
            <a href="/forgot-password" class="text-[11px] text-[#072ac6] font-medium underline underline-offset-2">Forgot Password?</a>
        </div>

        <!-- Login Button -->
        <button type="submit" class="w-full bg-[#072ac6] text-white py-2.5 rounded-full font-medium text-[14px] hover:bg-[#0624a8] transition-all">
            Login
        </button>
    </form>

    <!-- Divider -->
    <div class="relative w-full my-8 flex items-center justify-center">
        <div class="absolute inset-x-0 h-[1px] bg-[#072ac6]/30"></div>
        <span class="relative bg-white px-2 text-[#072ac6] text-[9px] font-medium">or</span>
    </div>

    <!-- Google Login -->
    <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center space-x-3 bg-[#fcf0cf] py-2.5 rounded-full font-medium text-[#072ac6] text-[11px] hover:bg-[#fbe7b1] transition-all">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-[14px] h-[14px]">
        <span>Continue with Google</span>
    </a>

    <!-- Sign Up Link -->
    <div class="text-center mt-10 text-[11px] text-[#072ac6] font-medium">
        Don't have an account? <a href="/register" class="font-bold underline">Sign Up</a>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
    }
</script>
@endsection
