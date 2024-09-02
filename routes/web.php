<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->hasRole('Team Member')) {
        return redirect('/task');
    } elseif ($user->hasAnyRole(['Admin', 'Project Manager'])) {
        return redirect('/projects');
    } else {
        return view('waiting-for-approval');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('/projects')->group(function () {
        Route::get('', [ProjectController::class, 'projectList'])->name('projects.list');
        Route::post('', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/{id}', [ProjectController::class, 'getProjectById'])->name('projects.edit')->where('id', '[0-9]+');
        Route::put('/{id}', [ProjectController::class, 'updateProjectById'])->name('projects.update')->where('id', '[0-9]+');
        Route::delete('/{id}', [ProjectController::class, 'deleteProjectById'])->name('projects.delete')->where('id', '[0-9]+');
        Route::get('/{id}/team-members', [ProjectController::class, 'getTeamMembers'])->name('projects.team-members')->where('id', '[0-9]+');
    });

    Route::prefix('/task')->group(function () {
        Route::get('', [TaskController::class, 'taskList'])->name('task.list');
        Route::post('', [TaskController::class, 'store'])->name('task.store');
        Route::get('/{id}', [TaskController::class, 'getTaskById'])->name('task.edit')->where('id', '[0-9]+');
        Route::put('/{id}', [TaskController::class, 'updateTaskById'])->name('task.update')->where('id', '[0-9]+');
        Route::patch('status/{id}', [TaskController::class, 'updateTaskStatus'])->name('task.status')->where('id', '[0-9]+');
        Route::delete('/{id}', [TaskController::class, 'deleteTaskById'])->name('task.delete')->where('id', '[0-9]+');
    });

    Route::prefix('/users')->group(function () {
        Route::get('', [UserController::class, 'userList'])->name('users.list');
        Route::patch('role/{id}', [UserController::class, 'updateUserRole'])->name('users.role')->where('id', '[0-9]+');
        Route::delete('/{id}', [UserController::class, 'deleteUserById'])->name('users.delete')->where('id', '[0-9]+');
    });
    
});

require __DIR__.'/auth.php';