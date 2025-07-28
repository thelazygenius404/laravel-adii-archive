<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\OrganismeController;
use App\Http\Controllers\EntiteProductriceController;
use App\Http\Controllers\PlanClassementController;
use App\Http\Controllers\CalendrierConservationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Protected routes for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
    // Gestion des organismes
    Route::prefix('admin/organismes')->name('admin.organismes.')->group(function () {
        Route::get('/', [OrganismeController::class, 'index'])->name('index');
        Route::get('/create', [OrganismeController::class, 'create'])->name('create');
        Route::post('/', [OrganismeController::class, 'store'])->name('store');
        Route::get('/{organisme}', [OrganismeController::class, 'show'])->name('show');
        Route::get('/{organisme}/edit', [OrganismeController::class, 'edit'])->name('edit');
        Route::put('/{organisme}', [OrganismeController::class, 'update'])->name('update');
        Route::delete('/{organisme}', [OrganismeController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [OrganismeController::class, 'export'])->name('export');
    });

    // Gestion des entités productrices
    Route::prefix('admin/entites')->name('admin.entites.')->group(function () {
        Route::get('/', [EntiteProductriceController::class, 'index'])->name('index');
        Route::get('/create', [EntiteProductriceController::class, 'create'])->name('create');
        Route::post('/', [EntiteProductriceController::class, 'store'])->name('store');
        Route::get('/{entite}', [EntiteProductriceController::class, 'show'])->name('show');
        Route::get('/{entite}/edit', [EntiteProductriceController::class, 'edit'])->name('edit');
        Route::put('/{entite}', [EntiteProductriceController::class, 'update'])->name('update');
        Route::delete('/{entite}', [EntiteProductriceController::class, 'destroy'])->name('destroy');
        Route::get('/export/excel', [EntiteProductriceController::class, 'export'])->name('export');
        
        // Additional methods
        Route::put('/{entite}/move', [EntiteProductriceController::class, 'move'])->name('move');
        Route::post('/bulk-action', [EntiteProductriceController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/{entite}/statistics', [EntiteProductriceController::class, 'statistics'])->name('statistics');
    });

    // API routes for AJAX calls
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/organismes', [OrganismeController::class, 'api'])->name('organismes');
        Route::get('/entites/organisme/{organisme}', [EntiteProductriceController::class, 'byOrganisme'])->name('entites.by-organisme');
        Route::get('/entites/hierarchy/{organisme?}', [EntiteProductriceController::class, 'hierarchy'])->name('entites.hierarchy');
        Route::get('/entites/{entite}/children', [EntiteProductriceController::class, 'children'])->name('entites.children');
        Route::get('/entites/{entite}/breadcrumb', [EntiteProductriceController::class, 'breadcrumb'])->name('entites.breadcrumb');
    });
    // Ajoutez ces routes dans routes/web.php dans la section admin middleware

// Gestion du plan de classement
Route::prefix('admin/plan-classement')->name('admin.plan-classement.')->group(function () {
    Route::get('/', [PlanClassementController::class, 'index'])->name('index');
    Route::get('/create', [PlanClassementController::class, 'create'])->name('create');
    Route::post('/', [PlanClassementController::class, 'store'])->name('store');
    Route::get('/{planClassement}', [PlanClassementController::class, 'show'])->name('show');
    Route::get('/{planClassement}/edit', [PlanClassementController::class, 'edit'])->name('edit');
    Route::put('/{planClassement}', [PlanClassementController::class, 'update'])->name('update');
    Route::delete('/{planClassement}', [PlanClassementController::class, 'destroy'])->name('destroy');
    Route::get('/export/excel', [PlanClassementController::class, 'export'])->name('export');
    Route::post('/bulk-action', [PlanClassementController::class, 'bulkAction'])->name('bulk-action');
});

// Gestion du calendrier de conservation
Route::prefix('admin/calendrier-conservation')->name('admin.calendrier-conservation.')->group(function () {
    Route::get('/', [CalendrierConservationController::class, 'index'])->name('index');
    Route::get('/create', [CalendrierConservationController::class, 'create'])->name('create');
    Route::post('/', [CalendrierConservationController::class, 'store'])->name('store');
    Route::get('/{calendrierConservation}', [CalendrierConservationController::class, 'show'])->name('show');
    Route::get('/{calendrierConservation}/edit', [CalendrierConservationController::class, 'edit'])->name('edit');
    Route::put('/{calendrierConservation}', [CalendrierConservationController::class, 'update'])->name('update');
    Route::delete('/{calendrierConservation}', [CalendrierConservationController::class, 'destroy'])->name('destroy');
    Route::get('/export/excel', [CalendrierConservationController::class, 'export'])->name('export');
    Route::post('/bulk-action', [CalendrierConservationController::class, 'bulkAction'])->name('bulk-action');
    Route::get('/plan/{planClassement}', [CalendrierConservationController::class, 'getReglesByPlan'])->name('by-plan');
});

// API routes supplémentaires
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/plan-classement', [PlanClassementController::class, 'api'])->name('plan-classement');
    Route::get('/calendrier-conservation/plan/{planClassementId}', [CalendrierConservationController::class, 'byPlanClassement'])->name('calendrier-conservation.by-plan');
    Route::get('/plan-classement/statistics', [PlanClassementController::class, 'statistics'])->name('plan-classement.statistics');
    Route::get('/calendrier-conservation/statistics', [CalendrierConservationController::class, 'statistics'])->name('calendrier-conservation.statistics');
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
