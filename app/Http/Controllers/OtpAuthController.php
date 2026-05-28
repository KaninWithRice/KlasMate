<?php

namespace App\Http\Controllers;

use App\Models\OtpToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class OtpAuthController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Generate 4-digit OTP as seen in forgotpassword.png
        $otp = rand(1000, 9999);
        
        OtpToken::create([
            'email' => $request->email,
            'token' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP via email using configured SMTP
        Mail::raw("Your Rebyu verification code is: $otp", function ($message) use ($request) {
            $message->to($request->email)->subject('Verification Code');
        });

        return response()->json(['message' => 'OTP sent successfully']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $otpToken = OtpToken::where('email', $request->email)
            ->where('token', $request->token)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpToken) {
            return response()->json(['error' => 'Invalid or expired OTP'], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        // Clean up OTP token
        OtpToken::where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
