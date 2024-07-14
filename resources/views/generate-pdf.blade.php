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
        h4 {
            margin-left: 10px;
            text-align:center;
        }
        span {
            margin-left: 10px;
        }
    </style>
     @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <h4>Attendance Report</h4>
    <span>Employee: {{ $selectedEmployeeToShow->employee_lastname }}, {{ $selectedEmployeeToShow->employee_firstname }} {{ $selectedEmployeeToShow->employee_middlename }}</span>
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
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Check-In Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceTimeIn as $attendanceIn)
                <tr>
                    <td>{{ $attendanceIn->employee->employee_id }}</td>
                    <td>{{ date('m-d-Y, (l)', strtotime($attendanceIn->check_in_time)) }}</td>
                    <td>{{ date('g:i:s A', strtotime($attendanceIn->check_in_time)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Check-Out Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceTimeOut as $attendanceOut)
                <tr>
                    <td>{{ $attendanceOut->employee->employee_id }}</td>
                    <td>{{ date('m-d-Y, (l)', strtotime($attendanceOut->check_out_time)) }}</td>
                    <td>{{ date('g:i:s A', strtotime($attendanceOut->check_out_time)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>AM</th>  
                    <th>PM</th>
                    <th>Total Hours</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceData as $attendance)
                    <tr>
                        <td class="text-black border border-gray-400">{{ $attendance->worked_date }}</td>
                        <td class="text-black border border-gray-400">
                            {{ floor($attendance->hours_workedAM) }} hrs. {{ round($attendance->hours_workedAM - floor($attendance->hours_workedAM), 1) * 60 }} min.
                        </td>
                        <td class="text-black border border-gray-400">
                            {{ floor($attendance->hours_workedPM) }} hrs. {{ round($attendance->hours_workedPM - floor($attendance->hours_workedPM), 1) * 60 }} min.
                        </td>

                        <td class="text-black border border-gray-400">
                            {{ floor($attendance->total_hours_worked) }} hrs. {{ round($attendance->total_hours_worked - floor($attendance->total_hours_worked), 1) * 60 }} min.
                        </td>

                        <td class="text-black border border-gray-400">{{ $attendance->remarks }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p>Overall Total Hours: {{ round($overallTotalHours,3) }}</p>
    </div>

</body>
</html>
