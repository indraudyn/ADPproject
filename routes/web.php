<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CeritaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CeritaController as AdminCeritaController;
use App\Http\Controllers\Admin\ForumAdminController;
use App\Http\Controllers\Admin\QuizController;

/*
| HALAMAN AWAL
*/
Route::get('/', function () {
    return view('welcome');
});

/*
| DASHBOARD (DEFAULT)
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
| PROFILE (AUTH)
*/
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
| ADMIN VIEW
*/
Route::middleware(['auth', 'admin'])->get('/admin', function () {
    return view('admin.dashboard');
});

/*
| DASHBOARD USER (VIEW)
*/
Route::middleware(['auth'])->get('/dashboard', function () {
    return view('dashboard.user');
})->name('dashboard');

/*
| PROFILE (VIEW & CONTROLLER)
*/
Route::get('/profile', function () {
    return view('profile');
})->middleware('auth')->name('profile');

Route::get('/profile', [ProfileController::class, 'index'])
    ->middleware('auth')
    ->name('profile');

Route::put('/profile', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

/*
| DASHBOARD USER (VIEW)
*/
Route::get('/dashboard', fn () => view('dashboard.user'))
    ->name('dashboard');

/*
| CERITA (VIEW)
*/
Route::get('/cerita/upload', fn () => view('cerita.upload'))
    ->name('cerita.upload');

Route::get('/cerita', fn () => view('cerita.index'))
    ->name('cerita.index');

/*
| MENU UMUM
*/
Route::get('/forum', fn () => view('forum.index'))
    ->name('forum.index');

Route::get('/settings', fn () => view('settings'))
    ->name('settings');

/*
| CERITA (VIEW DUPLIKAT)
*/
Route::get('/cerita/upload', function () {
    return view('cerita.upload');
})->name('cerita.upload');

/*
| CERITA (CONTROLLER)
*/
Route::get('/cerita/create', [CeritaController::class, 'create'])
    ->name('cerita.create');

Route::get('/cerita/create', [CeritaController::class, 'create'])
    ->name('cerita.create');

Route::post('/cerita/store', [CeritaController::class, 'store'])
    ->name('cerita.store');

/*
| CERITA (AUTH GROUP)
*/
Route::middleware('auth')->group(function () {

    Route::get('/cerita/upload', [CeritaController::class, 'upload'])
        ->middleware('auth')
        ->name('cerita.upload');

    Route::get('/cerita/create', [CeritaController::class, 'create'])
        ->name('cerita.create');

    Route::post('/cerita/store', [CeritaController::class, 'store'])
        ->name('cerita.store');

    Route::get('/cerita/{cerita}/edit', [CeritaController::class, 'edit'])
        ->name('cerita.edit');

    Route::get('/cerita/{cerita}', [CeritaController::class, 'show'])
        ->name('cerita.show');

    Route::delete('/cerita/{cerita}', [CeritaController::class, 'destroy'])
        ->name('cerita.destroy');
});

/*
| CERITA (AUTH DUPLIKAT)
*/
Route::middleware('auth')->group(function () {

    Route::get('/cerita/create', [CeritaController::class, 'create'])
        ->name('cerita.create');

    Route::post('/cerita/store', [CeritaController::class, 'store'])
        ->name('cerita.store');

    Route::get('/cerita/upload', [CeritaController::class, 'upload'])
        ->name('cerita.upload');
});

/*
| CERITA SHOW
*/
Route::get('/cerita/{cerita}', [CeritaController::class, 'show'])
    ->middleware('auth')
    ->name('cerita.show');

/*
| CERITA EDIT & UPDATE
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/cerita/{cerita}/edit', [CeritaController::class, 'edit'])
        ->name('cerita.edit');

    Route::put('/cerita/{cerita}', [CeritaController::class, 'update'])
        ->name('cerita.update');
});

/*
| DASHBOARD CONTROLLER
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

/*
| DASHBOARD ADMIN
*/
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
});

/*
| UPDATE ROLE USER
*/
Route::put('/admin/user/{user}/role', function ($userId, Request $request) {

    \App\Models\User::where('id', $userId)
        ->update(['role' => $request->role]);

    return back();

})->middleware(['auth', 'admin'])->name('admin.user.role');

/*
| DASHBOARD GROUP
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard USER
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Dashboard ADMIN
    Route::middleware('admin')->group(function () {

        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard');
    });
});

Route::middleware(['auth'])->get('/cerita', function () {
    $ceritas = \App\Models\Cerita::where('status', 'approved')
        ->latest()
        ->paginate(8);

    return view('cerita.index', compact('ceritas'));
})->name('cerita.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::delete('/forum/{id}', [ForumController::class, 'destroy'])->name('forum.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Halaman daftar user
    Route::get('/users', [UserController::class, 'index'])
        ->name('admin.users.index');

    // (OPSIONAL) Update role user
    Route::post('/users/{id}/role', [UserController::class, 'updateRole'])
        ->name('admin.users.updateRole');

});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.cerita.')
    ->group(function () {

        Route::get('/cerita', [AdminCeritaController::class, 'index'])
            ->name('index');

        Route::put('/cerita/{id}/status', [AdminCeritaController::class, 'updateStatus'])
            ->name('updateStatus');

        Route::get('/cerita/{id}', [AdminCeritaController::class, 'show'])
            ->name('show');
    });

    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/forum', [ForumAdminController::class, 'index'])->name('admin.forum.index');
    Route::post('/forum', [ForumAdminController::class, 'store'])->name('admin.forum.store');
});

Route::middleware(['auth','admin'])
->prefix('admin')
->group(function(){

    Route::get('/quiz', [QuizController::class,'index'])
        ->name('admin.quiz.index');

    Route::get('/quiz/create', [QuizController::class,'create'])
        ->name('admin.quiz.create');

    Route::post('/quiz/store', [QuizController::class,'store'])
        ->name('admin.quiz.store');

    Route::get('/quiz/{id}/edit', [QuizController::class,'edit'])
        ->name('admin.quiz.edit');

    Route::put('/quiz/{id}', [QuizController::class,'update'])
        ->name('admin.quiz.update');

    Route::delete('/quiz/{id}', [QuizController::class,'destroy'])
        ->name('admin.quiz.destroy');

});

//AUTH ROUTES
require __DIR__.'/auth.php';
