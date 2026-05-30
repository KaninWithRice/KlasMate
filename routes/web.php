<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\OtpAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// TEMPORARY MIGRATION ROUTE (Delete this after use!)
Route::get('/migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "<h1>Migration Successful!</h1><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "<h1>Migration Failed!</h1><pre>" . $e->getMessage() . "</pre>";
    }
});

// TEMPORARY OAUTH DEBUG ROUTE
Route::get('/debug-oauth', function () {
    return [
        'APP_URL' => config('app.url'),
        'GOOGLE_REDIRECT_URI' => config('services.google.redirect'),
        'SOCIALITE_REDIRECT_URL' => url('/auth/google/callback'),
        'ACTUAL_REDIRECT_URI_SENT_TO_GOOGLE' => Socialite::driver('google')->getRedirectGenerationUrl(),
    ];
});

// TEMPORARY CLEANUP ROUTE (Delete after use!)
Route::get('/cleanup-files', function () {
    try {
        $count = \App\Models\File::count();
        \App\Models\File::truncate(); // This deletes all records
        return "<h1>Cleanup Successful!</h1><p>Deleted $count file records from the database.</p>";
    } catch (\Exception $e) {
        return "<h1>Cleanup Failed!</h1><pre>" . $e->getMessage() . "</pre>";
    }
});

// TEMPORARY STORAGE TEST ROUTE
Route::get('/test-storage', function () {
    // ... existing storage test code ...
});

// TEMPORARY USER DEBUG ROUTE
Route::get('/debug-users', function () {
    $users = \App\Models\User::all();
    return [
        'count' => $users->count(),
        'current_user_id' => auth()->id(),
        'users' => $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email]),
    ];
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
    ]);

    auth()->login($user);

    return redirect('/dashboard');
});

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (auth()->attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

// Google Authentication Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// OTP Password Recovery Routes
Route::post('/forgot-password/send', [OtpAuthController::class, 'sendOtp'])->name('password.otp.send');
Route::post('/forgot-password/verify', [OtpAuthController::class, 'verifyOtp'])->name('password.otp.verify');

Route::middleware([
    'auth',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications', function() { return view('notifications'); })->name('notifications');
    Route::get('/friends', function() { return view('friends'); })->name('friends');
    Route::get('/settings', function() { return view('settings'); })->name('settings');
    Route::get('/profile/{user?}', function() { return view('profile'); })->name('profile');
    Route::put('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password/update', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    // Folder & Repository Routes
    Route::get('/repository/{folder?}', [FolderController::class, 'index'])->name('repository.index');
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::put('/folders/{folder}', [FolderController::class, 'update'])->name('folders.update');
    Route::delete('/folders/{folder}', [FolderController::class, 'destroy'])->name('folders.destroy');

    // File Routes
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
    Route::put('/files/{file}', [FileController::class, 'update'])->name('files.update');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/view', [FileController::class, 'view'])->name('files.view');
    Route::get('/files/{file}/stream', [FileController::class, 'stream'])->name('files.stream');
    Route::get('/files/{file}/force-delete', [FileController::class, 'destroy'])->name('files.force-delete');

    // Comment Routes
    Route::post('/files/{file}/comments', [CommentController::class, 'store'])->name('comments.store');

    // API Routes
    Route::get('/api/users/search', function (\Illuminate\Http\Request $request) {
        $q = trim($request->query('q', ''));
        $currentUserId = auth()->id();
        
        $query = \App\Models\User::query();
        
        // Always exclude current user
        if ($currentUserId) {
            $query->where('id', '!=', $currentUserId);
        }
        
        if ($q !== '') {
            // Very fuzzy search: match name OR email
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'ilike', "%{$q}%")
                    ->orWhere('email', 'ilike', "%{$q}%");
            });
        }
        
        $users = $query->take(20)->get();
        
        return $users;
    });
});
