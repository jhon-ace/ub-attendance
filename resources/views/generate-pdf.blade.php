<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Time In Report</title>
    <style>
        @page { margin:50px; }
    </style>
    <style>
        /* Add your PDF-specific styles here */
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0 auto; /* Center align the container */
        }
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #f2f2f2;
            padding: 10px;
            text-align: center;
            margin-bottom: 55px;
            border-bottom: 1px solid #ccc;
        }
        .header img {
            max-width: 100px; /* Adjust size of logo */
            height: auto;
            margin-bottom: 5px; /* Optional: Space between logo and text */
        }
        .header-text {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header-subtext {
            font-size: 10px;
            margin-bottom: 10px;
        }
        .table-container {
            margin-top: 60px; /* Adjust to create space below the header */
            text-align: center; /* Center align text within container */
        }
        .table-container table {
            width: 30%;
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
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
        <div class="header-text">Attendance Report</div>
        <div class="header-subtext">
            Employee: {{ $selectedEmployeeToShow->employee_lastname }}, {{ $selectedEmployeeToShow->employee_firstname }} {{ $selectedEmployeeToShow->employee_middlename }}
        </div>
    </div>

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
                    <td>{{ date('m-d-Y', strtotime($attendanceIn->check_in_time)) }}</td>
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
                    <td>{{ date('m-d-Y', strtotime($attendanceOut->check_out_time)) }}</td>
                    <td>{{ date('g:i:s A', strtotime($attendanceOut->check_out_time)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Hours Rendered</th>  
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceTimeIn as $result)
                <tr>
                    <td>{{ $result->worked_date }}</td>
                    <td>{{ floor($result->hours_worked) }} hrs, {{ ($result->hours_worked - floor($result->hours_worked)) * 60 }} mins</td>
                    <td>{{ $result->remarks }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
