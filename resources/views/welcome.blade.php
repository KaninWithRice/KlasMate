@extends('layouts.app')

@section('content')
<div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
    <div class="text-center mb-8">
        <!-- Yellow Folder Mascot -->
        <div class="inline-block bg-[#FFD54F] w-24 h-20 rounded-xl relative mb-4 shadow-sm">
            <div class="absolute -top-2 left-2 w-10 h-5 bg-[#FFCA28] rounded-t-lg"></div>
            <div class="flex flex-col items-center justify-center h-full pt-2">
                <div class="flex space-x-3 mb-1">
                    <div class="w-2.5 h-2.5 bg-[#2E3192] rounded-full"></div>
                    <div class="w-2.5 h-2.5 bg-[#2E3192] rounded-full"></div>
                </div>
                <div class="w-4 h-1 bg-[#2E3192] rounded-full opacity-50"></div>
            </div>
        </div>
        <h1 class="text-4xl font-extrabold text-[#1A237E] mb-2 tracking-tight">Hi, KlasMate!</h1>
    </div>

    <form action="/login" method="POST" class="space-y-4">
        @csrf
        <div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                </span>
                <input type="email" name="email" placeholder="Email" required class="w-full pl-12 pr-4 py-3.5 border-2 border-blue-100 rounded-full focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all text-gray-700 placeholder-blue-300">
            </div>
        </div>

        <div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                </span>
                <input type="password" name="password" placeholder="Password" required class="w-full pl-12 pr-4 py-3.5 border-2 border-blue-100 rounded-full focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all text-gray-700 placeholder-blue-300">
            </div>
        </div>

        <div class="text-center">
            <a href="/forgot-password" class="text-sm text-blue-600 font-bold hover:text-blue-800 transition-colors">Forgot Password?</a>
        </div>

        <button type="submit" class="w-full bg-[#1A237E] text-white py-4 rounded-full font-bold text-lg hover:bg-blue-900 transition-all shadow-lg hover:shadow-blue-200 transform hover:-translate-y-0.5 active:translate-y-0">
            Login
        </button>
    </form>

    <div class="relative my-8 text-center">
        <hr class="border-gray-100">
        <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-4 text-blue-400 text-sm font-medium">or</span>
    </div>

    <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center space-x-3 bg-[#FFF9E6] border border-[#FFECB3] py-3.5 rounded-full font-bold text-[#1A237E] hover:bg-[#FFF3CD] transition-all transform hover:-translate-y-0.5 active:translate-y-0">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-6 h-6">
        <span>Continue with Google</span>
    </a>

    <div class="text-center mt-8 text-sm text-gray-500 font-medium">
        Don't have an account? <a href="/register" class="text-[#1A237E] font-black underline decoration-2 underline-offset-4 hover:text-blue-700 transition-colors">Sign Up</a>
    </div>
</div>
@endsection
