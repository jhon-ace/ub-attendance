<?php

namespace App\Exports;

use App\Models\Admin\EmployeeAttendanceTimeIn;
use App\Models\Admin\EmployeeAttendanceTimeOut;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class AttendanceExport implements FromCollection, WithHeadings, WithColumnWidths
{
    protected $attendanceTimeIn;
    protected $attendanceTimeOut;

    public function __construct($attendanceTimeIn, $attendanceTimeOut)
    {
        $this->attendanceTimeIn = $attendanceTimeIn;
        $this->attendanceTimeOut = $attendanceTimeOut;
    }

    public function collection()
    {
        $data = collect();

        foreach ($this->attendanceTimeIn as $timeIn) {
            $employeeId = $timeIn->employee_id;
            $lastname = $timeIn->employee->employee_lastname;
            $firstname = $timeIn->employee->employee_firstname;
            $middlename = $timeIn->employee->employee_middlename;
            $fullName = "{$lastname}, {$firstname} {$middlename}";
            
            $timeOut = $this->attendanceTimeOut->where('employee_id', $employeeId)
                                                ->where('check_out_time', '>=', $timeIn->check_in_time)
                                                ->first();

            // Calculate late duration and total hours for AM and PM shifts
            $lateDurationAM = $this->calculateLateDuration($timeIn, 'AM');
            $lateDurationPM = $this->calculateLateDuration($timeIn, 'PM');
            
            $totalHoursAM = $this->calculateTotalHours($timeIn, 'AM');
            $totalHoursPM = $this->calculateTotalHours($timeIn, 'PM');

            $data->push([
                'employee_name' => $fullName,
                'check_in_time' => $timeIn->check_in_time,
                'check_out_time' => $timeOut ? $timeOut->check_out_time : 'N/A',
                'late_duration_am' => $lateDurationAM,
                'late_duration_pm' => $lateDurationPM,
                'total_hours_am' => $totalHoursAM,
                'total_hours_pm' => $totalHoursPM,
                // Add other relevant columns as needed
            ]);
        }

        return $data;
    }

    protected function calculateLateDuration($timeIn, $shift)
    {
        $shiftStart = $this->getShiftStartTime($timeIn->employee, $shift);
        $checkInTime = Carbon::parse($timeIn->check_in_time);

        if ($checkInTime->gt($shiftStart)) {
            $lateDuration = $checkInTime->diffInMinutes($shiftStart);
        } else {
            $lateDuration = 0;
        }

        return $lateDuration;
    }

    protected function calculateTotalHours($timeIn, $shift)
    {
        $timeOut = $this->attendanceTimeOut->where('employee_id', $timeIn->employee_id)
                                            ->where('check_out_time', '>=', $timeIn->check_in_time)
                                            ->first();

        if ($timeOut) {
            $checkInTime = Carbon::parse($timeIn->check_in_time);
            $checkOutTime = Carbon::parse($timeOut->check_out_time);
            $totalHours = $checkInTime->diffInHours($checkOutTime);
        } else {
            $totalHours = 0; // Or handle as needed if no check-out time
        }

        return $totalHours;
    }

    protected function getShiftStartTime($employee, $shift)
    {
        // Modify this method to return the appropriate shift start time based on AM or PM shift
        if ($shift === 'AM') {
            return Carbon::parse($employee->shift_start_time_am);
        } elseif ($shift === 'PM') {
            return Carbon::parse($employee->shift_start_time_pm);
        }
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Check In Time',
            'Check Out Time',
            'Late Duration AM (Minutes)',
            'Late Duration PM (Minutes)',
            'Total Hours AM',
            'Total Hours PM',
            // Add other relevant headings as needed
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 33,  // Width for Employee Name
            'B' => 33,  // Width for Check In Time
            'C' => 33,  // Width for Check Out Time
            'D' => 33,  // Width for Late Duration AM (Minutes)
            'E' => 33,  // Width for Late Duration PM (Minutes)
            'F' => 33,  // Width for Total Hours AM
            'G' => 33,  // Width for Total Hours PM
            // Add other column widths as needed
        ];
    }
}
