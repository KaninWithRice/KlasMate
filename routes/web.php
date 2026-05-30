<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\FriendshipController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;

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

// TEMPORARY SEED ROUTE
Route::get('/seed', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        return "<h1>Seeding Successful!</h1><pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "<h1>Seeding Failed!</h1><pre>" . $e->getMessage() . "</pre>";
    }
});

// ⚠️ EMERGENCY RESET ROUTE (Delete everything and start fresh)
Route::get('/resetall', function () {
    // Safety check: Require a ?confirm=true parameter to prevent accidental clicks
    if (request('confirm') !== 'true') {
        return "<h1>Warning: This will delete ALL users, courses, files, and messages!</h1>
                <p>To proceed, visit: <a href='/resetall?confirm=true'>/resetall?confirm=true</a></p>";
    }

    try {
        // 1. Wipe everything and run migrations fresh
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        // 2. Re-seed the default data (like the test users)
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();

        return "<h1>System Reset Successful!</h1>
                <h3>Migration Output:</h3><pre>$migrateOutput</pre>
                <h3>Seeder Output:</h3><pre>$seedOutput</pre>
                <p><a href='/login'>Go to Login</a></p>";
    } catch (\Exception $e) {
        return "<h1>Reset Failed!</h1><pre>" . $e->getMessage() . "</pre>";
    }
});

// TEMPORARY USER DEBUG ROUTE
Route::get('/debug-users', function () {
    try {
        $users = \App\Models\User::all();
        $friends = \App\Models\Friendship::all();
        return [
            'users_count' => $users->count(),
            'friendships_count' => $friends->count(),
            'current_user_id' => auth()->id(),
            'users' => $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name]),
        ];
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
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
    
    Route::get('/notifications', function() { 
        $requests = auth()->user()->friendRequests;
        return view('notifications', compact('requests')); 
    })->name('notifications');
    
    Route::get('/friends', function() { 
        $friends = auth()->user()->friends;
        return view('friends', compact('friends')); 
    })->name('friends');

    // 🚀 NEW SECURE SEARCH API
    Route::get('/friends/search', function (\Illuminate\Http\Request $request) {
        $q = trim($request->query('q', ''));
        $currentId = auth()->id();
        
        $query = \App\Models\User::where('id', '!=', $currentId);
        
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'ILIKE', "%{$q}%")
                    ->orWhere('email', 'ILIKE', "%{$q}%");
            });
        }
        
        $users = $query->orderBy('name', 'asc')->take(10)->get();

        return [
            'results' => $users->map(function($user) use ($currentId) {
                $friendship = \App\Models\Friendship::where(function($f) use ($currentId, $user) {
                    $f->where('user_id', $currentId)->where('friend_id', $user->id);
                })->orWhere(function($f) use ($currentId, $user) {
                    $f->where('user_id', $user->id)->where('friend_id', $currentId);
                })->first();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar,
                    'friend_status' => $friendship ? $friendship->status : 'none',
                    'is_requester' => $friendship && $friendship->user_id == $currentId
                ];
            })
        ];
    });

    Route::get('/courses/search', function (\Illuminate\Http\Request $request) {
        $q = trim($request->query('q', ''));
        $currentId = auth()->id();

        if ($q === '') return ['results' => []];

        $folders = \App\Models\Folder::whereNull('parent_id')
            ->where(function($query) use ($q) {
                $query->where('name', 'ILIKE', "%{$q}%")
                      ->orWhere('code', 'ILIKE', "%{$q}%");
            })
            ->take(10)
            ->get();

        return [
            'results' => $folders->map(function($folder) use ($currentId) {
                // Check if user has access
                $hasAccess = $folder->is_public 
                    || $folder->user_id === $currentId 
                    || \DB::table('folder_visits')->where('user_id', $currentId)->where('folder_id', $folder->id)->exists();

                return [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'code' => $folder->code,
                    'color' => $folder->color,
                    'is_public' => (bool)$folder->is_public,
                    'has_access' => (bool)$hasAccess,
                    'user_id' => $folder->user_id
                ];
            })
        ];
    });

    Route::post('/friends/{user}/request', [FriendshipController::class, 'sendRequest'])->name('friends.request');
    Route::post('/friends/{user}/accept', [FriendshipController::class, 'acceptRequest'])->name('friends.accept');

    // CHAT & SHARING
    Route::get('/chat/{friend}', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat/{friend}', [ChatController::class, 'store'])->name('chat.store');
    Route::post('/share/send', [ChatController::class, 'share'])->name('share.send');

    Route::get('/settings', function() { return view('settings'); })->name('settings');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::get('/profile/{user?}', function($user = null) { 
        $targetUser = $user ? \App\Models\User::findOrFail($user) : auth()->user();
        
        if ($targetUser->id !== auth()->id()) {
            $isFriend = \App\Models\Friendship::where('status', 'accepted')
                ->where(function($q) use ($targetUser) {
                    $q->where('user_id', auth()->id())->where('friend_id', $targetUser->id);
                })->exists();
                
            if (!$isFriend) {
                return redirect()->route('friends')->with('error', 'You must be friends to view this profile.');
            }
        }
        
        $folders = \App\Models\Folder::where('user_id', $targetUser->id)
            ->where('is_public', true)
            ->get();
            
        return view('profile', compact('targetUser', 'folders')); 
    })->name('profile');
    
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/repository/{folder?}', [FolderController::class, 'index'])->name('repository.index');
    Route::post('/folders', [FolderController::class, 'store'])->name('folders.store');
    Route::get('/files/{file}/view', [FileController::class, 'view'])->name('files.view');
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/stream', [FileController::class, 'stream'])->name('files.stream');
    Route::get('/files/{file}/force-delete', [FileController::class, 'destroy'])->name('files.force-delete');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::put('/files/{file}', [FileController::class, 'update'])->name('files.update');
});
