@php
    // Group data by employee_id
    $employees = [];
    $departments = [];

    foreach ($attendanceData as $attendance) {
        $employeeId = $attendance->employee_id;
        $departmentId = $attendance->department_id;

        // Get department abbreviation
        $departmentAbbreviation = \App\Models\Admin\Department::where('id', $departmentId)->value('department_abbreviation');

        // Initialize employee data if not already set
        if (!isset($employees[$employeeId])) {
            $employees[$employeeId] = [
                'totalHours' => 0,
                'total_hours_worked' => 0,
                'hours_late_overall' => 0,
                'hours_undertime_overall' => 0,
                'employee_idd' => $attendance->employee_idd,
                'employee_firstname' => $attendance->employee_firstname,
                'employee_middlename' => $attendance->employee_middlename,
                'employee_lastname' => $attendance->employee_lastname,
                'uniqueDays' => [],
                'start_date' => $attendance->check_in_time,
                'end_date' => $attendance->check_in_time,
                'department_abbreviation' => $departmentAbbreviation, // Add department abbreviation
            ];

            // Collect department abbreviations
            if (!in_array($departmentAbbreviation, $departments)) {
                $departments[] = $departmentAbbreviation;
            }
        }
        
        // Accumulate totals for each employee
        $employees[$employeeId]['totalHours'] += $attendance->hours_perDay;
        $employees[$employeeId]['total_hours_worked'] += $attendance->total_hours_worked;
        $employees[$employeeId]['hours_late_overall'] += $attendance->hours_late_overall; // Replace with actual late hours field
        $employees[$employeeId]['hours_undertime_overall'] += $attendance->hours_undertime_overall; // Replace with actual undertime field
    
        $date = \Illuminate\Support\Carbon::parse($attendance->check_in_time)->toDateString();
        $employees[$employeeId]['uniqueDays'][$date] = true;

        // Update the start and end date
        if (\Illuminate\Support\Carbon::parse($attendance->check_in_time)->lt(\Illuminate\Support\Carbon::parse($employees[$employeeId]['start_date']))) {
            $employees[$employeeId]['start_date'] = $attendance->check_in_time;
        }
        if (\Illuminate\Support\Carbon::parse($attendance->check_in_time)->gt(\Illuminate\Support\Carbon::parse($employees[$employeeId]['end_date']))) {
            $employees[$employeeId]['end_date'] = $attendance->check_in_time;
        }
    }

    // Check if all employees are in the same department
    $singleDepartment = count(array_unique($departments)) === 1 ? $departments[0] : null;
@endphp

<h1 class="text-center">Data for Payroll</h1>

@if ($singleDepartment)
    <h2 class="text-center mt-4">Department: {{ $singleDepartment }}</h2>
@endif

