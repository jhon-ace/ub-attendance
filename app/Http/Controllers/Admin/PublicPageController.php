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

class PublicPageController extends Controller
{


     public function portalTimeIn()
    {
        // Retrieve any user with the role 'admin'
        $adminUser = User::where('role', 'admin')->first();

        // Check if there is an admin user
        if ($adminUser) {
            $current_date = now()->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d');

            // Retrieve attendance data for the current date
            $curdateDataIn = EmployeeAttendanceTimeIn::whereDate('check_in_time', $current_date)->get();
            $curdateDataOut = EmployeeAttendanceTimeOut::whereDate('check_out_time', $current_date)->get();

            // Return view with the attendance data
            return view('attendance_time_in', compact('curdateDataIn', 'curdateDataOut'));
        }

        // Redirect with an error message if no admin user is found
        return redirect()->back()->with('error', 'No admin user found.');
    }

    public function submitAttendance(Request $request)
    {
        $request->validate([
            'user_rfid' => 'required|string|max:255',
        ]);

            $adminUser = User::where('role', 'admin')->first();
            
            if($adminUser)
            {
                $rfid = $request->input('user_rfid');
                
                // Query to get the employee based on the RFID
                $employee = Employee::where('employee_rfid', $rfid)->first();
                // Query to get the employee based on the RFID for compact
                $employees = Employee::where('employee_rfid', $rfid)->get();
                    
                if ($employee) {
                    // Get the current datetime in Kuala Lumpur timezone
                    $now = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));

                    // Format datetime for database insertion
                    $formattedDateTime = $now->format('Y-m-d H:i:s');

                    // Get the count of time-in records for today
                    $timeInCount = EmployeeAttendanceTimeIn::where('employee_id', $employee->id)
                        ->whereDate('check_in_time', $now->format('Y-m-d'))
                        ->count();

                    // Get the first time-in record for today
                    $firstTimeIn = EmployeeAttendanceTimeIn::where('employee_id', $employee->id)
                        ->whereDate('check_in_time', $now->format('Y-m-d'))
                        ->first();

                    // Get the count of time-out records for today
                    $timeOutCount = EmployeeAttendanceTimeOut::where('employee_id', $employee->id)
                        ->whereDate('check_out_time', $now->format('Y-m-d'))
                        ->count();

                    // Get the first time-in record for today
                    $firstTimeOut = EmployeeAttendanceTimeOut::where('employee_id', $employee->id)
                        ->whereDate('check_out_time', $now->format('Y-m-d'))
                        ->first();

                    // Get the last time-in record for today
                    $lastTimeIn = EmployeeAttendanceTimeIn::where('employee_id', $employee->id)
                        ->whereDate('check_in_time', $now->format('Y-m-d'))
                        ->latest('check_in_time')
                        ->first();

                        

                    $intervalAllowed = false;
                    // Check interval for first check-out
                    if ($firstTimeIn) {
                        $checkInTime = new DateTime($firstTimeIn->check_in_time, new DateTimeZone('Asia/Kuala_Lumpur'));
                        $interval = $now->diff($checkInTime);
                        $minutes = $interval->i + ($interval->h * 60);
                        if ($minutes >= 45) {
                            $intervalAllowed = true;
                        }
                    }

                    // Check if the employee has already checked out in the afternoon
                    if ($timeInCount == 2 && $timeOutCount == 2) {

                        return redirect()->route('attendance.portal')->with('success', 'Attendance complete. Safe travels home!');
                        // return response()->json([
                        //     'message' => 'Already checked out in the afternoon. Go home safely!',
                        // ], 403);
                    }
                    else if ($timeInCount == 1 && $timeOutCount == 1) {
                            
                        $intervalAllowed = false;

                        if ($firstTimeOut) {
                            $checkOutTime = new DateTime($firstTimeOut->check_out_time, new DateTimeZone('Asia/Kuala_Lumpur'));
                            $interval = $now->diff($checkOutTime);
                            $minutes = $interval->i + ($interval->h * 60);
                            if ($minutes >= 45) {
                                $intervalAllowed = true;
                            }
                        }

                        if ($intervalAllowed) 
                        {
                            // Second time-in (PM), no second check-out
                            $attendanceIn = new EmployeeAttendanceTimeIn();
                            $attendanceIn->employee_id = $employee->id;
                            $attendanceIn->check_in_time = $formattedDateTime; // Store formatted datetime
                            $attendanceIn->status = "On-campus";
                            $attendanceIn->save();

                            // return response()->json([
                            //     'message' => 'PM Time-in recorded successfully.',
                            //     'employee' => $employee,
                            //     'check_in_time' => $formattedDateTime,
                            // ], 200);
                            return view('attendance-profile_time_in_employee', compact('employees'));
                        } else {
                            // return response()->json([
                            //     'message' => 'Already Check-out in morning! Afternoon - Check-In not allowed yet. Please wait 45 minutes after check-out.',
                            // ], 403);
                            // return redirect()->route('admin.attendance.time-in.portal')->with('error', 'Already Check-out in morning! Afternoon - Check-In not allowed yet. Please wait 45 minutes after check-out!');
                            return redirect()->route('attendance.portal')->with('error', 'Please wait 45 minutes for Afternoon time in!');
                        }

                    } elseif ($timeInCount == 1 && $timeOutCount == 0) {
                        
                        if ($intervalAllowed) 
                        {
                            // First check-out (AM)
                            $attendanceOut = new EmployeeAttendanceTimeOut();
                            $attendanceOut->employee_id = $employee->id;
                            $attendanceOut->check_out_time = $formattedDateTime; // Store formatted datetime
                            $attendanceOut->status = "Outside Campus";
                            $attendanceOut->save();

                            // return response()->json([
                            //     'message' => 'AM Time-out recorded successfully.',
                            //     'employee' => $employee,
                            //     'check_out_time' => $formattedDateTime,
                            // ], 200);
                            return view('attendance-profile_time_out_employee', compact('employees'));
                        } else {
                            // return response()->json([
                            //     'message' => 'Already Time In Morning.',
                            // ], 403);
                            return redirect()->route('attendance.portal')->with('success', 'Already Time In!');
                        }

                    } elseif ($timeInCount == 2 && $timeOutCount == 1) {
                            // Check interval for second check-out
                        $intervalAllowed = false;
                        if ($lastTimeIn) {
                            $checkInTime = new DateTime($lastTimeIn->check_in_time, new DateTimeZone('Asia/Kuala_Lumpur'));
                            $interval = $now->diff($checkInTime);
                            $minutes = $interval->i + ($interval->h * 60);
                            if ($minutes >= 45) {
                                $intervalAllowed = true;
                            }
                        }

                        if ($intervalAllowed) {
                            // Second check-out (PM)
                            $attendanceOut = new EmployeeAttendanceTimeOut();
                            $attendanceOut->employee_id = $employee->id;
                            $attendanceOut->check_out_time = $formattedDateTime; // Store formatted datetime
                            $attendanceOut->status = "Outside Campus";
                            $attendanceOut->save();

                            // return response()->json([
                            //     'message' => 'PM Time-out recorded successfully.',
                            //     'employee' => $employee,
                            //     'check_out_time' => $formattedDateTime,
                            // ], 200);
                            return view('attendance-profile_time_out_employee', compact('employees'));
                        } else {
                            // return response()->json([
                            //     'message' => 'Already Time-in Afternoon.',
                            // ], 403);
                            return redirect()->route('attendance.portal')->with('success', 'Already Time In!');
                        }

                    } else {
                        // First time-in (AM)
                        $attendanceIn = new EmployeeAttendanceTimeIn();
                        $attendanceIn->employee_id = $employee->id;
                        $attendanceIn->check_in_time = $formattedDateTime; // Store formatted datetime
                        $attendanceIn->status = "On-campus";
                        $attendanceIn->save();

                        return view('attendance-profile_time_in_employee', compact('employees'));
                        // return response()->json([
                        //     'message' => 'AM Time-in recorded successfully.',
                        //     'employee' => $employee,
                        //     'check_in_time' => $formattedDateTime,
                        // ], 200);
                    }
                } else {
                    // Handle case where employee with given RFID is not found
                    // return response()->json(['error' => 'Employee not found.'], 404);
                    return redirect()->route('attendance.portal')->with('error', 'RFID not Recognized!');
                }
            } else {
                return redirect()->back()->with('error', 'Unauthorized access.');
            }
       
    }
}