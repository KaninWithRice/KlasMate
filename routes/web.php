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
    try {
        // Force the disk to throw exceptions so we can see the real error
        config(['filesystems.disks.s3.throw' => true]);
        
        $disk = \Illuminate\Support\Facades\Storage::disk('s3');
        $filename = 'test_' . time() . '.txt';
        $content = 'Supabase Storage Test at ' . now();
        
        echo "<h1>Storage Test (Detailed)</h1>";
        echo "<ul>";
        echo "<li>Bucket: " . config('filesystems.disks.s3.bucket') . "</li>";
        echo "<li>Endpoint: " . config('filesystems.disks.s3.endpoint') . "</li>";
        echo "<li>Path Style: " . (config('filesystems.disks.s3.use_path_style_endpoint') ? 'TRUE' : 'FALSE') . "</li>";
        echo "</ul>";
        
        echo "<p>Attempting upload...</p>";
        $disk->put($filename, $content);
        
        echo "<p style='color:green;'>✅ Upload Successful!</p>";
        echo "<p>Public URL: " . $disk->url($filename) . "</p>";
        
        $disk->delete($filename);
        echo "<p>Cleanup: Deleted test file.</p>";
        
    } catch (\Throwable $e) {
        echo "<div style='color:red; border:1px solid red; padding:10px;'>";
        echo "<h3>❌ STORAGE FATAL ERROR:</h3>";
        echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
        if (method_exists($e, 'getResponse')) {
            echo "<p><strong>Response:</strong> " . (string) $e->getResponse()->getBody() . "</p>";
        }
        echo "</div>";
        echo "<h4>Stack Trace:</h4><pre style='font-size:10px;'>" . $e->getTraceAsString() . "</pre>";
    }
    return "";
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
        $q = $request->query('q');
        if (!$q) return [];
        
        $query = \App\Models\User::where('id', '!=', auth()->id());
        
        // Split search terms to match multiple parts of the name
        $terms = explode(' ', $q);
        foreach ($terms as $term) {
            $query->where('name', 'like', "%{$term}%");
        }
        
        return $query->take(10)->get();
    });
});
