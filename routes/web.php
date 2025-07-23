<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ArchiveController;
Route::get('/', function () {
    return view('welcome');
});

// Protected routes for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    })->name('dashboard');
});

// Admin only routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/admin/users', function () {
        return view('admin.users');
    })->name('admin.users');
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
