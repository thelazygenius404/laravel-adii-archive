<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganismeController;
use App\Http\Controllers\EntiteProductriceController;
use App\Http\Controllers\PlanClassementController;
use App\Http\Controllers\CalendrierConservationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\StockageController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\TraveeController;
use App\Http\Controllers\TabletteController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\BoiteController;
use App\Http\Controllers\DossierController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes for authenticated users
Route::middleware('auth')->group(function () {
    
    // Dashboard with automatic role redirection
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'gestionnaire_archives' => redirect()->route('archives.dashboard'),
            'service_producteurs' => redirect()->route('producteurs.dashboard'),
            'user' => redirect()->route('user.dashboard'),
            default => view('dashboard', compact('user'))
        };
    })->name('dashboard');
    
    // Profile management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    // Common API routes for all authenticated users
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/stockage/search', [StockageController::class, 'search'])->name('stockage.search');
        Route::get('/salles/organisme/{organisme}', [SalleController::class, 'byOrganisme'])->name('salles.by-organisme');
        Route::get('/travees/salle/{salle}', [TraveeController::class, 'bySalle'])->name('travees.by-salle');
        Route::get('/tablettes/travee/{travee}', [TabletteController::class, 'byTravee'])->name('tablettes.by-travee');
        Route::get('/positions/tablette/{tablette}', [PositionController::class, 'byTablette'])->name('positions.by-tablette');
        Route::get('/positions/available', [StockageController::class, 'findAvailablePositions'])->name('positions.available');
        Route::get('/stockage/statistics/{organisme}', [StockageController::class, 'statisticsByOrganisme'])->name('stockage.statistics');
    });
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // User management
    Route::resource('users', UserController::class);
    Route::get('/users/export/excel', [UserController::class, 'export'])->name('users.export');
    
    // Organisme management
    Route::resource('organismes', OrganismeController::class);
    Route::get('/organismes/export/excel', [OrganismeController::class, 'export'])->name('organismes.export');
    
    // Entités productrices management
    Route::resource('entites', EntiteProductriceController::class);
    Route::prefix('entites')->name('entites.')->group(function () {
        Route::get('/export/excel', [EntiteProductriceController::class, 'export'])->name('export');
        Route::put('/{entite}/move', [EntiteProductriceController::class, 'move'])->name('move');
        Route::post('/bulk-action', [EntiteProductriceController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/{entite}/statistics', [EntiteProductriceController::class, 'statistics'])->name('statistics');
    });
    
    // Plan de classement management
    Route::resource('plan-classement', PlanClassementController::class);
    Route::prefix('plan-classement')->name('plan-classement.')->group(function () {
        Route::get('/export/excel', [PlanClassementController::class, 'export'])->name('export');
        Route::post('/bulk-action', [PlanClassementController::class, 'bulkAction'])->name('bulk-action');
    });
    
    // Calendrier de conservation management
    Route::resource('calendrier-conservation', CalendrierConservationController::class);
    Route::prefix('calendrier-conservation')->name('calendrier-conservation.')->group(function () {
        Route::get('/export/excel', [CalendrierConservationController::class, 'export'])->name('export');
        Route::post('/bulk-action', [CalendrierConservationController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/plan/{planClassement}', [CalendrierConservationController::class, 'getReglesByPlan'])->name('by-plan');
    });
    
    // Storage management
    Route::prefix('stockage')->name('stockage.')->group(function () {
        Route::get('/', [StockageController::class, 'index'])->name('index');
        Route::get('/hierarchy/{organisme?}', [StockageController::class, 'hierarchy'])->name('hierarchy');
        Route::get('/search', [StockageController::class, 'search'])->name('search');
        Route::get('/optimize', [StockageController::class, 'optimizeStorage'])->name('optimize');
        Route::get('/export', [StockageController::class, 'exportReport'])->name('export');
        Route::get('/export-report', [StockageController::class, 'exportReport'])->name('exportReport');
        Route::get('/statistics/{organisme}', [StockageController::class, 'statisticsByOrganisme'])->name('statistics')
            ->where('organisme', '.*'); // Allow any character including spaces and special chars
        Route::get('/positions/available', [StockageController::class, 'findAvailablePositions'])->name('positions.available');
        Route::post('/positions/bulk-create', [StockageController::class, 'bulkCreatePositions'])->name('positions.bulk-create');
        

    });
    
    // Physical storage entities
    Route::resource('salles', SalleController::class);
    Route::prefix('salles')->name('salles.')->group(function () {
        Route::put('/{salle}/capacity', [SalleController::class, 'updateCapacity'])->name('update-capacity');
        Route::get('/organisme/{organisme}', [SalleController::class, 'byOrganisme'])->name('by-organisme');
        Route::get('/export', [SalleController::class, 'export'])->name('export');
        Route::get('/statistics', [SalleController::class, 'statistics'])->name('statistics');
        Route::post('/bulk-action', [SalleController::class, 'bulkAction'])->name('bulk-action');
    });
    
    // Travee management
    Route::resource('travees', TraveeController::class);
    Route::get('/travees/salle/{salle}', [TraveeController::class, 'bySalle'])->name('travees.by-salle');
    Route::get('/travees/export', [TraveeController::class, 'export'])->name('travees.export');
    Route::get('/travees/{travee}/statistics', [TraveeController::class, 'statistics'])->name('travees.statistics');
    Route::resource('tablettes', TabletteController::class);
    Route::get('/tablettes/travee/{travee}', [TabletteController::class, 'byTravee'])->name('tablettes.by-travee');
    Route::get('/tablettes/export', [TabletteController::class, 'export'])->name('tablettes.export');
    Route::post('/travees/bulk-action', [TraveeController::class, 'bulkAction'])->name('travees.bulk-action');
    Route::post('/tablettes/bulk-action', [TabletteController::class, 'bulkAction'])->name('tablettes.bulk-action');
    
    // Position management
    Route::resource('positions', PositionController::class);
    Route::prefix('positions')->name('positions.')->group(function () {
        Route::get('/tablette/{tablette}', [PositionController::class, 'byTablette'])->name('by-tablette');
        Route::put('/{position}/toggle', [PositionController::class, 'toggleStatus'])->name('toggle');
         Route::post('/bulk-create', [PositionController::class, 'bulkCreate'])->name('bulk-create');
        Route::get('/export', [PositionController::class, 'export'])->name('export');
        Route::get('/statistics', [PositionController::class, 'statistics'])->name('statistics');
        Route::post('/bulk-action', [PositionController::class, 'bulkAction'])->name('bulk-action');
         Route::post('/generate-for-travee', [PositionController::class, 'generateForTravee'])->name('generate-for-travee');
         
    });
    
    // Box and folder management
    Route::resource('boites', BoiteController::class);
    Route::prefix('boites')->name('boites.')->group(function () {
        Route::put('/{boite}/destroy-box', [BoiteController::class, 'destroyBox'])->name('destroy-box');
        Route::put('/{boite}/restore-box', [BoiteController::class, 'restoreBox'])->name('restore-box');
        Route::get('/position/{position}', [BoiteController::class, 'byPosition'])->name('by-position');
        Route::get('/export', [BoiteController::class, 'export'])->name('export');
        Route::get('/low-occupancy', [BoiteController::class, 'lowOccupancy'])->name('low-occupancy');
        Route::get('/available-space', [BoiteController::class, 'findAvailableSpace'])->name('available-space');
        Route::post('/bulk-action', [BoiteController::class, 'bulkAction'])->name('bulk-action');
    });
    
    Route::resource('dossiers', DossierController::class);
    Route::prefix('dossiers')->name('dossiers.')->group(function () {
        Route::get('/elimination/due', [DossierController::class, 'dueForElimination'])->name('elimination');
        Route::post('/mark-elimination', [DossierController::class, 'markForElimination'])->name('mark-elimination');
        Route::post('/archive', [DossierController::class, 'archiveDossiers'])->name('archive');
        Route::get('/export', [DossierController::class, 'export'])->name('export');
        Route::get('/statistics', [DossierController::class, 'statistics'])->name('statistics');
    });
    
    // Admin API routes
    Route::prefix('api')->name('api.')->group(function () {
        // Organisme and entity APIs
        Route::get('/organismes', [OrganismeController::class, 'api'])->name('organismes');
        Route::get('/entites/organisme/{organisme}', [EntiteProductriceController::class, 'byOrganisme'])->name('entites.by-organisme');
        Route::get('/entites/hierarchy/{organisme?}', [EntiteProductriceController::class, 'hierarchy'])->name('entites.hierarchy');
        Route::get('/entites/{entite}/children', [EntiteProductriceController::class, 'children'])->name('entites.children');
        Route::get('/entites/{entite}/breadcrumb', [EntiteProductriceController::class, 'breadcrumb'])->name('entites.breadcrumb');
        Route::get('/entites/{entite}/statistics', [EntiteProductriceController::class, 'statistics'])->name('entites.statistics');
        
        // Plan de classement APIs
        Route::get('/plan-classement', [PlanClassementController::class, 'api'])->name('plan-classement');
        Route::get('/plan-classement/statistics', [PlanClassementController::class, 'statistics'])->name('plan-classement.statistics');
        
        // Calendrier de conservation APIs
        Route::get('/calendrier-conservation/plan/{planClassementId}', [CalendrierConservationController::class, 'byPlanClassement'])->name('calendrier-conservation.by-plan');
        Route::get('/calendrier-conservation/statistics', [CalendrierConservationController::class, 'statistics'])->name('calendrier-conservation.statistics');
    });
});

// Archive manager routes
Route::middleware(['auth', 'gestionnaire'])->prefix('archives')->name('archives.')->group(function () {
    Route::get('/dashboard', function () {
        return view('archives.dashboard');
    })->name('dashboard');
    
    Route::get('/manage', function () {
        return view('archives.manage');
    })->name('manage');
    
    // Add specific archive management routes here
    // Route::resource('documents', DocumentController::class);
    // Route::resource('classifications', ClassificationController::class);
});

// Service producteurs routes
Route::middleware(['auth', 'service_producteurs'])->prefix('producteurs')->name('producteurs.')->group(function () {
    Route::get('/dashboard', function () {
        return view('producteurs.dashboard');
    })->name('dashboard');
    
    Route::get('/entities', function () {
        return view('producteurs.entities');
    })->name('entities');
    
    // Add specific producer service routes here
    // Route::resource('submissions', SubmissionController::class);
    // Route::resource('requests', RequestController::class);
});

// Standard user routes
Route::middleware(['auth', 'user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
    Route::get('/notifications', [UserDashboardController::class, 'notifications'])->name('notifications');
    
    // Add user-specific routes here
    // Route::get('/requests', [UserDashboardController::class, 'requests'])->name('requests');
    // Route::post('/requests', [UserDashboardController::class, 'createRequest'])->name('requests.create');
});

require __DIR__.'/auth.php';