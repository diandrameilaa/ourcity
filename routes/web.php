<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\DashboardController;

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

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




 Route::middleware(['auth', 'admin'])->group(function () {
    // Admin routes, accessible only to authenticated admins
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/data', [UserController::class, 'getData'])->name('user.data');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/status', [UserController::class, 'toggleStatus'])->name('user.toggle_status');

    // Routes for projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/data', [ProjectController::class, 'getData'])->name('projects.data');
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');


    //  Notifications
    Route::resource('notifications', NotificationController::class);

});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/data', [ReportController::class, 'getData'])->name('reports.data');
    Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('reports/{id}/edit', [ReportController::class, 'edit'])->name('reports.edit');
    Route::put('reports/{id}', [ReportController::class, 'update'])->name('reports.update');

    // Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/data', [ProjectController::class, 'getData'])->name('projects.data');

    // Discussions
    Route::get('/discussions', [DiscussionController::class, 'index'])->name('discussions.index');
    Route::post('/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::delete('/discussions/{id}', [DiscussionController::class, 'destroy'])->name('discussions.destroy');
});
