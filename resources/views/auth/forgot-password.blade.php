@extends('layouts.app')

@section('content')
<div x-data="forgotPassword()" class="w-full min-h-screen bg-white p-8 flex flex-col">
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

        <template x-if="error">
            <p class="text-red-500 text-[11px] mb-4 font-bold" x-text="error"></p>
        </template>

        <button @click="sendOtp" :disabled="loading" class="w-full bg-[#f5c32f] text-[#072ac6] py-2.5 rounded-full font-medium text-[13.72px] hover:bg-[#e6b62c] transition-all disabled:opacity-50">
            <span x-show="!loading">Recover Password</span>
            <span x-show="loading">Sending...</span>
        </button>
    </div>

    <!-- Step 2: Check your email (OTP) -->
    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" class="flex flex-col" x-cloak>
        <h1 class="text-[28px] font-bold text-[#072ac6] mb-3 tracking-tight">Check your email</h1>
        <p class="text-[#072ac6] text-[11px] mb-8 font-normal">We've sent the code to <span class="font-bold" x-text="email"></span></p>

        <div class="flex justify-between space-x-2 mb-6">
            <template x-for="(i, index) in otp" :key="index">
                <input type="text" maxlength="1" x-model="otp[index]" 
                    @keyup="handleOtpInput($event, index)"
                    :id="'otp-' + index"
                    class="w-14 h-14 border border-[#072ac6] rounded-2xl text-center text-[24px] font-bold text-[#072ac6] focus:outline-none focus:ring-2 focus:ring-[#072ac6]/20 transition-all">
            </template>
        </div>

        <template x-if="error">
            <p class="text-red-500 text-center text-[11px] mb-4 font-bold" x-text="error"></p>
        </template>

        <div class="space-y-4">
            <button @click="verifyOtp" :disabled="loading" class="w-full bg-[#f5c32f] text-[#072ac6] py-2.5 rounded-full font-medium text-[13.72px] hover:bg-[#e6b62c] transition-all disabled:opacity-50">
                <span x-show="!loading">Verify</span>
                <span x-show="loading">Verifying...</span>
            </button>
            <button @click="sendOtp" :disabled="loading" class="w-full bg-white text-[#072ac6]/60 py-2.5 rounded-full font-medium text-[13.72px] border border-[#072ac6]/20 hover:bg-[#072ac6]/5 transition-all">
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

        <template x-if="error">
            <p class="text-red-500 text-[11px] mb-4 font-bold" x-text="error"></p>
        </template>

        <button @click="resetPassword" :disabled="loading" class="w-full bg-[#f5c32f] text-[#072ac6] py-2.5 rounded-full font-medium text-[13.72px] hover:bg-[#e6b62c] transition-all disabled:opacity-50">
            <span x-show="!loading">Reset Password</span>
            <span x-show="loading">Resetting...</span>
        </button>
    </div>

    <!-- Step 4: Success -->
    <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" class="flex flex-col items-center justify-center text-center py-10" x-cloak>
        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
            </svg>
        </div>
        <h1 class="text-[28px] font-bold text-[#072ac6] mb-3 tracking-tight">Success!</h1>
        <p class="text-[#072ac6] text-[13px] mb-8 font-normal">Your password has been reset successfully.</p>
        
        <button @click="window.location.href='/login'" class="w-full bg-[#072ac6] text-white py-2.5 rounded-full font-medium text-[13.72px] hover:bg-blue-800 transition-all">
            Go to Login
        </button>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    function forgotPassword() {
        return {
            step: 1,
            email: '',
            otp: ['', '', '', ''],
            password: '',
            password_confirmation: '',
            loading: false,
            error: '',

            handleOtpInput(e, index) {
                if (e.target.value && index < 3) {
                    document.getElementById('otp-' + (index + 1)).focus();
                }
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    document.getElementById('otp-' + (index - 1)).focus();
                }
            },

            async sendOtp() {
                if (!this.email) {
                    this.error = 'Please enter your email.';
                    return;
                }

                this.loading = true;
                this.error = '';

                try {
                    const response = await fetch('/forgot-password/send-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ email: this.email })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.step = 2;
                    } else {
                        this.error = data.message || data.error || 'Failed to send OTP.';
                    }
                } catch (e) {
                    this.error = 'Something went wrong. Please try again.';
                } finally {
                    this.loading = false;
                }
            },

            async verifyOtp() {
                const token = this.otp.join('');
                if (token.length < 4) {
                    this.error = 'Please enter the 4-digit code.';
                    return;
                }

                this.error = '';
                this.step = 3; // Move to password entry
            },

            async resetPassword() {
                if (!this.password || this.password.length < 8) {
                    this.error = 'Password must be at least 8 characters.';
                    return;
                }

                if (this.password !== this.password_confirmation) {
                    this.error = 'Passwords do not match.';
                    return;
                }

                this.loading = true;
                this.error = '';

                try {
                    const response = await fetch('/forgot-password/verify-otp', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            email: this.email,
                            token: this.otp.join(''),
                            password: this.password,
                            password_confirmation: this.password_confirmation
                        })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        this.step = 4;
                    } else {
                        this.error = data.message || data.error || 'Failed to reset password.';
                    }
                } catch (e) {
                    this.error = 'Something went wrong. Please try again.';
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endsection
