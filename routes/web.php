<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialPracticeController;
use App\Http\Controllers\PostTestController;
use App\Http\Controllers\PreTestController;
use App\Http\Controllers\TesController;
use App\Http\Middleware\EnsurePreTestCompleted;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('pre-test')->name('pre-test.')->controller(PreTestController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('mulai', 'start')->name('start');
        Route::get('kerjakan', 'take')->name('take');
        Route::post('kirim', 'submit')->name('submit');
        Route::get('hasil', 'result')->name('result');
    });

    Route::middleware(EnsurePreTestCompleted::class)->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        Route::get('materi', [MaterialController::class, 'index'])->name('materi.index');
        Route::get('materi/{material:slug}', [MaterialController::class, 'show'])->name('materi.show');
        Route::post('materi/{material:slug}/selesai', [MaterialController::class, 'complete'])->name('materi.complete');

        Route::get('tes-latihan', TesController::class)->name('tes.index');

        Route::prefix('materi/{material:slug}/latihan')->name('materi.latihan.')->controller(MaterialPracticeController::class)->group(function () {
            Route::get('/', 'take')->name('take');
            Route::post('mulai', 'start')->name('start');
            Route::post('jawab', 'answer')->name('answer');
            Route::post('selesai', 'finish')->name('finish');
            Route::get('hasil/{attempt}', 'result')->name('result');
        });

        Route::prefix('post-test')->name('post-test.')->controller(PostTestController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('mulai', 'start')->name('start');
            Route::get('kerjakan', 'take')->name('take');
            Route::post('kirim', 'submit')->name('submit');
            Route::get('hasil', 'result')->name('result');
        });
    });

    Route::middleware(EnsureUserIsAdmin::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::resource('pengguna', Admin\UserController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['pengguna' => 'user'])
            ->names('users');

        Route::resource('topik', Admin\TopicController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->parameters(['topik' => 'topic'])
            ->names('topics');

        Route::resource('soal', Admin\QuestionController::class)
            ->except('show')
            ->parameters(['soal' => 'question'])
            ->names('questions');

        Route::get('penilaian', [Admin\ScoreCriterionController::class, 'index'])->name('criteria.index');
        Route::post('penilaian', [Admin\ScoreCriterionController::class, 'store'])->name('criteria.store');
        Route::put('penilaian/{criterion}', [Admin\ScoreCriterionController::class, 'update'])->name('criteria.update');
        Route::delete('penilaian/{criterion}', [Admin\ScoreCriterionController::class, 'destroy'])->name('criteria.destroy');
        Route::get('tampilan', [Admin\AppearanceController::class, 'index'])->name('appearance.index');
        Route::put('tampilan', [Admin\AppearanceController::class, 'update'])->name('appearance.update');
        Route::put('pengaturan-pre-test', [Admin\ScoreCriterionController::class, 'updateSettings'])->name('settings.update');

        Route::get('nilai', [Admin\ScoreController::class, 'index'])->name('scores.index');
        Route::post('nilai', [Admin\ScoreController::class, 'store'])->name('scores.store');
        Route::put('nilai/{attempt}', [Admin\ScoreController::class, 'update'])->name('scores.update');
        Route::delete('nilai/{attempt}', [Admin\ScoreController::class, 'destroy'])->name('scores.destroy');
    });
});

require __DIR__.'/settings.php';
