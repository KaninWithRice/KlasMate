@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen flex flex-col md:flex-row items-center justify-center bg-[#f0f0f0] p-4 md:p-8">
    <!-- The "Card" container -->
    <div class="w-full max-w-[1100px] flex flex-col md:flex-row bg-white overflow-hidden md:rounded-[40px] md:shadow-2xl min-h-[500px] md:min-h-[600px] border border-black/5">
        
        <!-- Left Side: Branding (Desktop Only) -->
        <div class="hidden md:flex flex-1 bg-[#fcf0cf] flex-col items-center justify-center p-12 text-center border-r border-black/5">
            <div class="mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[300px] h-auto">
            </div>
            <p class="text-[18px] text-[#072ac6] font-bold max-w-sm leading-tight">Manage your academic courses and files with ease. Connect with KlasMates and share resources.</p>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-[500px] p-8 md:p-16 flex flex-col items-center justify-center bg-white">
            <!-- Mobile Logo -->
            <div class="md:hidden mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[180px] h-auto">
            </div>

            <h1 class="text-[36px] font-black text-[#072ac6] mb-10 tracking-tight text-center leading-none">Hi, KlasMate!</h1>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-[12px] rounded-r-xl w-full max-w-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/login" method="POST" class="w-full space-y-4 max-w-sm">
                @csrf
                <!-- Email Field -->
                <div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required 
                            class="w-full pl-12 pr-4 py-3.5 border-2 @error('email') border-red-500 @else border-[#072ac6]/10 @enderror rounded-full focus:outline-none focus:ring-2 focus:ring-[#072ac6]/20 transition-all text-[#072ac6] placeholder-[#072ac6]/40 text-[15px] font-medium bg-gray-50">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-[10px] mt-1 ml-4 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 17a2 2 0 002-2 2 2 0 00-2-2 2 2 0 00-2 2 2 2 0 002 2zm6-9h-1V6a5 5 0 00-10 0v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2m-6-8a3 3 0 0 1 3 3v2H9V5a3 3 0 0 1 3-3z"/>
                            </svg>
                        </span>
                        <input type="password" name="password" id="password" placeholder="Password" required 
                            class="w-full pl-12 pr-12 py-3.5 border-2 @error('password') border-red-500 @else border-[#072ac6]/10 @enderror rounded-full focus:outline-none focus:ring-2 focus:ring-[#072ac6]/20 transition-all text-[#072ac6] placeholder-[#072ac6]/40 text-[15px] font-medium bg-gray-50">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-4 flex items-center text-[#072ac6]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-[10px] mt-1 ml-4 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Forgot Password -->
                <div class="text-right">
                    <a href="/forgot-password" class="text-[12px] text-[#072ac6] font-bold underline underline-offset-4">Forgot Password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="w-full bg-[#072ac6] text-white py-3.5 rounded-full font-black text-[16px] hover:bg-[#0624a8] transition-all shadow-lg shadow-[#072ac6]/20 active:scale-95">
                    Login
                </button>
            </form>

            <!-- Divider -->
            <div class="relative w-full max-w-sm my-10 flex items-center justify-center">
                <div class="absolute inset-x-0 h-[1.5px] bg-gray-100"></div>
                <span class="relative bg-white px-4 text-[#072ac6]/40 text-[10px] font-black uppercase tracking-widest">or</span>
            </div>

            <!-- Google Login -->
            <a href="{{ route('google.login') }}" class="w-full max-w-sm flex items-center justify-center space-x-4 bg-[#fcf0cf]/50 py-3.5 rounded-full font-bold text-[#072ac6] text-[13px] hover:bg-[#fcf0cf] border-2 border-[#072ac6]/10 transition-all active:scale-95 shadow-sm">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-[18px] h-[18px]">
                <span>Continue with Google</span>
            </a>

            <!-- Sign Up Link -->
            <div class="text-center mt-12 text-[13px] text-[#072ac6] font-medium">
                Don't have an account? <a href="/register" class="font-black underline decoration-2 underline-offset-4">Sign Up</a>
            </div>
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
