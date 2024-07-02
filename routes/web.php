<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\SchoolController;

Route::get('/', function () {
    return view('welcome');
});
// Admin Routes
Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        //dashboard
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        //school  routes
        Route::resource('school', SchoolController::class)->names([
            'index' => 'school.index',
            'create' => 'school.create',
            'store' => 'school.store',
            'edit' => 'school.edit',
            'update' => 'school.update'
        ]);
        Route::delete('school', [SchoolController::class, 'deleteSelected'])->name('school.deleteSelected');
        
        //staff routes
        Route::resource('staff', AdminStaffController::class)->names([
            'index' => 'staff.index',
            'create' => 'Staff.create',
            'store' => 'staff.store',
            'edit' => 'staff.edit',
            'update' => 'staff.update'
        ]);










    });

});
// End of Admin routes

// Employee Routes
Route::middleware(['auth', 'verified'])->prefix('employee')->name('employee.')->group(function () {

    Route::middleware(['role:employee'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
    });

});
// End of Employee Routes

// Route::middleware(['role:student'])->group(function () {
    //     // Student routes
    // });
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
