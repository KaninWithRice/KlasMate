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

// TEMPORARY MIGRATION ROUTE
Route::get('/migrate', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        return "<h1>Migration Successful!</h1><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "<h1>Migration Failed!</h1><pre>" . $e->getMessage() . "</pre>";
    }
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

// 🚀 PUBLIC SEARCH API (Top level for maximum reliability)
Route::get('/api/users/search', function (\Illuminate\Http\Request $request) {
    $q = trim($request->query('q', ''));
    $query = \App\Models\User::query();
    
    if (auth()->check()) {
        $query->where('id', '!=', auth()->id());
    }
    
    if ($q !== '') {
        $query->where(function($sub) use ($q) {
            // Case-insensitive matching for both name and email
            $sub->where('name', 'ILIKE', "%{$q}%")
                ->orWhere('email', 'ILIKE', "%{$q}%");
        });
    }
    
    return $query->latest()->take(20)->get();
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

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);
    if (auth()->attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }
    return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
});

// Auth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications', function() { return view('notifications'); })->name('notifications');
    Route::get('/friends', function() { return view('friends'); })->name('friends');
    Route::get('/settings', function() { return view('settings'); })->name('settings');
    Route::get('/profile/{user?}', function() { return view('profile'); })->name('profile');
    
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/repository/{folder?}', [FolderController::class, 'index'])->name('repository.index');
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::get('/files/{file}/view', [FileController::class, 'view'])->name('files.view');
    Route::get('/files/{file}/stream', [FileController::class, 'stream'])->name('files.stream');
    Route::get('/files/{file}/force-delete', [FileController::class, 'destroy'])->name('files.force-delete');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::put('/files/{file}', [FileController::class, 'update'])->name('files.update');
});
