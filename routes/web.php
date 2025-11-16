<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjetController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\EpicController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\KanbanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', ProjetController::class)
    ->parameters(['projects' => 'projet']);

    Route::get('/p/{token}', [ProjetController::class, 'shared'])->name('projects.shared');

    Route::prefix('projects/{projet}')->group(function () {
        Route::get('/sprints/create', [SprintController::class, 'create'])->name('projects.sprints.create');
        Route::post('/sprints', [SprintController::class, 'store'])->name('projects.sprints.store');

        Route::get('/epics/create', [EpicController::class, 'create'])->name('projects.epics.create');
        Route::post('/epics', [EpicController::class, 'store'])->name('projects.epics.store');

        Route::get('/tasks/create', [TaskController::class, 'create'])->name('projects.tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');

        Route::get('/proposals', [TaskController::class, 'proposalsIndex'])->name('projects.proposals.index');
        Route::post('/proposals', [TaskController::class, 'propose'])->name('projects.proposals.store');
    });

    Route::post('/proposals/{tache_proposee}/approve', [TaskController::class, 'approveProposal'])
        ->name('proposals.approve');
    Route::post('/proposals/{tache_proposee}/reject', [TaskController::class, 'rejectProposal'])
        ->name('proposals.reject');

    Route::patch('/sprints/{sprint}/inline', [SprintController::class, 'inline'])->name('sprints.inline');
    Route::delete('/sprints/{sprint}', [SprintController::class, 'destroy'])->name('sprints.destroy');

    Route::patch('/epics/{epic}/inline', [EpicController::class, 'inline'])->name('epics.inline');
    Route::delete('/epics/{epic}', [EpicController::class, 'destroy'])->name('epics.destroy');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

    Route::patch('/tasks/{tache}/inline', [TaskController::class, 'inline'])->name('tasks.inline');
    Route::delete('/tasks/{tache}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/roadmap', [RoadmapController::class, 'index'])->name('roadmap.index');
});

require __DIR__.'/auth.php';
