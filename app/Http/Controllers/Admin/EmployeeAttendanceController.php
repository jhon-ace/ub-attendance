<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Admin\School; 
use \App\Models\Admin\Department;
use \App\Models\Admin\Employee;
use \App\Models\Admin\EmployeeAttendanceTimeIn;
use \App\Models\Admin\EmployeeAttendanceTimeOut;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

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
        return view('attendance_time_in');
    }
    // Adjust this according to your User model namespace

public function openPortal(Request $request)
{
    // Hardcoded credentials for validation (not from user input)
    $validEmail = 'jacasabuena@cec.edu.ph';
    $validPassword = 'administrator';

    // Validate incoming request data
    $request->validate([
        'employee_rfid' => 'required',
    ]);

    // Attempt to retrieve user from database based on hardcoded email
    $user = User::where('email', $validEmail)->first();

    // Check if retrieved user exists and validate password
    if ($user) {
        // Use the check method on the retrieved user's hashed password
        if (password_verify($validPassword, $user->password)) {
            // Check if user has admin role (assuming you have a hasRole method)
            if ($user->hasRole('admin')) {
                // Check if employee with the specified RFID exists
                $employeeRfid = $request->input('employee_rfid');
                $employees = Employee::where('employee_rfid', $employeeRfid)->get();
                $employees2 = Employee::where('employee_rfid', $employeeRfid)->first();
                
                if ($employees2) {
                    // Insert attendance record
                    $status ="Inside the campus";

                    $attendance = new EmployeeAttendanceTimeIn();
                    $attendance->employee_id = $employees2->id;
                    $attendance->check_in_time = Carbon::now(); // Current timestamp
                    $attendance->status = $status; 
                    $attendance->save();

                    return view('attendance-profile_time_in', compact('employees'));
                } else {
                    return redirect()->route('attendance.portal')->with('error', 'Employee not found.');
                }
            } else {
                return redirect()->back()->with('error', 'Unauthorized access.');
            }
        } else {
            // Invalid password
            return redirect()->back()->with('error', 'Invalid email or password.');
        }
    } else {
        // User not found
        return redirect()->back()->with('error', 'Invalid email or password.');
    }
}


        // $employeeRfid = $request->input('employee_rfid');
        // $employees = Employee::where('employee_rfid', $employeeRfid)->get();
        
        // if($employees->isNotEmpty()) {
        //     return redirect()->route('admin.attendance.employee_attendance.portal')->with('success', 'Employee found successfully.');
        // } else {
        //     return redirect()->route('admin.attendance.employee_attendance.portal')->with('error', 'Employee not found.');
        // }
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
