<?php

use App\Models\User;
use App\Models\Asset;
use App\Models\Incident;
use App\Models\Problem; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProblemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;

// Route::get('/buat-token', function () {
//     $user = User::where('email', 'admin@gmail.com')->first();
//     $token = $user->createToken('token-dari-route')->plainTextToken;
//     return response()->json(['token' => $token]);
// });

    Route::get('/', function (
        ) {return view('welcome');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::middleware('auth')->group(function () {
        // Rute profil dari Breeze
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Rute untuk semua fitur utama
        Route::resource('incidents', IncidentController::class);
        Route::resource('problems', ProblemController::class)->middleware('role:admin,security');
        Route::resource('users', UserController::class)->middleware('role:admin');
        Route::resource('assets', AssetController::class)->middleware('role:admin');
        Route::resource('sites', SiteController::class)->middleware('role:admin');
        Route::get('/get-assets-by-site/{site}', [AssetController::class, 'getAssetsBySite'])->name('assets.by_site');
        Route::get('/search', [SearchController::class, 'index'])->name('search.index');
        Route::post('/incidents/{incident}/comments', [CommentController::class, 'storeForIncident'])->name('incidents.comments.store');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('role:admin,security');
        Route::get('/pusat-bantuan', [KnowledgeBaseController::class, 'index'])->name('kb.index');
        Route::get('/pusat-bantuan/{article:slug}', [KnowledgeBaseController::class, 'show'])->name('kb.show');
        Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
        Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
        Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('articles', AdminArticleController::class);
        
    });
    

    // Rute untuk Manajemen Site
    Route::get('/sites/{site}/assets/{category}/{status}', [SiteController::class, 'showAssetList'])->name('sites.assets.list')->middleware('role:admin');

    // Rute API untuk mengambil aset berdasarkan site
    Route::get('/api/sites/{site}/assets', [AssetController::class, 'getAssetsBySite'])->name('api.sites.assets');
    Route::get('/v1/sites/{site}/assets', [ApiController::class, 'getAssetsBySite']);    
    
    
    });


// // Gabungkan semua rute yang butuh login di sini
// Route::middleware('auth')->group(function () {
//     // Rute profil dari Breeze
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//     Route::resource('incidents', IncidentController::class);
//     Route::resource('problems', ProblemController::class)->middleware('role:admin,security');
//     // Route::resource('assets', AssetController::class)->middleware('role:admin');
//     Route::get('/sites', [SiteController::class, 'index'])->name('sites.index')->middleware('role:admin');
//     Route::get('/sites/{site}', [SiteController::class, 'show'])->name('sites.show')->middleware('role:admin');
//     Route::get('/sites/{site}/assets/{category}/{status}', [SiteController::class, 'showAssetList'])->name('sites.assets.list')->middleware('role:admin');
//     Route::resource('users', UserController::class)->middleware('role:admin');
//     Route::post('/incidents/{incident}/comments', [CommentController::class, 'storeForIncident'])->name('incidents.comments.store');
// });

require __DIR__.'/auth.php';