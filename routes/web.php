<?php

use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\AcuerdoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Livewire\Dashboard;
use App\Livewire\SeguimientoArea;
use App\Livewire\KpisAreas;

// Auth Routes
Route::get('/hello', function () {
    return "Hello World! Path: " . request()->path() . " URI: " . request()->getRequestUri();
});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de recuperación de contraseña (estandarizadas con Auth Center)
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Cambio de contraseña para usuarios autenticados
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'updateChangePassword'])->name('password.change.post');
});

Route::middleware(['auth', 'force-password', 'has-access'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/seguimiento-por-area', SeguimientoArea::class)->name('seguimiento.area');
    Route::get('/kpis-areas', KpisAreas::class)->name('kpis.areas');
    Route::get('/historico-acuerdos', [AcuerdoController::class, 'historico'])->name('acuerdos.historico');

    // Calendar Routes
    Route::get('/planeador', [EventController::class, 'index'])->name('planeador');
    Route::get('/events', [EventController::class, 'fetch'])->name('events.fetch');
    Route::post('/events/import', [EventController::class, 'import'])->name('events.import');
    Route::post('/events/import-ics', [EventController::class, 'importICS'])->name('events.import.ics');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // Users Management
    Route::resource('users', UserController::class);
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    Route::resource('acuerdos', AcuerdoController::class);
    Route::post('/acuerdos/{acuerdo}/comment', [AcuerdoController::class, 'comment'])->name('acuerdos.comment');
    Route::get('/export-acuerdos', [AcuerdoController::class, 'export'])->name('acuerdos.export');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'updatePassword'])->name('password.update');
});
