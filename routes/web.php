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
        // Redirection automatique selon le rôle
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'gestionnaire_archives':
                return redirect()->route('archives.dashboard');
            case 'service_producteurs':
                return redirect()->route('producteurs.dashboard');
            case 'user':
                return redirect()->route('user.dashboard');
            default:
                // Fallback si le rôle n'est pas reconnu
                return view('dashboard', compact('user'));
        }
    })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});





// Admin only routes
Route::middleware(['auth', 'admin'])->group(function () {
    // Dashboard admin
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
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

// API routes for AJAX calls
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/organismes', [OrganismeController::class, 'api'])->name('organismes');
    Route::get('/entites/organisme/{organisme}', [EntiteProductriceController::class, 'byOrganisme'])->name('entites.by-organisme');
    Route::get('/entites/hierarchy/{organisme?}', [EntiteProductriceController::class, 'hierarchy'])->name('entites.hierarchy');
    Route::get('/entites/{entite}/children', [EntiteProductriceController::class, 'children'])->name('entites.children');
    Route::get('/entites/{entite}/breadcrumb', [EntiteProductriceController::class, 'breadcrumb'])->name('entites.breadcrumb');
    Route::get('/entites/{entite}/statistics', [EntiteProductriceController::class, 'statistics'])->name('entites.statistics'); // Add this line
});
// Routes pour la gestion du stockage
   Route::get('/stockage', [StockageController::class, 'index'])->name('stockage.index');
    Route::get('/stockage/hierarchy/{organisme?}', [StockageController::class, 'hierarchy'])->name('stockage.hierarchy');
    Route::get('/stockage/search', [StockageController::class, 'search'])->name('stockage.search');
    Route::get('/stockage/optimize', [StockageController::class, 'optimizeStorage'])->name('stockage.optimize');
    Route::get('/stockage/export', [StockageController::class, 'exportReport'])->name('stockage.export');
    Route::get('/stockage/statistics/{organisme}', [StockageController::class, 'statisticsByOrganisme'])->name('stockage.statistics');
    Route::get('/stockage/positions/available', [StockageController::class, 'findAvailablePositions'])->name('stockage.positions.available');

    // Gestion des salles
    Route::resource('salles', SalleController::class);
    Route::put('/salles/{salle}/capacity', [SalleController::class, 'updateCapacity'])->name('salles.update-capacity');
    Route::get('/salles/organisme/{organisme}', [SalleController::class, 'byOrganisme'])->name('salles.by-organisme');
    Route::get('/salles/export', [SalleController::class, 'export'])->name('salles.export');
    Route::get('/salles/statistics', [SalleController::class, 'statistics'])->name('salles.statistics');

    // Gestion des travées
    Route::resource('travees', TraveeController::class);
    Route::get('/travees/salle/{salle}', [TraveeController::class, 'bySalle'])->name('travees.by-salle');

    // Gestion des tablettes
    Route::resource('tablettes', TabletteController::class);
    Route::get('/tablettes/travee/{travee}', [TabletteController::class, 'byTravee'])->name('tablettes.by-travee');

    // Gestion des positions
    Route::resource('positions', PositionController::class);
    Route::get('/positions/tablette/{tablette}', [PositionController::class, 'byTablette'])->name('positions.by-tablette');
    Route::put('/positions/{position}/toggle', [PositionController::class, 'toggleStatus'])->name('positions.toggle');

    // Gestion des boîtes
    Route::resource('boites', BoiteController::class);
    Route::put('/boites/{boite}/destroy-box', [BoiteController::class, 'destroyBox'])->name('boites.destroy-box');
    Route::put('/boites/{boite}/restore-box', [BoiteController::class, 'restoreBox'])->name('boites.restore-box');
    Route::get('/boites/position/{position}', [BoiteController::class, 'byPosition'])->name('boites.by-position');
    Route::get('/boites/export', [BoiteController::class, 'export'])->name('boites.export');
    Route::get('/boites/low-occupancy', [BoiteController::class, 'lowOccupancy'])->name('boites.low-occupancy');
    Route::get('/boites/available-space', [BoiteController::class, 'findAvailableSpace'])->name('boites.available-space');
    Route::post('/boites/bulk-action', [BoiteController::class, 'bulkAction'])->name('boites.bulk-action');

    // Gestion des dossiers
    Route::resource('dossiers', DossierController::class);
    Route::get('/dossiers/elimination/due', [DossierController::class, 'dueForElimination'])->name('dossiers.elimination');
    Route::post('/dossiers/mark-elimination', [DossierController::class, 'markForElimination'])->name('dossiers.mark-elimination');
    Route::post('/dossiers/archive', [DossierController::class, 'archiveDossiers'])->name('dossiers.archive');
    Route::get('/dossiers/export', [DossierController::class, 'export'])->name('dossiers.export');
    Route::get('/dossiers/statistics', [DossierController::class, 'statistics'])->name('dossiers.statistics');
  // Dashboard stockage
    Route::prefix('admin/stockage')->name('admin.stockage.')->group(function () {
        Route::get('/', [StockageController::class, 'index'])->name('index');
        Route::get('/hierarchy/{organisme?}', [StockageController::class, 'hierarchy'])->name('hierarchy');
        Route::get('/search', [StockageController::class, 'search'])->name('search');
        Route::get('/optimize', [StockageController::class, 'optimizeStorage'])->name('optimize');
        Route::get('/export', [StockageController::class, 'exportReport'])->name('export');
        Route::get('/statistics/{organisme}', [StockageController::class, 'statisticsByOrganisme'])->name('statistics');
        Route::get('/positions/available', [StockageController::class, 'findAvailablePositions'])->name('positions.available');
    });

    // Gestion des salles avec préfixe et nom appropriés
    Route::prefix('admin/salles')->name('admin.salles.')->group(function () {
        Route::get('/', [SalleController::class, 'index'])->name('index');
        Route::get('/create', [SalleController::class, 'create'])->name('create');
        Route::post('/', [SalleController::class, 'store'])->name('store');
        Route::get('/{salle}', [SalleController::class, 'show'])->name('show');
        Route::get('/{salle}/edit', [SalleController::class, 'edit'])->name('edit');
        Route::put('/{salle}', [SalleController::class, 'update'])->name('update');
        Route::delete('/{salle}', [SalleController::class, 'destroy'])->name('destroy');
        
        // Routes supplémentaires
        Route::put('/{salle}/capacity', [SalleController::class, 'updateCapacity'])->name('update-capacity');
        Route::get('/organisme/{organisme}', [SalleController::class, 'byOrganisme'])->name('by-organisme');
        Route::get('/export', [SalleController::class, 'export'])->name('export');
        Route::get('/statistics', [SalleController::class, 'statistics'])->name('statistics');
    });

    // Gestion des travées avec préfixe et nom appropriés
    Route::prefix('admin/travees')->name('admin.travees.')->group(function () {
        Route::get('/', [TraveeController::class, 'index'])->name('index');
        Route::get('/create', [TraveeController::class, 'create'])->name('create');
        Route::post('/', [TraveeController::class, 'store'])->name('store');
        Route::get('/{travee}', [TraveeController::class, 'show'])->name('show');
        Route::get('/{travee}/edit', [TraveeController::class, 'edit'])->name('edit');
        Route::put('/{travee}', [TraveeController::class, 'update'])->name('update');
        Route::delete('/{travee}', [TraveeController::class, 'destroy'])->name('destroy');
        
        Route::get('/salle/{salle}', [TraveeController::class, 'bySalle'])->name('by-salle');
    });

    // Gestion des tablettes avec préfixe et nom appropriés
    Route::prefix('admin/tablettes')->name('admin.tablettes.')->group(function () {
        Route::get('/', [TabletteController::class, 'index'])->name('index');
        Route::get('/create', [TabletteController::class, 'create'])->name('create');
        Route::post('/', [TabletteController::class, 'store'])->name('store');
        Route::get('/{tablette}', [TabletteController::class, 'show'])->name('show');
        Route::get('/{tablette}/edit', [TabletteController::class, 'edit'])->name('edit');
        Route::put('/{tablette}', [TabletteController::class, 'update'])->name('update');
        Route::delete('/{tablette}', [TabletteController::class, 'destroy'])->name('destroy');
        
        Route::get('/travee/{travee}', [TabletteController::class, 'byTravee'])->name('by-travee');
    });

    // Gestion des positions avec préfixe et nom appropriés
    Route::prefix('admin/positions')->name('admin.positions.')->group(function () {
        Route::get('/', [PositionController::class, 'index'])->name('index');
        Route::get('/create', [PositionController::class, 'create'])->name('create');
        Route::post('/', [PositionController::class, 'store'])->name('store');
        Route::get('/{position}', [PositionController::class, 'show'])->name('show');
        Route::get('/{position}/edit', [PositionController::class, 'edit'])->name('edit');
        Route::put('/{position}', [PositionController::class, 'update'])->name('update');
        Route::delete('/{position}', [PositionController::class, 'destroy'])->name('destroy');
        
        Route::get('/tablette/{tablette}', [PositionController::class, 'byTablette'])->name('by-tablette');
        Route::put('/{position}/toggle', [PositionController::class, 'toggleStatus'])->name('toggle');
    });

    // Gestion des boîtes avec préfixe et nom appropriés
    Route::prefix('admin/boites')->name('admin.boites.')->group(function () {
        Route::get('/', [BoiteController::class, 'index'])->name('index');
        Route::get('/create', [BoiteController::class, 'create'])->name('create');
        Route::post('/', [BoiteController::class, 'store'])->name('store');
        Route::get('/{boite}', [BoiteController::class, 'show'])->name('show');
        Route::get('/{boite}/edit', [BoiteController::class, 'edit'])->name('edit');
        Route::put('/{boite}', [BoiteController::class, 'update'])->name('update');
        Route::delete('/{boite}', [BoiteController::class, 'destroy'])->name('destroy');
        
        // Routes supplémentaires
        Route::put('/{boite}/destroy-box', [BoiteController::class, 'destroyBox'])->name('destroy-box');
        Route::put('/{boite}/restore-box', [BoiteController::class, 'restoreBox'])->name('restore-box');
        Route::get('/position/{position}', [BoiteController::class, 'byPosition'])->name('by-position');
        Route::get('/export', [BoiteController::class, 'export'])->name('export');
        Route::get('/low-occupancy', [BoiteController::class, 'lowOccupancy'])->name('low-occupancy');
        Route::get('/available-space', [BoiteController::class, 'findAvailableSpace'])->name('available-space');
        Route::post('/bulk-action', [BoiteController::class, 'bulkAction'])->name('bulk-action');
    });

    // Gestion des dossiers avec préfixe et nom appropriés
    Route::prefix('admin/dossiers')->name('admin.dossiers.')->group(function () {
        Route::get('/', [DossierController::class, 'index'])->name('index');
        Route::get('/create', [DossierController::class, 'create'])->name('create');
        Route::post('/', [DossierController::class, 'store'])->name('store');
        Route::get('/{dossier}', [DossierController::class, 'show'])->name('show');
        Route::get('/{dossier}/edit', [DossierController::class, 'edit'])->name('edit');
        Route::put('/{dossier}', [DossierController::class, 'update'])->name('update');
        Route::delete('/{dossier}', [DossierController::class, 'destroy'])->name('destroy');
        
        // Routes supplémentaires
        Route::get('/elimination/due', [DossierController::class, 'dueForElimination'])->name('elimination');
        Route::post('/mark-elimination', [DossierController::class, 'markForElimination'])->name('mark-elimination');
        Route::post('/archive', [DossierController::class, 'archiveDossiers'])->name('archive');
        Route::get('/export', [DossierController::class, 'export'])->name('export');
        Route::get('/statistics', [DossierController::class, 'statistics'])->name('statistics');
    });

});
// Routes API pour les utilisateurs authentifiés
Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    
    // API pour recherche rapide
    Route::get('/stockage/search', [StockageController::class, 'search'])->name('stockage.search');
    
    // API pour récupérer les données liées
    Route::get('/salles/organisme/{organisme}', [SalleController::class, 'byOrganisme'])->name('salles.by-organisme');
    Route::get('/travees/salle/{salle}', [TraveeController::class, 'bySalle'])->name('travees.by-salle');
    Route::get('/tablettes/travee/{travee}', [TabletteController::class, 'byTravee'])->name('tablettes.by-travee');
    Route::get('/positions/tablette/{tablette}', [PositionController::class, 'byTablette'])->name('positions.by-tablette');
    Route::get('/positions/available', [StockageController::class, 'findAvailablePositions'])->name('positions.available');
    
    // API pour les statistiques
    Route::get('/stockage/statistics/{organisme}', [StockageController::class, 'statisticsByOrganisme'])->name('stockage.statistics');
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
// NOUVEAU : Routes pour les utilisateurs standard
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/profile', [UserDashboardController::class, 'profile'])->name('user.profile');
    Route::get('/user/notifications', [UserDashboardController::class, 'notifications'])->name('user.notifications');
});

require __DIR__.'/auth.php';
