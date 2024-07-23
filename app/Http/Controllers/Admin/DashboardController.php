<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Admin\School; 
use \App\Models\Admin\Department;
use \App\Models\Admin\Employee;
use \App\Models\Admin\Student;
use \App\Models\Admin\EmployeeAttendanceTimeIn;
use \App\Models\Admin\EmployeeAttendanceTimeOut;
use \App\Models\Admin\StudentAttendanceTimeIn;
use \App\Models\Admin\StudentAttendanceTimeOut;
use \App\Models\Admin\DepartmentWorkingHour;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class DashboardController extends Controller
{
    public function index()
    {
        // Retrieve any user with the role 'admin'
        // $adminUser = User::where('role', 'admin')->first();

        // // Check if there is an admin user
        // if ($adminUser) {

            $current_date = now()->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d');

            // Retrieve attendance data for the current date
            $curdateDataIn = EmployeeAttendanceTimeIn::whereDate('check_in_time', $current_date)->get();
            $curdateDataOut = EmployeeAttendanceTimeOut::whereDate('check_out_time', $current_date)->get();

            // Return view with the attendance data
            return view('dashboard', compact('curdateDataIn', 'curdateDataOut'));
        // }

        // Redirect with an error message if no admin user is found
        return redirect()->back()->with('error', 'No admin user found.');
    }
}
