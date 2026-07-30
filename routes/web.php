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
use App\Http\Controllers\QuizPlayController;

/*
| HALAMAN AWAL
*/
Route::get('/', function () {
    $parwas = \App\Models\Parwa::take(3)->get();
    return view('welcome', compact('parwas'));
});

Route::get('/set-locale/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('set-locale');



Route::controller(App\Http\Controllers\ParwaController::class)->group(function () {
    Route::get('/parwa', 'index')->name('parwa.index');
    Route::get('/parwa/read/{book}/{section}/{version?}', 'read')->name('parwa.read');
    Route::get('/parwa/{slug}/{version?}', 'show')->name('parwa.detail');
    Route::get('/parwa/{slug}/video', 'video')->name('parwa.video');
});

Route::post('/set-parwa-version', function (Illuminate\Http\Request $request) {
    session(['selected_parwa_version' => $request->version]);
    return response()->json(['success' => true]);
})->name('set-parwa-version');



Route::get('/ajax/parwa/sections-by-book', [App\Http\Controllers\ParwaController::class, 'sectionsByBook']);

Route::get('/ajax/debug-db', function() {
    return \App\Models\Cerita::select('id', 'judul', 'book', 'section', 'versionId', 'status')->get();
});

Route::post('/video', [App\Http\Controllers\VideoController::class, 'store'])->name('video.store');




/*
| ADMIN VIEW
*/
Route::middleware(['auth', 'admin'])->get('/admin', function () {
    return view('admin.dashboard');
});


/*
| PROFILE (VIEW & CONTROLLER)
*/
Route::get('/profile', [ProfileController::class, 'index'])
    ->middleware('auth')
    ->name('profile');

Route::put('/profile', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])
    ->middleware('auth')
    ->name('profile.photo.destroy');


/*
| MENU UMUM
*/
Route::get('/settings', fn () => view('settings'))
    ->name('settings');

/*
| CERITA
*/
Route::middleware('auth')->group(function () {

    Route::get('/cerita/upload', [CeritaController::class, 'upload'])
        ->name('cerita.upload');

    Route::get('/cerita/create', [CeritaController::class, 'create'])
        ->name('cerita.create');

    Route::post('/cerita/store', [CeritaController::class, 'store'])
        ->name('cerita.store');

    Route::get('/cerita/{id}/edit', [CeritaController::class, 'edit'])
        ->name('cerita.edit');

    Route::put('/cerita/{id}', [CeritaController::class, 'update'])
        ->name('cerita.update');

    Route::delete('/cerita/{id}', [CeritaController::class, 'destroy'])
        ->name('cerita.destroy');
});

Route::get('/cerita/{id}', [CeritaController::class, 'show'])
    ->name('cerita.show');




/*
| UPDATE ROLE USER
*/
Route::put('/admin/user/{id}/role', [UserController::class, 'updateRole'])
    ->middleware(['auth', 'admin'])
    ->name('admin.user.role');

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

    // Dashbaord Video (User)
    Route::get('/video/upload', [App\Http\Controllers\VideoController::class, 'upload'])
        ->name('video.upload');
        
    Route::get('/video/create', [App\Http\Controllers\VideoController::class, 'create'])
        ->name('video.create');
        
    Route::post('/video/store-user', [App\Http\Controllers\VideoController::class, 'storeUser'])
        ->name('video.storeUser');

    Route::get('/video/{video}/edit', [App\Http\Controllers\VideoController::class, 'edit'])
        ->name('video.edit');
        
    Route::put('/video/{video}', [App\Http\Controllers\VideoController::class, 'update'])
        ->name('video.update');
        
    Route::delete('/video/{video}', [App\Http\Controllers\VideoController::class, 'destroy'])
        ->name('video.destroy');

    // Dashboard Audio (User)
    Route::get('/audio/upload', [App\Http\Controllers\AudioController::class, 'upload'])
        ->name('audio.upload');
        
    Route::get('/audio/create', [App\Http\Controllers\AudioController::class, 'create'])
        ->name('audio.create');
        
    Route::post('/audio/store-user', [App\Http\Controllers\AudioController::class, 'storeUser'])
        ->name('audio.storeUser');

    Route::get('/audio/{audio}/edit', [App\Http\Controllers\AudioController::class, 'edit'])
        ->name('audio.edit');
        
    Route::put('/audio/{audio}', [App\Http\Controllers\AudioController::class, 'update'])
        ->name('audio.update');
        
    Route::delete('/audio/{audio}', [App\Http\Controllers\AudioController::class, 'destroy'])
        ->name('audio.destroy');
});

