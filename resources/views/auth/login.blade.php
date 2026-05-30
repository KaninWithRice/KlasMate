@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen flex flex-col md:flex-row bg-white">
    <!-- Left Side: Branding (Desktop Only) -->
    <div class="hidden md:flex flex-1 bg-[#fcf0cf] flex-col items-center justify-center p-12 text-center">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[300px] h-auto mb-10">
        <h2 class="text-[64px] font-black text-[#072ac6] leading-tight mb-4 tracking-tighter">KlasMate</h2>
        <p class="text-[22px] text-[#072ac6]/80 font-medium max-w-lg">Manage your academic courses and files with ease. Connect with KlasMates and share resources.</p>
    </div>

    <!-- Right Side: Login Form -->
    <div class="w-full md:w-[550px] lg:w-[650px] p-8 md:p-16 lg:p-24 flex flex-col items-center justify-center bg-white">
        <!-- Mobile Logo -->
        <div class="md:hidden mb-12">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[200px] h-auto">
        </div>

        <h1 class="text-[42px] font-black text-[#072ac6] mb-12 tracking-tight text-center">Hi, KlasMate!</h1>

        <form action="/login" method="POST" class="w-full space-y-6 max-w-sm">
            @csrf
            <!-- Email Field -->
            <div class="relative">
                <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </span>
                <input type="email" name="email" placeholder="Email Address" required 
                    class="w-full pl-14 pr-4 py-4 border-2 border-[#072ac6] rounded-full focus:outline-none focus:ring-4 focus:ring-[#072ac6]/10 transition-all text-[#072ac6] placeholder-[#072ac6]/50 text-[16px] font-bold">
            </div>

            <!-- Password Field -->
            <div class="relative">
                <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 17a2 2 0 002-2 2 2 0 00-2-2 2 2 0 00-2 2 2 2 0 002 2zm6-9h-1V6a5 5 0 00-10 0v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 016 0v2H9V6z"/>
                    </svg>
                </span>
                <input type="password" name="password" id="password" placeholder="Password" required 
                    class="w-full pl-14 pr-14 py-4 border-2 border-[#072ac6] rounded-full focus:outline-none focus:ring-4 focus:ring-[#072ac6]/10 transition-all text-[#072ac6] placeholder-[#072ac6]/50 text-[16px] font-bold">
                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-4 flex items-center text-[#072ac6]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>

            <!-- Forgot Password -->
            <div class="text-right">
                <a href="/forgot-password" class="text-[14px] text-[#072ac6] font-bold underline underline-offset-4 decoration-2">Forgot Password?</a>
            </div>

            <!-- Login Button -->
            <button type="submit" class="w-full bg-[#072ac6] text-white py-4 rounded-full font-black text-[18px] hover:bg-[#0624a8] shadow-lg shadow-[#072ac6]/20 active:scale-95 transition-all">
                Login
            </button>
        </form>

        <!-- Divider -->
        <div class="relative w-full max-w-sm my-12 flex items-center justify-center">
            <div class="absolute inset-x-0 h-[1.5px] bg-[#072ac6]/20"></div>
            <span class="relative bg-white px-6 text-[#072ac6] text-[12px] font-black uppercase tracking-widest">or</span>
        </div>

        <!-- Google Login -->
        <a href="{{ route('google.login') }}" class="w-full max-w-sm flex items-center justify-center space-x-4 bg-[#fcf0cf] py-4 rounded-full font-bold text-[#072ac6] text-[14px] hover:bg-[#fbe7b1] border-2 border-black transition-all active:scale-95 shadow-md">
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-[20px] h-[20px]">
            <span>Continue with Google</span>
        </a>

        <!-- Sign Up Link -->
        <div class="text-center mt-12 text-[14px] text-[#072ac6] font-medium">
            Don't have an account? <a href="/register" class="font-black underline decoration-2 underline-offset-4">Sign Up</a>
        </div>
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
