<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\OtpAuthController;
use App\Http\Controllers\FriendshipController;
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
    Route::get('/api/users/search', function (\Illuminate\Http\Request $request) {
        $q = trim($request->query('q', ''));
        $currentId = auth()->id();
        
        $query = \App\Models\User::where('id', '!=', $currentId);
        
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $qLower = strtolower($q);
                $sub->whereRaw('LOWER(name) LIKE ?', ["%{$qLower}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$qLower}%"]);
            });
        }
        
        $users = $query->orderBy('name', 'asc')->take(10)->get();

        return $users->map(function($user) use ($currentId) {
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
        });
    });

    Route::post('/friends/{user}/request', [FriendshipController::class, 'sendRequest'])->name('friends.request');
    Route::post('/friends/{user}/accept', [FriendshipController::class, 'acceptRequest'])->name('friends.accept');

    Route::get('/settings', function() { return view('settings'); })->name('settings');
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
    Route::get('/files/{file}/stream', [FileController::class, 'stream'])->name('files.stream');
    Route::get('/files/{file}/force-delete', [FileController::class, 'destroy'])->name('files.force-delete');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::put('/files/{file}', [FileController::class, 'update'])->name('files.update');
});
