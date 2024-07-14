<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\EmployeeAttendanceController;


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
        
        // course routes
        Route::get('/courses', [CourseController::class, 'index'])->name('course.index');
        Route::post('/courses', [CourseController::class, 'store'])->name('course.store');
        Route::get('/courses/{course_id}/edit', [CourseController::class, 'edit'])->name('course.edit');
        Route::put('courses/{id}', [CourseController::class, 'update'])->name('course.update');
        Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('course.destroy');
        Route::delete('courses', [CourseController::class, 'deleteAll'])->name('course.deleteAll');

        // Employee routes
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employee.index');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employee.store');
        // Route::get('/employees/{employee_id}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
        Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employee.update');
        Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');
        Route::delete('employee', [EmployeeController::class, 'deleteAll'])->name('employee.deleteAll');

        // Employee Attendance routes
        Route::get('/employees/attendance', [EmployeeAttendanceController::class, 'index'])->name('attendance.employee_attendance');
        Route::get('/generate-pdf', [EmployeeAttendanceController::class, 'generatePDF'])->name('generate.pdf');
        
        Route::get('/attendance/time-in/portal', [EmployeeAttendanceController::class, 'portalTimeIn'])->name('attendance.time-in.portal');
        Route::get('/attendance/time-out/portal', [EmployeeAttendanceController::class, 'portalTimeOut'])->name('attendance.time-out.portal');

        Route::post('/attendance/time-in/portal', [EmployeeAttendanceController::class, 'submitPortalTimeIn'])->name('attendance.time-in.store');
        Route::post('/attendance/time-out/portal', [EmployeeAttendanceController::class, 'submitPortalTimeOut'])->name('attendance.time-out.store');       

        // Student routes
        Route::get('/students', [StudentController::class, 'index'])->name('student.index');
        Route::post('/students', [StudentController::class, 'store'])->name('student.store');
        // Route::get('/students/{student_id}/edit', [StudentController::class, 'edit'])->name('student.edit');
        Route::put('/students/{id}', [StudentController::class, 'update'])->name('student.update');
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('student.destroy');
        Route::delete('students', [StudentController::class, 'deleteAll'])->name('student.deleteAll');

        
        //staff routes
        Route::resource('staff', AdminStaffController::class)->names([
            'index' => 'staff.index',
            'create' => 'staff.create',
            'store' => 'staff.store',
            'edit' => 'staff.edit',
            'update' => 'staff.update'
        ]);
        Route::delete('staff', [AdminStaffController::class, 'deleteAll'])->name('staff.deleteAll');





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
