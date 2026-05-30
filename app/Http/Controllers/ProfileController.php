<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'school' => 'nullable|string|max:255',
            'program' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $disk = config('filesystems.default');
            
            // Store at root for clean URLs in Supabase
            $path = $file->store('', $disk);
            $filename = basename($path);
            
            // Supabase Public URL logic
            $projectRef = 'stcuxchsqfeaejpjsfkw'; 
            $bucket = 'reviewers';
            $user->avatar = "https://{$projectRef}.supabase.co/storage/v1/object/public/{$bucket}/{$filename}";
        }

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('school')) $user->school = $request->school;
        if ($request->has('program')) $user->program = $request->program;

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }
}
