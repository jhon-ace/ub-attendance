<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Admin\School; 
use \App\Models\Admin\Department;
use \App\Models\Admin\Employee;
use \App\Models\Admin\EmployeeAttendanceTimeIn;
use \App\Models\Admin\EmployeeAttendanceTimeOut;

class EmployeeAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Admin.attendance.employee_attendance');
    }

    public function portal()
    {
        return view('Admin.attendance.employee_attendance_portal');
    }
    public function portalSubmit(Request $request)
    {
        $employeeRfid = $request->input('employee_rfid');
        $employees = Employee::where('employee_rfid', $employeeRfid)->get();
        if($employees)
            return redirect()->route('admin.attendance.employee_attendance.portal')->with('success', 'Employee get successfully.');
        else{
            return redirect()->route('admin.attendance.employee_attendance.portal')->with('error', 'Employee not found.');
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
