<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Contracts\View\View;

class AttendanceExport implements FromView, WithColumnWidths
{
    protected $attendanceData;

    public function __construct($attendanceData)
    {
        $this->attendanceData = $attendanceData;
    }

    public function view(): View
    {
        return view('exports.attendance_report', [
            'attendanceData' => $this->attendanceData,
        ]);
    }

    public function columnWidths(): array
    {
        // Set all columns to width 33
        return [
            'A' => 8,  // Employee Name
            'B' => 30,  // Total Hours
            'C' =>20,  // Total Hours Worked
            'D' => 25,  // Total Late
            'E' => 25,  // Total Undertime
            'F' => 25,  // Total Absent
            'G' => 25,  // Date Range
        ];
    }
}
