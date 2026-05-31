@extends('layouts.app')

@section('content')
<div class="w-full h-[100dvh] flex items-center justify-center bg-[#f0f0f0] p-0 sm:p-4 md:p-8 overflow-hidden">
    <!-- The "Card" container -->
    <div class="w-full h-full max-h-screen sm:h-auto max-w-[1100px] flex flex-col md:flex-row bg-white overflow-hidden sm:rounded-[40px] md:shadow-2xl border border-black/5">
        
        <!-- Left Side: Branding (Desktop Only) -->
        <div class="hidden md:flex flex-1 bg-[#fcf0cf] flex-col items-center justify-center p-12 text-center border-r border-black/5">
            <div class="mb-8">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[280px] h-auto">
            </div>
            <p class="text-[18px] text-[#072ac6] font-bold max-w-sm leading-tight">Create your account and start organizing your academic resources today.</p>
        </div>

        <!-- Right Side: Register Form -->
        <div class="w-full md:w-[600px] p-6 sm:p-10 lg:p-16 flex flex-col justify-center bg-white h-full overflow-y-auto">
            <!-- Navigation -->
            <div class="w-full mb-3 sm:mb-6 shrink-0">
                <a href="/login" class="flex items-center space-x-2 text-[#072ac6] text-[11px] sm:text-[13px] font-bold transition-colors hover:underline">
                    <svg class="w-3.5 h-3.5 sm:w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>Go Back</span>
                </a>
            </div>

            <!-- Mobile Logo -->
            <div class="md:hidden mb-4 sm:mb-6 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-[100px] sm:w-[140px] h-auto">
            </div>

            <!-- Title -->
            <div class="shrink-0 mb-4 sm:mb-8 text-center sm:text-left">
                <h1 class="text-[24px] sm:text-[31px] font-black text-[#072ac6] mb-0.5 leading-tight tracking-tight">
                    Join us, <span class="text-[#f5c32f]">KlasMate!</span>
                </h1>
                <p class="text-[#072ac6] text-[10px] sm:text-[12px] font-bold uppercase tracking-wider">Create your account</p>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-[11px] rounded-r-xl shrink-0">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/register" method="POST" class="w-full space-y-2.5 sm:space-y-4 shrink-0">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-4">
                    <!-- Display Name -->
                    <div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </span>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Display Name" required 
                                class="w-full pl-10 sm:pl-12 pr-4 py-2.5 sm:py-3 border @error('name') border-red-500 @else border-[#072ac6]/10 @enderror rounded-full focus:outline-none text-[12px] text-[#072ac6] placeholder-[#072ac6]/40 bg-gray-50">
                        </div>
                        @error('name')
                            <p class="text-red-500 text-[10px] mt-1 ml-4 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required 
                                class="w-full pl-10 sm:pl-12 pr-4 py-2.5 sm:py-3 border @error('email') border-red-500 @else border-[#072ac6]/10 @enderror rounded-full focus:outline-none text-[12px] text-[#072ac6] placeholder-[#072ac6]/40 bg-gray-50">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-[10px] mt-1 ml-4 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-4">
                    <!-- School (Optional) -->
                    <div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM3.89 9L12 13.47l4.59-2.5v3.42L12 17.5l-4.59-2.61V10.9L3.89 9z"/></svg>
                            </span>
                            <input type="text" name="school" value="{{ old('school') }}" placeholder="School (Optional)" 
                                class="w-full pl-10 sm:pl-12 pr-4 py-2.5 sm:py-3 border @error('school') border-red-500 @else border-[#072ac6]/10 @enderror rounded-full focus:outline-none text-[12px] text-[#072ac6] placeholder-[#072ac6]/40 bg-gray-50">
                        </div>
                        @error('school')
                            <p class="text-red-500 text-[10px] mt-1 ml-4 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Program (Optional) -->
                    <div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <input type="text" name="program" value="{{ old('program') }}" placeholder="Program (Optional)" 
                                class="w-full pl-10 sm:pl-12 pr-4 py-2.5 sm:py-3 border @error('program') border-red-500 @else border-[#072ac6]/10 @enderror rounded-full focus:outline-none text-[12px] text-[#072ac6] placeholder-[#072ac6]/40 bg-gray-50">
                        </div>
                        @error('program')
                            <p class="text-red-500 text-[10px] mt-1 ml-4 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-4">
                    <!-- Password -->
                    <div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17a2 2 0 002-2 2 2 0 00-2-2 2 2 0 00-2 2 2 2 0 002 2zm6-9h-1V6a5 5 0 00-10 0v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 016 0v2H9V6z"/></svg>
                            </span>
                            <input type="password" name="password" id="password" placeholder="Password" required 
                                class="w-full pl-10 sm:pl-12 pr-10 sm:pr-12 py-2.5 sm:py-3 border @error('password') border-red-500 @else border-[#072ac6]/10 @enderror rounded-full focus:outline-none text-[12px] text-[#072ac6] placeholder-[#072ac6]/40 bg-gray-50">
                            <button type="button" onclick="toggleVisibility('password')" class="absolute inset-y-0 right-4 flex items-center text-[#072ac6]">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-[10px] mt-1 ml-4 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Re-enter Password -->
                    <div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-4 flex items-center text-[#072ac6]">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17a2 2 0 002-2 2 2 0 00-2-2 2 2 0 00-2 2 2 2 0 002 2zm6-9h-1V6a5 5 0 00-10 0v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2zM9 6a3 3 0 016 0v2H9V6z"/></svg>
                            </span>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required 
                                class="w-full pl-10 sm:pl-12 pr-10 sm:pr-12 py-2.5 sm:py-3 border border-[#072ac6]/10 rounded-full focus:outline-none text-[12px] text-[#072ac6] placeholder-[#072ac6]/40 bg-gray-50">
                            <button type="button" onclick="toggleVisibility('password_confirmation')" class="absolute inset-y-0 right-4 flex items-center text-[#072ac6]">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#072ac6] text-white py-3 rounded-full font-black text-[15px] sm:text-[16px] hover:bg-[#0624a8] transition-all shadow-lg active:scale-95 mt-2">
                    Create Account
                </button>
            </form>

            <!-- Divider -->
            <div class="relative w-full my-4 sm:my-6 flex items-center justify-center shrink-0">
                <div class="absolute inset-x-0 h-[1.5px] bg-gray-100"></div>
                <span class="relative bg-white px-4 text-[#072ac6]/40 text-[10px] font-black uppercase tracking-widest">or</span>
            </div>

            <!-- Google Login -->
            <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center space-x-3 bg-[#fcf0cf]/50 py-2.5 sm:py-3.5 rounded-full font-bold text-[#072ac6] text-[12px] sm:text-[13px] hover:bg-[#fcf0cf] border-2 border-[#072ac6]/10 transition-all active:scale-95 shadow-sm shrink-0 pb-4">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-[16px] sm:w-[18px] h-[16px] sm:h-[18px]">
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
