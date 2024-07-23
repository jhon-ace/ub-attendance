<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\EmployeeAttendanceTimeIn;
use App\Models\Admin\EmployeeAttendanceTimeOut;
use DateTime;
use DateTimeZone;

class SubmitMissingAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:submit-missing-attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Submit missing attendance records at midnight';

    /**
     * Execute the console command.
     */
    public function handle()
{
    // Get current time with timezone
    $now = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
    $currentDate = $now->format('Y-m-d');

    // Fetch all employees with missing check-in or check-out times
    $attendanceTimeIn = EmployeeAttendanceTimeIn::all();
    $attendanceTimeOut = EmployeeAttendanceTimeOut::all();

    // Get unique employee IDs from attendance records
    $employeeIds = $attendanceTimeIn->pluck('employee_id')->unique();

    $this->info("Processing attendance for employees...");

    foreach ($employeeIds as $employeeId) {
        $this->info("Processing employee ID: {$employeeId}");

        // Find the latest check-in and check-out times for this employee
        $lastCheckIn = EmployeeAttendanceTimeIn::where('employee_id', $employeeId)
            ->orderBy('check_in_time', 'desc')
            ->first();

        $lastCheckOut = EmployeeAttendanceTimeOut::where('employee_id', $employeeId)
            ->orderBy('check_out_time', 'desc')
            ->first();

        if ($lastCheckIn && $lastCheckOut) {
            $lastCheckInDate = new DateTime($lastCheckIn->check_in_time);
            $formattedDateIn = $lastCheckInDate->format('Y-m-d');

            $lastCheckOutDate = new DateTime($lastCheckOut->check_out_time);
            $formattedDateOut = $lastCheckOutDate->format('Y-m-d');

            if ($formattedDateIn === $formattedDateOut) {
                $nextDate = (clone $lastCheckInDate)->modify('+1 day');
                $formattedNextDate = $nextDate->format('Y-m-d');

                // $missingTimeIn = EmployeeAttendanceTimeIn::whereDate('check_in_time', $currentDate)->get();
                // $missingTimeOut = EmployeeAttendanceTimeOut::whereDate('check_out_time', $currentDate)->get();


                $missingTimeIn = EmployeeAttendanceTimeIn::whereDate('check_in_time', $currentDate)
                    ->where('employee_id', $employeeId)
                    ->get();

                // Check and create missing check-out records for this employee
                $missingTimeOut = EmployeeAttendanceTimeOut::whereDate('check_out_time', $currentDate)
                    ->where('employee_id', $employeeId)
                    ->get();


                $this->info("Check-in and check-out dates match for employee ID {$employeeId}.");

                // Check and create missing check-in records
                if ($missingTimeIn->isEmpty()) {
                    foreach (range(1, 2) as $index) {
                        $attendance = new EmployeeAttendanceTimeIn();
                        $attendance->employee_id = $employeeId;
                        $attendance->check_in_time = $currentDate;
                        $attendance->status = "Absent";
                        $attendance->save();
                        $this->info("Created missing check-in record for employee ID {$employeeId}.");
                    }
                } else {
                    $this->info("Check-in record already exists for employee ID {$employeeId}.");
                }

                // Check and create missing check-out records
                if ($missingTimeOut->isEmpty()) {
                    foreach (range(1, 2) as $index) {
                        $attendance = new EmployeeAttendanceTimeOut();
                        $attendance->employee_id = $employeeId;
                        $attendance->check_out_time = $currentDate;
                        $attendance->status = "Absent";
                        $attendance->save();
                        $this->info("Created missing check-out record for employee ID {$employeeId}.");
                    }
                } else {
                    $this->info("Check-out record already exists for employee ID {$employeeId}.");
                }
            } else {
                $this->info("Check-in and check-out dates do not match for employee ID {$employeeId}.");
            }
        } else {
            $this->info("Missing check-in or check-out records for employee ID {$employeeId}.");
        }
    }

    $this->info("Attendance processing complete.");
}


    
}

    //     public function handle()
    // {
    //     // Get current time with timezone
    //     $now = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));
    //     $currentDate = $now->format('Y-m-d');
        
    //     // Fetch all employees with missing check-in or check-out times
    //     $attendanceTimeIn = EmployeeAttendanceTimeIn::all();
    //     $attendanceTimeOut = EmployeeAttendanceTimeOut::all();

    //     // Process each employee ID
    //     foreach ($attendanceTimeIn->unique('employee_id') as $attendanceIn) {
    //         $employeeId = $attendanceIn->employee_id;
            
    //         // Find the latest check-in time for this employee
    //         $lastCheckIn = EmployeeAttendanceTimeIn::where('employee_id', $employeeId)
    //             ->orderBy('check_in_time', 'desc')
    //             ->first();

    //             $lastCheckOut = EmployeeAttendanceTimeOut::where('employee_id', $employeeId)
    //             ->orderBy('check_out_time', 'desc')
    //             ->first();
              
    //         if ($lastCheckIn && $lastCheckOut) {
    //             // Convert check_in_time to DateTime object if it's not already
                
    //             $lastCheckInDate = new DateTime($lastCheckIn->check_in_time);
    //             $formattedDateIn = $lastCheckInDate->format('Y-m-d');

    //             $lastCheckOutDate = new DateTime($lastCheckOut->check_out_time);
    //             $formattedDateOut = $lastCheckOutDate->format('Y-m-d');
                
                
    //             // Check for corresponding check-out records for this employee
    //             if($formattedDateIn ===  $formattedDateOut)
    //             {

                        
    //                     $lastCheckInDatee = new DateTime($lastCheckIn->check_in_time);
    //                     // Increment the date by one day
    //                     $lastCheckInDatee->modify('+1 day');

    //                     // Format the new date
    //                     $formattedDateInn = $lastCheckInDatee->format('Y-m-d');


    //                     $missingTimeIn = EmployeeAttendanceTimeIn::whereDate('check_in_time', $formattedDateInn)
    //                         ->get();
        
                        
    //                     $missingTimeOut = EmployeeAttendanceTimeOut::whereDate('check_out_time', $currentDate)
    //                         // ->whereNull('check_out_time')
    //                         ->get();


    //                     if ($missingTimeIn->isEmpty()) {
    //                         foreach (range(1, 2) as $index) { // Loop twice to create two records
    //                             $attendance = new EmployeeAttendanceTimeIn();
    //                             $attendance->employee_id = $attendanceIn->employee_id;
    //                             $attendance->check_in_time = $formattedDateInn;
    //                             $attendance->status = "Absent";
    //                             $attendance->save();
    //                             $this->info("Updated check-in for employee ID {$attendance->employee_id}.");
    //                         }
    //                     }

    //                     if ($missingTimeOut->isEmpty()) {
    //                         foreach (range(1, 2) as $index) { // Loop twice to create two records
    //                             $attendance = new EmployeeAttendanceTimeOut();
    //                             $attendance->employee_id = $attendanceIn->employee_id;
    //                             $attendance->check_out_time = $formattedDateInn;
    //                             $attendance->status = "Absent";
    //                             $attendance->save();
    //                             $this->info("Updated check-out for employee ID {$attendance->employee_id}.");
    //                         }
    //                     }

    //             }

    //         }
    //     }
    // }
    
//}
