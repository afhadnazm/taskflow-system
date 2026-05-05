<?php

use App\Http\Controllers\ProjectController;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Admin\ProjectManagement;
use App\Livewire\Admin\TaskManagement;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Tasks;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/admin/users', UserManagement::class)->name('admin.users');
    Route::get('/admin/projects', ProjectManagement::class)->name('admin.projects');
    Route::get('/admin/tasks', TaskManagement::class)->name('admin.tasks');

    Route::resource('projects', ProjectController::class);

    Route::get('/projects/{project}/tasks', Tasks::class)
        ->name('projects.tasks');
    Route::get('/notifications/{notification}/read', function ($notificationId) {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($notificationId);

        $notification->markAsRead();

        return redirect()->route('projects.tasks', $notification->data['project_id']);
    })->name('notifications.read');
});

require __DIR__ . '/auth.php';
