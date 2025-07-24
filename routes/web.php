<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArchiveController;
Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Protected routes for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    })->name('dashboard');
});

// Admin only routes
Route::middleware(['auth', 'admin'])->group(function () {
    // Dashboard admin
    
    Route::get('/admin/dashboard', function () {
        return view('dashboard');
    })->name('admin.dashboard');
    
    // Gestion des utilisateurs
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [UserController::class, 'export'])->name('export');
    });
});

// Gestionnaire archives only routes
Route::middleware(['auth', 'gestionnaire'])->group(function () {
    Route::get('/archives/dashboard', function () {
        return view('archives.dashboard');
    })->name('archives.dashboard');
    
    Route::get('/archives/manage', function () {
        return view('archives.manage');
    })->name('archives.manage');
});

// Service producteurs only routes
Route::middleware(['auth', 'service_producteurs'])->group(function () {
    Route::get('/producteurs/dashboard', function () {
        return view('producteurs.dashboard');
    })->name('producteurs.dashboard');
    
    Route::get('/producteurs/entities', function () {
        return view('producteurs.entities');
    })->name('producteurs.entities');
});

require __DIR__.'/auth.php';