<!-- Table Header -->
<div class="overflow-x-auto relative mt-2 mb-2">
    <table class="min-w-full text-left text-sm font-light border-collapse border border-gray-200">
        <thead class="border-b bg-gray-800 font-medium text-white">
            <tr>
                <th class="py-3 px-6">Emp ID</th>
                <th class="py-3 px-6">Employee FullName</th>
                <th class="py-3 px-6">Duty Hours to be rendered</th>
                <th class="py-3 px-6">Total Time Rendered</th>
                <th class="py-3 px-6">Final Time Deduction</th>
                <th class="py-3 px-6">Total Late</th>
                <th class="py-3 px-6">Total Undertime</th>
                <th class="py-3 px-6">Absent Hour</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employeeId => $employeeData)
                @php
                                                
                    // Total hours
                    $totalSeconds = $employeeData['totalHours'] * 3600;
                    $hours = floor($totalSeconds / 3600);
                    $minutes = floor(($totalSeconds % 3600) / 60);
                    $seconds = $totalSeconds % 60;


                    $totalSecondsWorked = $employeeData['total_hours_worked'] * 3600;
                    $overallhours = floor($totalSecondsWorked / 3600);
                    $overallminutes = floor(($totalSecondsWorked % 3600) / 60);
                    $overallseconds = $totalSecondsWorked % 60;

                    if ($overallseconds == 59) {
                        $overallminutes += 1;
                        $overallseconds = 0;
                    }

                    // If minutes exceed 59, convert to hours
                    if ($overallminutes >= 60) {
                        $overallhours += floor($overallminutes / 60);
                        $overallminutes = $overallminutes % 60;
                    }

                    $formattedTimeWorked = 
                        ($overallhours > 0 ? "{$overallhours} hr/s, " : '0 hr/s, ') .
                        ($overallminutes > 0 ? "{$overallminutes} min/s " : '0 min/s, ') .
                        ($overallseconds > 0 ? "{$overallseconds} sec" : '0 sec');

                    // Total late
                    $totalSecondsM = $employeeData['hours_late_overall'] * 3600;
                    $hoursM = floor($totalSecondsM / 3600);
                    $minutesM = floor(($totalSecondsM % 3600) / 60);
                    $secondsM = $totalSecondsM % 60;

                    $totalLateSeconds = $totalSeconds - $totalSecondsWorked;
                    $totalLateHours = floor($totalLateSeconds / 3600);
                    $totalLateMinutes = floor(($totalLateSeconds % 3600) / 60);
                    $totalLateSeconds = $totalLateSeconds % 60;

                    $latee = 
                        ($totalLateHours > 0 ? "{$totalLateHours} hr/s, " : '0 hr/s, ') .
                        ($totalLateMinutes > 0 ? "{$totalLateMinutes} min/s " : '0 min/s, ') .
                        ($totalLateSeconds > 0 ? "{$totalLateSeconds} sec" : '0 sec');
                    

                    // Total undertime
                    $undertimeInSeconds = $employeeData['hours_undertime_overall'] * 60;
                    $undertimeHours = intdiv($undertimeInSeconds, 3600);
                    $remainingSeconds = $undertimeInSeconds % 3600;
                    $undertimeMinutes = intdiv($remainingSeconds, 60);
                    $undertimeSeconds = $remainingSeconds % 60;

                    // Format the undertime
                    $undertimeFormatted = 
                        ($undertimeHours > 0 ? "{$undertimeHours} hr/s, " : '0 hr/s, ') .
                        ($undertimeMinutes > 0 ? "{$undertimeMinutes} min/s " : '0 min/s, ') .
                        ($undertimeSeconds > 0 ? "{$undertimeSeconds} sec" : '0 sec');

                    // Format total hours
                    //$totalFormatted = 
                        // ($hours > 0 ? "{$hours} hr/s, " : '0 hr/s, ') .
                        // ($minutes > 0 ? "{$minutes} min/s " : '0 min/s, ');

                    $totalFormatted = '';

                    if ($hours > 0) {
                        $totalFormatted .= "{$hours} hr/s";
                    }

                    if ($minutes > 0) {
                        $totalFormatted .= ($hours > 0 ? ', ' : '') . "{$minutes} min/s";
                    } elseif ($hours > 0) {
                        // Include a comma if hours are present but no minutes
                        $totalFormatted .= '';
                    } else {
                        // If there are no hours and no minutes, ensure the format is '0 hr/s, 0 min/s'
                        $totalFormatted = '0 hr/s, 0 min/s';
                    }

                    // Add seconds if needed
                    $totalFormatted .= $seconds > 0 ? " {$seconds} sec" : '';

                    // Format total late
                    $lateFormatted = 
                        ($hoursM > 0 ? "{$hoursM} hr/s, " : '0 hr/s, ') .
                        ($minutesM > 0 ? "{$minutesM} min/s " : '0 min/s, ') .
                        ($secondsM > 0 ? "{$secondsM} sec" : '0 sec');

                        $attendanceDaysCount = count($employeeData['uniqueDays']);

                        $rtotal = $totalSecondsWorked + $totalSecondsM + $undertimeInSeconds;
                    $absentSecondss = $totalSeconds - $rtotal;

                    // Convert absence seconds to hours, minutes, and seconds
                    $absentHours = floor($absentSecondss / 3600);
                    $remainingSeconds = $absentSecondss % 3600;
                    $absentMinutes = floor($remainingSeconds / 60);
                    $absentSeconds = $remainingSeconds % 60;

                    // If seconds are 59, round up the minutes
                    if ($absentSeconds == 59) {
                        $absentMinutes += 1;
                        $absentSeconds = 0;
                    }

                    // If minutes are 60, convert them to an hour
                    if ($absentMinutes == 60) {
                        $absentHours += 1;
                        $absentMinutes = 0;
                    }

                    // Format the absence time
                    $absentFormatted = 
                        ($absentHours > 0 ? "{$absentHours} hr/s" : '') .
                        (($absentHours > 0 && $absentMinutes > 0) ? ", " : '') . 
                        ($absentMinutes > 0 ? "{$absentMinutes} min/s" : '') .
                        (($absentMinutes > 0 && $absentSeconds > 0) ? " " : '') . 
                        ($absentSeconds > 0 ? "{$absentSeconds} sec" : ($absentHours <= 0 && $absentMinutes <= 0 ? ' 0 ' : ''));

                    // Add the comma and space between the values
                    $absentFormatted = trim($absentFormatted, ', ');



                    $finalDeduction = $totalSecondsM + $undertimeInSeconds + $absentSecondss;

                    // Calculate final hour deduction
                    $finalHourDeductionHours = floor($finalDeduction / 3600);
                    $finalDeductionRemainingSeconds = $finalDeduction % 3600;
                    $finalHourDeductionMinutes = floor($finalDeductionRemainingSeconds / 60);
                    $finalHourDeductionSeconds = $finalDeductionRemainingSeconds % 60;

                    // Format final hour deduction
                    $finalHourDeductionFormatted = 
                        ($finalHourDeductionHours > 0 ? "{$finalHourDeductionHours} hr/s, " : '0 hr/s, ') .
                        ($finalHourDeductionMinutes > 0 ? "{$finalHourDeductionMinutes} min/s " : '0 min/s, ') .
                        ($finalHourDeductionSeconds > 0 ? "{$finalHourDeductionSeconds} sec" : '0 sec');
                    
                    
                @endphp

                <tr class="border-b bg-gray-100 hover:bg-gray-200">
                    <td class="py-3 px-6 text-center">{{ $employeeData['employee_idd'] }}</td>
                    <td class="py-3 px-6">
                        {{ $employeeData['employee_firstname'] }} {{ $employeeData['employee_middlename'] }} {{ $employeeData['employee_lastname'] }}
                    </td>
                    <td class="py-3 px-6 text-center">{{ $totalFormatted }}  from ({{ $attendanceDaysCount }} days worked)</td>
                    <td class="py-3 px-6 text-center">{{ $formattedTimeWorked }}</td>
                    <td class="py-3 px-6 text-center">{{ $lateFormatted }}</td>
                    <td class="py-3 px-6 text-center">{{ $latee }}</td>
                    <td class="py-3 px-6 text-center">{{ $undertimeFormatted }}</td>
                    <td class="py-3 px-6 text-center">{{ $absentFormatted }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
