<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AttendanceController;


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
        Route::delete('school', [SchoolController::class, 'deleteAll'])->name('school.deleteAll');
        
        //department routes
        Route::resource('department', DepartmentController::class)->names([
            'index' => 'department.index',
            'create' => 'department.create',
            'store' => 'department.store',
            'edit' => 'department.edit',
            'update' => 'department.update'
        ]);
        Route::delete('department', [DepartmentController::class, 'deleteAll'])->name('department.deleteAll');

        //staff routes
        Route::resource('staff', AdminStaffController::class)->names([
            'index' => 'staff.index',
            'create' => 'staff.create',
            'store' => 'staff.store',
            'edit' => 'staff.edit',
            'update' => 'staff.update'
        ]);
        Route::delete('staff', [AdminStaffController::class, 'deleteAll'])->name('staff.deleteAll');

        // Employee routes
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employee.index');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employee.store');
        Route::get('/employees/{employee_id}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
        Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employee.update');
        Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');
        Route::delete('employee', [EmployeeController::class, 'deleteAll'])->name('employee.deleteAll');

        // Student routes
        Route::resource('student', StudentController::class)->names([
            'index' => 'student.index',
            'create' => 'student.create',
            'store' => 'student.store',
            'edit' => 'student.edit',
            'update' => 'student.update'
        ]);
        Route::delete('student', [StudentController::class, 'deleteAll'])->name('student.deleteAll');


        // Attendance routes
        Route::resource('attendance', AttendanceController::class)->names([
            'index' => 'attendance.index',
            'create' => 'attendance.create',
            'store' => 'attendance.store',
            'edit' => 'attendance.edit',
            'update' => 'attendance.update'
        ]);
        Route::delete('attendance', [AttendanceController::class, 'deleteAll'])->name('attendance.deleteAll');







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