Route::get('/cerita', [CeritaController::class, 'index'])->name('cerita.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/{slug}', [ForumController::class, 'show'])->name('forum.show');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::post('/forum/topic', [ForumController::class, 'storeTopic'])->name('forum.store-topic');
    Route::delete('/forum/{id}', [ForumController::class, 'destroy'])->name('forum.destroy');
    Route::delete('/forum/topic/{id}', [ForumController::class, 'destroyTopic'])->name('forum.destroy-topic');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Halaman daftar user
    Route::get('/users', [UserController::class, 'index'])
        ->name('admin.users.index');

    // (OPSIONAL) Update role user
    Route::post('/users/{id}/role', [UserController::class, 'updateRole'])
        ->name('admin.users.updateRole');

    // Hapus user
    Route::delete('/users/{id}', [UserController::class, 'destroy'])
        ->name('admin.users.destroy');

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

        Route::get('/cerita/{id}/edit', [AdminCeritaController::class, 'edit'])
            ->name('edit');

        Route::put('/cerita/{id}', [AdminCeritaController::class, 'update'])
            ->name('update');
            
        Route::delete('/cerita/{id}', [AdminCeritaController::class, 'destroy'])
            ->name('destroy');
    });

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.video.')
    ->group(function () {
        Route::get('/video', [\App\Http\Controllers\Admin\VideoController::class, 'index'])->name('index');
        Route::get('/video/create', [\App\Http\Controllers\Admin\VideoController::class, 'create'])->name('create');
        Route::post('/video/store', [\App\Http\Controllers\Admin\VideoController::class, 'store'])->name('store');
        Route::get('/video/{id}/edit', [\App\Http\Controllers\Admin\VideoController::class, 'edit'])->name('edit');
        Route::put('/video/{id}', [\App\Http\Controllers\Admin\VideoController::class, 'update'])->name('update');
        Route::put('/video/{id}/status', [\App\Http\Controllers\Admin\VideoController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/video/{id}', [\App\Http\Controllers\Admin\VideoController::class, 'destroy'])->name('destroy');
    });

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.audio.')
    ->group(function () {
        Route::get('/audio', [\App\Http\Controllers\Admin\AudioController::class, 'index'])->name('index');
        Route::get('/audio/create', [\App\Http\Controllers\Admin\AudioController::class, 'create'])->name('create');
        Route::post('/audio/store', [\App\Http\Controllers\Admin\AudioController::class, 'store'])->name('store');
        Route::get('/audio/{id}/edit', [\App\Http\Controllers\Admin\AudioController::class, 'edit'])->name('edit');
        Route::put('/audio/{id}', [\App\Http\Controllers\Admin\AudioController::class, 'update'])->name('update');
        Route::put('/audio/{id}/status', [\App\Http\Controllers\Admin\AudioController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/audio/{id}', [\App\Http\Controllers\Admin\AudioController::class, 'destroy'])->name('destroy');
    });

    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/forum', [ForumAdminController::class, 'index'])->name('admin.forum.index');
    Route::post('/forum', [ForumAdminController::class, 'store'])->name('admin.forum.store');
    Route::patch('/forum/topics/{id}/approve', [ForumAdminController::class, 'approve'])->name('admin.forum.approve');
    Route::patch('/forum/topics/{id}/reject',  [ForumAdminController::class, 'reject'])->name('admin.forum.reject');
    Route::delete('/forum/topics/{id}',        [ForumAdminController::class, 'destroy'])->name('admin.forum.destroy');
});

Route::middleware(['auth','admin'])
->prefix('admin')
->group(function(){

    Route::get('/quiz/index', [QuizController::class,'index'])
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

Route::get('/quiz', [QuizPlayController::class, 'index'])->name('quiz.index');

Route::middleware(['auth'])->group(function () {

    Route::get('/quiz/play', [QuizPlayController::class, 'start'])->name('quiz.play');

    Route::get('/quiz/start', [QuizPlayController::class, 'start'])
        ->name('quiz.start');

    Route::post('/quiz/submit', [QuizPlayController::class, 'submit'])
        ->name('quiz.submit');

    Route::get('/quiz/result', [QuizPlayController::class, 'result'])
        ->name('quiz.result');

});

/*
| DASHBOARD NARASUMBER & CERITA
*/
Route::middleware(['auth', 'narasumber'])
    ->prefix('narasumber')
    ->name('narasumber.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Narasumber\DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/cerita', [\App\Http\Controllers\Narasumber\CeritaController::class, 'index'])->name('cerita.index');
        Route::get('/cerita/{id}', [\App\Http\Controllers\Narasumber\CeritaController::class, 'show'])->name('cerita.show');
        Route::get('/cerita/{id}/edit', [\App\Http\Controllers\Narasumber\CeritaController::class, 'edit'])->name('cerita.edit');
        Route::put('/cerita/{id}', [\App\Http\Controllers\Narasumber\CeritaController::class, 'update'])->name('cerita.update');
        Route::put('/cerita/{id}/status', [\App\Http\Controllers\Narasumber\CeritaController::class, 'updateStatus'])->name('cerita.updateStatus');
        Route::delete('/cerita/{id}', [\App\Http\Controllers\Narasumber\CeritaController::class, 'destroy'])->name('cerita.destroy');

        Route::get('/video', [\App\Http\Controllers\Narasumber\VideoController::class, 'index'])->name('video.index');
        Route::get('/video/create', [\App\Http\Controllers\Narasumber\VideoController::class, 'create'])->name('video.create');
        Route::post('/video/store', [\App\Http\Controllers\Narasumber\VideoController::class, 'store'])->name('video.store');
        Route::get('/video/{id}/edit', [\App\Http\Controllers\Narasumber\VideoController::class, 'edit'])->name('video.edit');
        Route::put('/video/{id}', [\App\Http\Controllers\Narasumber\VideoController::class, 'update'])->name('video.update');
        Route::put('/video/{id}/status', [\App\Http\Controllers\Narasumber\VideoController::class, 'updateStatus'])->name('video.updateStatus');
        Route::delete('/video/{id}', [\App\Http\Controllers\Narasumber\VideoController::class, 'destroy'])->name('video.destroy');

        Route::get('/audio', [\App\Http\Controllers\Narasumber\AudioController::class, 'index'])->name('audio.index');
        Route::get('/audio/create', [\App\Http\Controllers\Narasumber\AudioController::class, 'create'])->name('audio.create');
        Route::post('/audio/store', [\App\Http\Controllers\Narasumber\AudioController::class, 'store'])->name('audio.store');
        Route::get('/audio/{id}/edit', [\App\Http\Controllers\Narasumber\AudioController::class, 'edit'])->name('audio.edit');
        Route::put('/audio/{id}', [\App\Http\Controllers\Narasumber\AudioController::class, 'update'])->name('audio.update');
        Route::put('/audio/{id}/status', [\App\Http\Controllers\Narasumber\AudioController::class, 'updateStatus'])->name('audio.updateStatus');
        Route::delete('/audio/{id}', [\App\Http\Controllers\Narasumber\AudioController::class, 'destroy'])->name('audio.destroy');
    });

//AUTH ROUTES
require __DIR__.'/auth.php';
