<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Time In Report</title>
    <style>
        @page { margin:18px; }
    </style>
    <style>
        /* Add your PDF-specific styles here */
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0 auto; /* Center align the container */
        }
        .table-container {
            margin-top:20px;
            margin-bottom: 20px;
          
             
            text-align: center; /* Center align text within container */
        }
        .table-container table {
            width: 33%;
            border-collapse: collapse;
            float: left; /* Float tables to achieve side-by-side display */
            margin-right: 5px; /* Add some margin between tables */
        }
        table, th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .table-container .table2 {
            width: 65%;
            border-collapse: collapse;
            float: left; /* Float tables to achieve side-by-side display */
            margin-right: 5px; /* Add some margin between tables */
        }
        .table2, th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
        }
        .table th {
            background-color: #f2f2f2;
        }
        h4 {
            margin-left: 10px;
            text-align:center;
        }
        span {
            margin-left: 10px;
        }
            .border-right {
        border-right: 2px solid #000; /* Adjust the border style, width, and color as needed */
        padding-right: 10px; /* Optional: Add some padding for better spacing */
        margin-right: 10px; /* Optional: Add some margin to separate the content from the border */
    }

    .border-separator {
        display: inline-block;
        height: 100%;
        border-right: 2px solid #000; /* Adjust the border style, width, and color as needed */
        margin-right: 10px; /* Optional: Add some margin to separate the content from the border */
    }

    </style>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <h4>ATTENDANCE REPORT</h4>
    <span>Employee: <text style="color:red">{{ $selectedEmployeeToShow->employee_lastname }}, {{ $selectedEmployeeToShow->employee_firstname }} {{ $selectedEmployeeToShow->employee_middlename }}</text></span><br>
    <span>Employee ID: <text style="color:red">{{ $selectedEmployeeToShow->employee_id }}</text></span>
    @if ($selectedStartDate && $selectedEndDate)
        <div class="date-range">
            <span>Selected Date: {{ date('F d, Y', strtotime($selectedStartDate)) }} to {{ date('F d, Y', strtotime($selectedEndDate)) }}</span>
        </div>
    @else
        <div class="date-range">
            <span>Selected Date: No date range selected</span>
        </div>
    @endif
    <div class="table-container">
       @php
            // Group check-ins and check-outs by employee and date
            $groupedAttendance = [];

            foreach ($attendanceTimeIn as $attendanceIn) {
                $date = date('Y-m-d', strtotime($attendanceIn->check_in_time));
                $employeeId = $attendanceIn->employee->employee_id;

                if (!isset($groupedAttendance[$employeeId][$date])) {
                    $groupedAttendance[$employeeId][$date] = [
                        'date' => date('m-d-Y, (l)', strtotime($attendanceIn->check_in_time)),
                        'check_ins' => [],
                        'check_outs' => []
                    ];
                }

                $groupedAttendance[$employeeId][$date]['check_ins'][] = date('g:i:s A', strtotime($attendanceIn->check_in_time));
            }

            foreach ($attendanceTimeOut as $attendanceOut) {
                $date = date('Y-m-d', strtotime($attendanceOut->check_out_time));
                $employeeId = $attendanceOut->employee->employee_id;

                if (!isset($groupedAttendance[$employeeId][$date])) {
                    $groupedAttendance[$employeeId][$date] = [
                        'date' => date('m-d-Y, (l)', strtotime($attendanceOut->check_out_time)),
                        'check_ins' => [],
                        'check_outs' => []
                    ];
                }

                $groupedAttendance[$employeeId][$date]['check_outs'][] = date('g:i:s A', strtotime($attendanceOut->check_out_time));
            }
        @endphp
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Time - In</th>
                    <th>Time - Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedAttendance as $employeeId => $dates)
                    @foreach($dates as $date => $attendance)
                        @php
                            $isOnLeave = in_array('12:00:00 AM', $attendance['check_ins']) && in_array('12:00:00 AM', $attendance['check_outs']);
                        @endphp
                        <tr>
                            <td>{{ $employeeId }}</td>
                            <td>{{ $attendance['date'] }}</td>
                            <td>
                                @if ($isOnLeave)
                                    On Leave
                                @else
                                    @foreach($attendance['check_ins'] as $checkIn)
                                        {{ $checkIn }}<br>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if ($isOnLeave)
                                    On Leave
                                @else
                                    @foreach($attendance['check_outs'] as $checkOut)
                                        {{ $checkOut }}<br>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <table class="table2">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>AM Late</th>  
                    <th>PM Late</th>
                    <th>AM Undertime</th>  
                    <th>PM Undertime</th>
                    <th>Total AM Hours</th>
                    <th>Total PM Hours</th>
                    <th>Total Late</th>
                    <th>Total Undertime</th>
                    <th>Total Rendered Hours</th>
                    <th>Required Hours</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceData as $attendance)
                    <tr>
                        <td class="text-black border border-gray-400">
                            {{ date('m-d-Y (l)', strtotime($attendance->worked_date)) }}
                        </td>
                        <td class="text-black border border-gray-400">
                            @php
                                // Calculate late duration in minutes
                                $lateDurationInMinutes = $attendance->late_duration;

                                // Calculate late hours, minutes, and seconds
                                $lateHours = intdiv($lateDurationInMinutes, 60);
                                $lateMinutes = $lateDurationInMinutes % 60;
                                $lateSeconds = ($lateDurationInMinutes - floor($lateDurationInMinutes)) * 60;

                                // Round seconds to avoid precision issues
                                $lateSeconds = round($lateSeconds);

                                // Format the late duration string
                                $lateDurationFormatted = ($lateHours > 0 ? "{$lateHours} hr " : '') 
                                                        . ($lateMinutes > 0 ? "{$lateMinutes} min " : '')
                                                        . ($lateSeconds > 0 ? "{$lateSeconds} sec" : '');

                                // If the formatted string is empty, ensure we show "0"
                                $lateDurationFormatted = $lateDurationFormatted ?: '0 sec';
                            @endphp

                            {{ $lateDurationFormatted }}
                        </td>
                        <td class="text-black border border-gray-400">
                            @php
                                // Calculate late duration in minutes
                                $lateDurationInMinutes = $attendance->late_durationPM;

                                // Calculate late hours, minutes, and seconds
                                $lateHours = intdiv($lateDurationInMinutes, 60);
                                $lateMinutes = $lateDurationInMinutes % 60;
                                $lateSeconds = ($lateDurationInMinutes - floor($lateDurationInMinutes)) * 60;

                                // Round seconds to avoid precision issues
                                $lateSeconds = round($lateSeconds);

                                // Format the late duration string
                                $lateDurationFormatted = ($lateHours > 0 ? "{$lateHours} hr " : '') 
                                                        . ($lateMinutes > 0 ? "{$lateMinutes} min " : '')
                                                        . ($lateSeconds > 0 ? "{$lateSeconds} sec" : '');

                                // If the formatted string is empty, ensure we show "0"
                                $lateDurationFormatted = $lateDurationFormatted ?: '0 sec';
                            @endphp

                            {{ $lateDurationFormatted }}
                        </td>
                        <td class="text-black border border-gray-400">
                            @php
                                // Assume $attendance->undertimeAM is in minutes
                                $undertimeInMinutes = $attendance->undertimeAM;

                                // Convert minutes to total seconds
                                $undertimeInSeconds = $undertimeInMinutes * 60;

                                // Convert total seconds to hours, minutes, and seconds
                                $undertimeHours = intdiv($undertimeInSeconds, 3600); // Total hours
                                $remainingSeconds = $undertimeInSeconds % 3600; // Remaining seconds after hours
                                $undertimeMinutes = intdiv($remainingSeconds, 60); // Total minutes
                                $undertimeSeconds = $remainingSeconds % 60; // Remaining seconds after minutes

                                // Format the duration string
                                $undertimeFormatted = 
                                    ($undertimeHours > 0 ? "{$undertimeHours} hr " : '') .
                                    ($undertimeMinutes > 0 ? "{$undertimeMinutes} min " : '0 min ') .
                                    ($undertimeSeconds > 0 ? "{$undertimeSeconds} sec" : '0 sec');
                            @endphp

                            {{ $undertimeFormatted }}
                        </td>
                        <td class="text-black border border-gray-400">
                            @php
                                // Assume $attendance->undertimePM is in minutes
                                $undertimeInMinutes = $attendance->undertimePM;

                                // Convert minutes to total seconds
                                $undertimeInSeconds = $undertimeInMinutes * 60;

                                // Convert total seconds to hours, minutes, and seconds
                                $undertimeHours = intdiv($undertimeInSeconds, 3600); // Total hours
                                $remainingSeconds = $undertimeInSeconds % 3600; // Remaining seconds after hours
                                $undertimeMinutes = intdiv($remainingSeconds, 60); // Total minutes
                                $undertimeSeconds = $remainingSeconds % 60; // Remaining seconds after minutes

                                // Format the duration string
                                $undertimeFormatted = 
                                    ($undertimeHours > 0 ? "{$undertimeHours} hr " : '') .
                                    ($undertimeMinutes > 0 ? "{$undertimeMinutes} min " : '0 min ') .
                                    ($undertimeSeconds > 0 ? "{$undertimeSeconds} sec" : '0 sec');
                            @endphp

                            {{ $undertimeFormatted }}

                        </td>
                        <td class="text-black border border-gray-400 px-2 py-1">
                                                    
                            @php
                                // Total hours worked in AM shift
                                $totalHoursAM = floor($attendance->hours_workedAM);
                                $totalMinutesAM = ($attendance->hours_workedAM - $totalHoursAM) * 60;

                                // Convert minutes to seconds
                                $totalSecondsAM = ($totalMinutesAM - floor($totalMinutesAM)) * 60;
                                $totalMinutesAM = floor($totalMinutesAM);

                                // Get late duration in minutes for AM shift
                                
                                // Convert total minutes to hours and minutes for AM shift
                                $finalHoursAM = $totalHoursAM + floor($totalMinutesAM / 60);
                                $finalMinutesAM = $totalMinutesAM % 60;

                                // Ensure final seconds is a whole number
                                $finalSecondsAM = round($totalSecondsAM);

                            @endphp

                            {{ $finalHoursAM }} hrs. {{ $finalMinutesAM }} min. {{ $finalSecondsAM }} sec.
                        </td>

                        <td class="text-black border border-gray-400">                     
                            @php
                                // Total hours worked in AM PM shift
                                $totalHoursPM = floor($attendance->hours_workedPM);
                                $totalMinutesPM = ($attendance->hours_workedPM - $totalHoursPM) * 60;

                                // Convert minutes to seconds
                                $totalSecondsPM = ($totalMinutesPM - floor($totalMinutesPM)) * 60;
                                $totalMinutesPM = floor($totalMinutesPM);

                                

                                // Convert total minutes to hours and minutes for AM shift
                                $finalHoursPM = $totalHoursPM + floor($totalMinutesPM / 60);
                                $finalMinutesPM = $totalMinutesPM % 60;

                                // Ensure final seconds is a whole number
                                $finalSecondsPM = round($totalSecondsPM);

                            @endphp

                            {{ $finalHoursPM }} hrs. {{ $finalMinutesPM }} min. {{ $finalSecondsPM }} sec.
                        </td>
                        <td class="text-black border border-gray-400">
                            @php
                                // Total late time in minutes as a decimal
                                $totalLateMinutesDecimal = $attendance->total_late;

                                // Convert decimal minutes to total hours, minutes, and seconds
                                $totalLateHours = intdiv($totalLateMinutesDecimal, 60); // Total hours
                                $remainingMinutes = floor($totalLateMinutesDecimal % 60); // Remaining minutes
                                $totalLateSeconds = round(($totalLateMinutesDecimal - floor($totalLateMinutesDecimal)) * 60); // Total seconds

                                // Format the duration string
                                $totalLateDurationFormatted = 
                                    ($totalLateHours > 0 ? "{$totalLateHours} hrs " : '') .
                                    ($remainingMinutes > 0 ? "{$remainingMinutes} mins " : '0 mins ') .
                                    ($totalLateSeconds > 0 ? "{$totalLateSeconds} secs" : '0 secs');
                            @endphp

                            {{ $totalLateDurationFormatted }}
                        </td>
                        <td class="text-black border border-gray-400">
                            @php
                                $am = $attendance->undertimeAM;
                                $pm = $attendance->undertimePM;
                                $totalUndertimeInMinutes = $am + $pm;

                                // Convert total minutes to total seconds
                                $totalUndertimeInSeconds = $totalUndertimeInMinutes * 60;

                                // Convert total seconds to hours, minutes, and seconds
                                $totalLateHours = intdiv($totalUndertimeInSeconds, 3600); // Total hours
                                $remainingSeconds = $totalUndertimeInSeconds % 3600; // Remaining seconds after hours
                                $totalLateMinutes = intdiv($remainingSeconds, 60); // Total minutes
                                $totalLateSeconds = $remainingSeconds % 60; // Remaining seconds after minutes

                                // Format the duration string
                                $totalLateDurationFormatted = 
                                    ($totalLateHours > 0 ? "{$totalLateHours} hrs " : '') .
                                    ($totalLateMinutes > 0 ? "{$totalLateMinutes} mins " : '0 mins ') .
                                    ($totalLateSeconds > 0 ? "{$totalLateSeconds} secs" : '0 secs');
                            @endphp

                            {{ $totalLateDurationFormatted }}

                        </td>
                        <td class="text-black border border-gray-400">
                            @php
                                // Total hours worked in decimal format
                                $totalHoursWorked = $attendance->total_hours_worked;
                                
                                // Calculate hours and minutes
                                $totalHours = floor($totalHoursWorked);
                                $totalMinutes = ($totalHoursWorked - $totalHours) * 60;
                                
                                // Convert total minutes to total seconds
                                $totalSeconds = $totalMinutes * 60;
                                
                                // Calculate final hours, minutes, and seconds
                                $finalHours = $totalHours + floor($totalSeconds / 3600);
                                $remainingSeconds = $totalSeconds % 3600;
                                $finalMinutes = floor($remainingSeconds / 60);
                                $finalSeconds = $remainingSeconds % 60;
                            @endphp

                            {{ $finalHours }} hrs. {{ $finalMinutes }} min. {{ $finalSeconds }} sec.

                        </td>
                        <td class="text-black border border-gray-400">
                            {{ $attendance->hours_perDay }} hr/s
                            
                        </td>
                        <td class="text-black border uppercase border-gray-400">
                            @php
                                $lateDurationAM = $attendance->late_duration;
                                $lateDurationPM = $attendance->late_durationPM;
                                $am = $attendance->undertimeAM ?? 0;
                                $pm = $attendance->undertimePM ?? 0;

                                $totalHoursAM = floor($attendance->hours_workedAM);
                                $totalMinutesAM = ($attendance->hours_workedAM - $totalHoursAM) * 60;
                                $totalHoursPM = floor($attendance->hours_workedPM);
                                $totalMinutesPM = ($attendance->hours_workedPM - $totalHoursPM) * 60;
                                $totalHours = $totalHoursAM + $totalHoursPM;
                                $totalMinutes = $totalMinutesAM + $totalMinutesPM;
                                $modify_status = $attendance->modify_status;

                                $remarkss = '';

                                if (
                                    $lateDurationAM == 0 &&
                                    $lateDurationPM == 0 &&
                                    $am == 0 &&
                                    $pm == 0 &&
                                    $totalHoursAM == 0 &&
                                    $totalMinutesAM == 0 &&
                                    $totalHoursPM == 0 &&
                                    $totalMinutesPM == 0 &&
                                    $modify_status == "Absent"
                                ) {
                                    $remarkss = 'Absent';
                                }
                                else if (
                                    $lateDurationAM == 0 &&
                                    $lateDurationPM == 0 &&
                                    $am == 0 &&
                                    $pm == 0 &&
                                    $totalHoursAM == 0 &&
                                    $totalMinutesAM == 0 &&
                                    $totalHoursPM == 0 &&
                                    $totalMinutesPM == 0 &&
                                    $modify_status == "On Leave"
                                ) {
                                    $remarkss = 'Leave';
                                }
                                else if (
                                    $lateDurationAM == 0 &&
                                    $lateDurationPM == 0 &&
                                    $am == 0 &&
                                    $pm == 0 &&
                                    $totalHoursAM > 0 &&
                                    $totalMinutesAM == 0 &&
                                    $totalHoursPM > 0 &&
                                    $totalMinutesPM == 0 &&
                                    $modify_status == "On Leave"
                                ) {
                                    $remarkss = 'Leave';
                                }
                                    else {
                                    if ($totalHoursAM == 0 && $totalMinutesAM == 0) {
                                        $remarkss = "Present but Absent Morning";
                                    }
                                    else if ($totalHoursPM == 0 && $totalMinutesPM == 0) {
                                        $remarkss = "Present but Absent Afternoon";
                                    } else {
                                        if ($lateDurationAM > 0 && $lateDurationPM > 0) {
                                            $remarkss = 'Present - Late AM & PM';
                                        } elseif ($lateDurationAM > 0) {
                                            $remarkss = 'Present - Late AM';
                                        } elseif ($lateDurationPM > 0) {
                                            $remarkss = 'Present - Late PM';
                                        } else {
                                            $remarkss = "Present";
                                        }
                                    }

                                    $undertimeRemark = '';
                                    if ($am > 0) {
                                        $undertimeRemark .= 'Undertime AM';
                                    }
                                    if ($pm > 0) {
                                        if (!empty($undertimeRemark)) {
                                            $undertimeRemark .= ' & PM';
                                        } else {
                                            $undertimeRemark .= 'Undertime PM';
                                        }
                                    }
                                    if (!empty($undertimeRemark)) {
                                        $remarkss .= ' - ' . $undertimeRemark;
                                    }
                                }
                            @endphp

                                {{ $remarkss }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- make  -->
         <br><br><br>
        
    </div>
    <div style="margin-top:200px; margin-left:82%;display:flex; justify-content:flex-end; align-items:center;">
            <div class="flex   justify-end">
                <div class="flex flex-col mr-4">
                    @php
                        $totalSeconds = $overallTotalHours * 3600; // Convert total hours to seconds
                        $hours = floor($totalSeconds / 3600);
                        $minutes = floor(($totalSeconds % 3600) / 60);
                        $seconds = $totalSeconds % 60;

                        //total late
                        $totalSecondsM = $overallTotalLateHours * 3600; // Convert total hours to seconds
                        $hoursM = floor($totalSecondsM / 3600);
                        $minutesM = floor(($totalSecondsM % 3600) / 60);
                        $secondsM = $totalSecondsM % 60;

                        $undertimeInSeconds = $overallTotalUndertime * 60;

                        // Convert total seconds to hours, minutes, and seconds
                        $undertimeHours = intdiv($undertimeInSeconds, 3600); // Total hours
                        $remainingSeconds = $undertimeInSeconds % 3600; // Remaining seconds after hours
                        $undertimeMinutes = intdiv($remainingSeconds, 60); // Total minutes
                        $undertimeSeconds = $remainingSeconds % 60; // Remaining seconds after minutes

                        // Format the duration string
                        $undertimeFormatted = 
                            ($undertimeHours > 0 ? "{$undertimeHours} hr/s, " : '0 hr/s, ') .
                            ($undertimeMinutes > 0 ? "{$undertimeMinutes} min/s " : '0 min/s, ') .
                            ($undertimeSeconds > 0 ? "{$undertimeSeconds} sec" : '0 sec');

                    @endphp
              
                    <table class="border border-black" cellpadding="10">
                        <tr class="border border-black">
                            <th class="border border-black text-right">Total Late</th>
                            <td class="text-red-500">{{ $hoursM }} hr/s, {{ $minutesM }} min/s, {{ $secondsM }} sec</td>
                        </tr>
                        <tr class="border border-black">
                            <th class="border border-black text-right">Total Undertime</th>
                            <td class="text-red-500">{{ $undertimeFormatted }}</td>
                        </tr>
                        <tr class="border border-black">
                            <th class="border border-black text-right">Overall Total Time</th>
                            <td class="text-red-500">{{ $hours }} hr/s, {{ $minutes }} min/s, {{ $seconds }} sec</td>
                        </tr>
                    </table>
                </div>                        
            </div>
        </div>
</body>
</html>
