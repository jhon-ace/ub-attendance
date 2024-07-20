<div class="mb-4">
        @php
            session(['selectedSchool' => $selectedSchool]);
            session(['selectedDepartment4' => $selectedDepartment4]);
            session(['selectedEmployee' => $selectedEmployee])
        @endphp
    @if (session('success'))
        <x-sweetalert type="success" :message="session('success')" />
    @endif

    @if (session('info'))
        <x-sweetalert type="info" :message="session('info')" />
    @endif

    @if (session('error'))
        <x-sweetalert type="error" :message="session('error')" />
    @endif
    <div class="flex justify-between mb-4 sm:-mt-4">
        <div class="font-bold text-md tracking-tight text-md text-black  mt-2">Admin / Employee Attendance</div>
    </div>
    
        <div class="flex flex-column overflow-x-auto -mb-5">
            <div class="col-span-3 p-4">
                <label for="school_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate">School Year:</label>
                <select wire:model="selectedSchool" id="school_id" name="school_id" wire:change="updateEmployees"
                        class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror md:w-auto"
                        required>
                    <option value="">Select School Year</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->abbreviation }}</option>
                    @endforeach
                </select>
                @if($schoolToShow)
                    <p class="text-black mt-2 text-sm mb-1 ">Selected School Year: <span class="text-red-500 ml-2">{{ $schoolToShow->abbreviation }}</span></p>
                    <!-- <p class="text-black  text-sm ml-4">Selected School: <span class="text-red-500 ml-2">{{ $schoolToShow->school_name }}</span></p> -->
                @endif
            </div>

        <div class="col-span-1 p-4">
            @if(!empty($selectedSchool))
                <label for="department_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate">Department:</label>
                <select wire:model="selectedDepartment4" id="department_id" name="department_id"
                        wire:change="updateEmployeesByDepartment"
                        class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror md:w-auto"
                        required>
                    @if($departments->isEmpty())
                        <option value="0">No Departments</option>
                    @else
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->department_id }} | {{ $department->department_abbreviation }} - {{ $department->department_name }}</option>
                        @endforeach
                    @endif
                </select>
                @if($departmentToShow)
                    <!-- <p class="text-black mt-2 text-sm mb-1">Selected Department ID: <span class="text-red-500 ml-2">{{ $departmentToShow->department_id }}</span></p> -->
                    <p class="text-black text-sm ml-4">Selected Department: <span class="text-red-500 ml-2">{{ $departmentToShow->department_name }}</span></p>
                    
                @endif
            @endif
        </div>

    </div>
    <hr class="border-gray-200 my-4">
        @if(!$schoolToShow)
            <p class="text-black text-sm mt-11 mb-4 uppercase text-center">No selected school</p>
        @endif
        @if(!empty($selectedSchool))
            @if(!$departmentToShow)
                <p class="text-black text-sm mt-11 mb-4 uppercase text-center">No selected department</p>
            @endif
        @endif
        
        @if($departmentToShow)
            <div class="flex justify-start">
                <div>
                    <label for="department_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate">Display attendance:</label>
                    <select wire:model="selectedEmployee" id="department_id" name="department_id"
                            wire:change="updateAttendanceByEmployee"
                            class="cursor-pointer text-sm shadow appearance-none border  rounded text-black leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror md:w-auto"
                            required>
                        @if($departments->isEmpty())
                            <option value="0">No Employees</option>
                        @else
                            <option value="" selected>Select Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->employee_id }} - {{ $employee->employee_lastname }}, {{ $employee->employee_firstname }} {{ ucfirst($employee->employee_middlename) }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                            <!-- Modal -->
                <div x-data="{ open: false }" @keydown.window.escape="open = false" x-cloak>
                    <!-- Modal Trigger Button -->
                    <button @click="open = true" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded ml-2 mt-5"><i class="fa-solid fa-calendar-days"></i></button>

                    <!-- Modal Background -->
                    <div x-show="open" class="fixed inset-0 bg-black bg-opacity-50 z-50" @click="open = false"></div>

                    <!-- Modal Content -->
                    <div x-show="open" class="fixed inset-0 flex items-center justify-center z-50">
                        <div class="bg-white p-8 rounded-lg shadow-lg max-w-7xl w-full ">
                            <h2 class="text-lg font-semibold mb-4">Work Details</h2>

                            <!-- Modal Body -->
                            <div class="space-y-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day Of Week</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Morning Hours</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Afternoon Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($departmentDisplayWorkingHour as $working_hour)
                                                <tr>
                                                    @php
                                                        $daysOfWeek = [
                                                            0 => 'Sunday',
                                                            1 => 'Monday',
                                                            2 => 'Tuesday',
                                                            3 => 'Wednesday',
                                                            4 => 'Thursday',
                                                            5 => 'Friday',
                                                            6 => 'Saturday',
                                                        ];
                                                    @endphp

                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        {{ $daysOfWeek[$working_hour->day_of_week] }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                       {{ date('h:i A', strtotime($working_hour->morning_start_time)) }} - {{ date('h:i A', strtotime($working_hour->morning_end_time)) }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                       {{ date('h:i A', strtotime($working_hour->afternoon_start_time)) }} - {{ date('h:i A', strtotime($working_hour->afternoon_end_time)) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                    <div class="mt-6 flex justify-end">
                                    <button @click="open = false" class="btn btn-secondary">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($selectedEmployeeToShow)
                @if($search && $attendanceTimeIn->isEmpty() && $attendanceTimeOut->isEmpty() && !$selectedAttendanceByDate->isEmpty())
                    <p class="text-black mt-8 text-center">No attendance/s found in <span class="text-red-500">{{ $selectedEmployeeToShow->employee_id }} | {{ $selectedEmployeeToShow->employee_lastname }}, {{ $selectedEmployeeToShow->employee_firstname }} {{ $selectedEmployeeToShow->employee_middlename }} </span> for matching "{{ $search }}"</p>
                    <p class="text-center mt-5"><button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="$set('search', '')"><i class="fa-solid fa-remove"></i> Clear Search</button></p>
                @elseif(!$search && $attendanceTimeIn->isEmpty() && $attendanceTimeOut->isEmpty() && !$selectedAttendanceByDate->isEmpty())
                    <p class="text-black mt-8 text-center uppercase">No data available in employee <text class="text-red-500">{{ $selectedEmployeeToShow->employee_id }} | {{ $selectedEmployeeToShow->employee_lastname }}, {{ $selectedEmployeeToShow->employee_firstname }} {{ $selectedEmployeeToShow->employee_middlename }}</text></p>
                @else
                    <div class="flex justify-between mt-1 mb-2">
                        <div class="mt-2 text-sm font-bold ">
                            <text class="uppercase">Attendance of Employee: {{ $selectedEmployeeToShow->employee_lastname }}, {{ $selectedEmployeeToShow->employee_firstname }} {{ $selectedEmployeeToShow->employee_middlename }}
                        </div>
                        <div class="flex flex-col">
                            <div class="flex justify-between items-center mb-2">
                                <div class="grid grid-rows-2 grid-flow-col -mt-10">
                                  
                                    <div class="text-center uppercase ml-16">
                                        Select Specific Date
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <label for="startDate" class="text-gray-600">Start Date:</label>
                                        <input 
                                            id="startDate" 
                                            type="date" 
                                            class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            wire:model="startDate"
                                            wire:change="updateAttendanceByDateRange"
                                        >
                                        <label for="endDate" class="text-gray-600">End Date:</label>
                                        <input 
                                            id="endDate" 
                                            type="date" 
                                            class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            wire:model="endDate"
                                            wire:change="updateAttendanceByDateRange"
                                        >
                                    </div>
                                </div>
                                <button wire:click="generatePDF" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                                    <i class="fa-solid fa-file"></i> Print DTR
                                </button>
                            </div>
                        </div>
                    </div>
                    <div x-data="{ tab: 'time-in-time-out' }" class="p-4">
                        <div class="overflow-x-auto">
                            <!-- Tab buttons -->
                            <div class="flex mb-4">
                                <button 
                                    @click="tab = 'time-in-time-out'"
                                    :class="{ 'bg-blue-500 text-white': tab === 'time-in-time-out', 'border border-gray-500': tab !== 'time-in-time-out' }"
                                    class="px-4 py-2 mr-2 rounded hover:bg-blue-600 hover:text-white focus:outline-none"
                                >
                                    Time In & Time Out
                                </button>
                                <!-- <button 
                                    @click="tab = 'time-out'"
                                    :class="{ 'bg-blue-500 text-white': tab === 'time-out', 'bg-gray-200': tab !== 'time-out' }"
                                    class="px-4 py-2 mr-2 rounded hover:bg-blue-600 focus:outline-none"
                                >
                                    Time Out
                                </button> -->
                                <button 
                                    @click="tab = 'computed-hours'"
                                    :class="{ 'bg-blue-500 text-white': tab === 'computed-hours', 'border border-gray-500': tab !== 'computed-hours' }"
                                    class="px-4 py-2 rounded hover:bg-blue-600 hover:text-white focus:outline-none"
                                >
                                    Calculation of Work Hours
                                </button>
                            </div>

                            <!-- Tab content -->
                            <div x-show="tab === 'time-in-time-out'" class="w-full">
                                <!-- Table for Time In -->
                                <div class="flex justify-between">
                                    <div class="w-[49%]">
                                        <h3 class="text-center">Time In</h3>
                                        <!-- Assuming $attendanceTimeIn is sorted by check_in_time descending -->
                                        @if ($attendanceTimeIn->isNotEmpty())
                                            @php
                                                $currentDate = null;
                                            @endphp
                                            @foreach ($attendanceTimeIn as $attendanceIn)
                                                @php
                                                    $checkInTime = strtotime($attendanceIn->check_in_time);
                                                    $date = date('m-d-Y', $checkInTime);
                                                    $category = date('A', $checkInTime); // AM or PM
                                                @endphp
                                                @if ($date !== $currentDate)
                                                    @php
                                                        $currentDate = $date;
                                                        $firstRow = true;
                                                    @endphp
                                                    <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                                                        <thead class="bg-gray-200 text-black">
                                                            <tr>
                                                                <th class="border border-gray-400 px-3 py-2">
                                                                    Emp ID
                                                                </th>
                                                                <th class="border border-gray-400 px-3 py-2">
                                                                    Date
                                                                </th>
                                                                <th class="border border-gray-400 px-3 py-2">
                                                                    Check-In
                                                                </th>
                                                                <th class="border border-gray-400 px-3 py-2">
                                                                    Time In
                                                                </th>
                                                                <!-- Add other columns as needed -->
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                @endif
                                                <tr class="hover:bg-gray-100">
                                                    <td class="text-black border border-gray-400">{{ $attendanceIn->employee->employee_id }}</td>
                                                    <td class="text-black border border-gray-400">
                                                        {{ date('m-d-Y (l)', strtotime($attendanceIn->check_in_time)) }}
                                                    </td>
                                                    <td class="text-black border border-gray-400">{{ date('g:i:s A', strtotime($attendanceIn->check_in_time)) }}</td>
                                                    <td class="text-black border border-gray-400">
                                                        {{ $category }}
                                                    </td>
                                                    <!-- Add other columns as needed -->
                                                </tr>
                                                @if ($loop->last)
                                                        </tbody>
                                                    </table>
                                                @endif
                                            @endforeach
                                        @else
                                            <p class="text-center">No Time In records found.</p>
                                        @endif
                                        <div class="text-center font-bold uppercase">{{ $attendanceTimeIn->links() }}</div>
                                    </div>
                                    
                                    <div class="w-[49%]">
                                        <h3 class="text-center">Time Out</h3>
                                        @if ($attendanceTimeOut->isNotEmpty())
                                            @php
                                                $currentDate = null;
                                                $firstRow = true;
                                            @endphp
                                            @foreach ($attendanceTimeOut as $attendanceOut)
                                                @php
                                                    $checkOutTime = strtotime($attendanceOut->check_out_time);
                                                    $date = date('m-d-Y', $checkOutTime);
                                                    $isFirstRow = ($date !== $currentDate);
                                                    $category = $isFirstRow ? 'AM' : date('A', $checkOutTime);
                                                @endphp
                                                @if ($isFirstRow)
                                                    @if ($loop->index > 0)
                                                        </tbody></table>
                                                    @endif
                                                    <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                                                        <thead class="bg-gray-200 text-black">
                                                            <tr>
                                                                <th class="border border-gray-400 px-3 py-2">
                                                                    Emp ID
                                                                </th>
                                                                <th class="border border-gray-400 px-3 py-2">
                                                                    Date
                                                                </th>
                                                                <th class="border border-gray-400 px-3 py-2">
                                                                    Check-Out
                                                                </th>
                                                                <th class="border border-gray-400 px-3 py-2">
                                                                    Time Out
                                                                </th>
                                                                <!-- Add other columns as needed -->
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                    @php
                                                        $currentDate = $date;
                                                    @endphp
                                                @endif
                                                <tr class="hover:bg-gray-100">
                                                    <td class="text-black border border-gray-400">{{ $attendanceOut->employee->employee_id }}</td>
                                                    <td class="text-black border border-gray-400">
                                                        {{ date('m-d-Y (l)', $checkOutTime) }}
                                                    </td>
                                                    <td class="text-black border border-gray-400">{{ date('g:i:s A', $checkOutTime) }}</td>
                                                    <td class="text-black border border-gray-400">
                                                        {{ $category }}
                                                    </td>
                                                    <!-- Add other columns as needed -->
                                                </tr>
                                                @if ($loop->last)
                                                    </tbody></table>
                                                @endif
                                            @endforeach
                                        @else
                                            <p class="text-center">No Time Out records found.</p>
                                        @endif
                                        <div class="text-center font-bold uppercase">{{ $attendanceTimeOut->links() }}</div>
                                    </div>

                                </div>
                            </div>

                            <div x-show="tab === 'computed-hours'" class="w-full">
                                <!-- Table for Computed Working Hours -->
                                <div class="w-[100%]">
                                    <h3 class="text-center">Calculation of Work Hours</h3>
                                    <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                                        <thead class="bg-gray-200 text-black">
                                            <tr>
                                                <th class="border border-gray-400 px-3 py-2">
                                                    Date
                                                </th>
                                                <th class="border border-gray-400 px-3 py-2">
                                                    AM Late Time
                                                </th>
                                                <th class="border border-gray-400 px-3 py-2">
                                                    PM Late Time
                                                </th>
                                                <th class="border border-gray-400 px-3 py-2">
                                                    AM UnderTime
                                                </th>
                                                <th class="border border-gray-400 px-3 py-2">
                                                    PM UnderTime
                                                </th>
                                                <th class="border border-gray-400 px-3 py-2">
                                                   Total AM Hours
                                                </th>
                                                
                                                
                                                <th class="border border-gray-400 px-3 py-2">
                                                    Total PM Hours
                                                </th>

                                                <th class="border border-gray-400 px-3 py-2">
                                                    Total Late Hours
                                                </th>
                                                <th class="border border-gray-400 px-3 py-2">
                                                    Total Hours Rendered
                                                </th>
                                                
                                                <th class="border border-gray-400 px-3 py-2">
                                                    Remarks
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Example data using Blade templating -->
                                            @foreach ($attendanceData as $attendance)
                                            <tr>
                                                <td class="text-black border border-gray-400">
                                                    {{ date('M d, Y (D)', strtotime($attendance->worked_date)) }}
                                                </td>

                                                <td class="text-black border border-gray-400">
                                                    @php
                                                    // Retrieve the late duration from session
                                                    $lateDurationInMinutes = session('late_duration', 0);

                                                    // Calculate hours and minutes
                                                    $lateHours = intdiv($lateDurationInMinutes, 60);
                                                    $lateMinutes = $lateDurationInMinutes % 60;

                                                    // Format the late duration
                                                    $lateDurationFormatted = '';

                                                    if ($lateHours > 0) {
                                                    $lateDurationFormatted .= "{$lateHours} hr ";
                                                    }
                                                    if ($lateMinutes > 0) {
                                                    $lateDurationFormatted .= "{$lateMinutes} min";
                                                    }
                                                    // If no late duration
                                                    if (empty($lateDurationFormatted)) {
                                                    $lateDurationFormatted = 'No late';
                                                    }
                                                    @endphp

                                                    {{ $lateDurationFormatted }}
                                                </td>
                                                <td class="text-black border border-gray-400">
                                                    @php
                                                    // Retrieve the late duration from session
                                                    $lateDurationInMinutes = session('late_duration_pm', 0);

                                                    // Calculate hours and minutes
                                                    $lateHours = intdiv($lateDurationInMinutes, 60);
                                                    $lateMinutes = $lateDurationInMinutes % 60;

                                                    // Format the late duration
                                                    $lateDurationFormatted = '';

                                                    if ($lateHours > 0) {
                                                    $lateDurationFormatted .= "{$lateHours} hr ";
                                                    }
                                                    if ($lateMinutes > 0) {
                                                    $lateDurationFormatted .= "{$lateMinutes} min";
                                                    }
                                                    // If no late duration
                                                    if (empty($lateDurationFormatted)) {
                                                    $lateDurationFormatted = 'No late';
                                                    }
                                                    @endphp

                                                    {{ $lateDurationFormatted }}
                                                </td>
                                                <td class="text-black border border-gray-400">
                                                </td>
                                                <td class="text-black border border-gray-400"></td>
                                                <td class="text-black border border-gray-400">
                                                    {{ floor($attendance->hours_workedAM) }} hrs. {{ round($attendance->hours_workedAM - floor($attendance->hours_workedAM), 1) * 60 }} min.
                                                </td>
                                                

                                                <td class="text-black border border-gray-400">
                                                    {{ floor($attendance->hours_workedPM) }} hrs. {{ round($attendance->hours_workedPM - floor($attendance->hours_workedPM), 1) * 60 }} min.
                                                </td>
                                                @php
                                                    // Retrieve the late durations from the session
                                                    $lateDurationPmInMinutes = session('late_duration_pm', 0);
                                                    $lateDurationInMinutes = session('late_duration', 0);

                                                    // Calculate the total late duration in minutes
                                                    $totalLateDurationInMinutes = $lateDurationPmInMinutes + $lateDurationInMinutes;

                                                    // Calculate hours and minutes
                                                    $totalLateHours = intdiv($totalLateDurationInMinutes, 60);
                                                    $totalLateMinutes = $totalLateDurationInMinutes % 60;

                                                    // Format the total late duration
                                                    $totalLateDurationFormatted = '';

                                                    if ($totalLateHours > 0) {
                                                        $totalLateDurationFormatted .= "{$totalLateHours} hrs ";
                                                    }
                                                    if ($totalLateMinutes > 0) {
                                                        $totalLateDurationFormatted .= "{$totalLateMinutes} mins";
                                                    }
                                                    // If no total late duration
                                                    if (empty($totalLateDurationFormatted)) {
                                                        $totalLateDurationFormatted = 'No late';
                                                    }
                                                @endphp

                                                <td class="text-black border border-gray-400">
                                                    {{ $totalLateDurationFormatted }}
                                                </td>

                                                <td class="text-black border border-gray-400">
                                                    @php
    // Calculate total hours and minutes for AM and PM
    $totalHoursAM = floor($attendance->hours_workedAM);
    $totalMinutesAM = round($attendance->hours_workedAM - $totalHoursAM, 1) * 60;
    
    $totalHoursPM = floor($attendance->hours_workedPM);
    $totalMinutesPM = round($attendance->hours_workedPM - $totalHoursPM, 1) * 60;
    
    $totalHours = $totalHoursAM + $totalHoursPM;
    $totalMinutes = $totalMinutesAM + $totalMinutesPM;

    // Retrieve late durations from session
    $lateDurationInMinutes = session('late_duration', 0);
    $lateDurationPmInMinutes = session('late_duration_pm', 0);

    // Add 15 minutes if there is late duration in either AM or PM, but not both
    if ($lateDurationInMinutes > 0 && $lateDurationPmInMinutes === 0) {
        $totalMinutes += 15;
    } elseif ($lateDurationInMinutes === 0 && $lateDurationPmInMinutes > 0) {
        $totalMinutes += 15;
    }
    else if($lateDurationInMinutes > 0 && $lateDurationPmInMinutes > 0){
        $totalMinutes += 30;
    }

    // Convert total minutes to hours and minutes
    $finalHours = floor($totalMinutes / 60);
    $finalMinutes = $totalMinutes % 60;
@endphp


<!-- Display total hours and minutes -->
{{ $totalHours }} hrs. {{ $totalMinutes }} min.<br>


                                                </td>
                                                
                                                <td class="text-black border border-gray-400">{{ $attendance->remarks }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-items-end justify-end">
                        <p>Overall Total Hours: {{ round($overallTotalHours,2) }}</p>
                    </div>
                @endif
            @else
                @if($employees->isEmpty())
                    <p class="text-black text-sm mt-11 mb-4 uppercase text-center">Add Employee first in the department</p>
                @else
                    <p class="text-black text-sm mt-11 mb-4 uppercase text-center">No selected Employee</p>
                @endif
            @endif
        @endif
            
        
    
   
</div>
@push('scripts')
<script>
    Livewire.on('livewire:load', () => {
        flatpickr("#date_start", {
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                @this.set('dateStart', dateStr);
            }
        });

        flatpickr("#date_end", {
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                @this.set('dateEnd', dateStr);
            }
        });
    });
</script>
@endpush

<script>
    function downloadPDF() {
        // Initialize jsPDF
        const pdf = new jspdf.jsPDF('l', 'px', 'a4');

        // Set margins (adjust as needed)
        const margins = { top: 30, bottom: 10, left: 30, width: 800 };

        // Define position variables
        let posY = margins.top;

        // Add title
        pdf.setFontSize(16);


        pdf.text('Time In', margins.left, posY);
        posY += 10; // Increase posY for spacing

        // Define column widths
        const col1Width = 60;
        const col2Width = 120;
        const col3Width = 120;

        // Add table headers without background
        pdf.setFontSize(12);

        // Employee ID column header
        pdf.rect(margins.left, posY - 7, col1Width, 20); // Border around cell
        pdf.text('Employee ID', margins.left + 5, posY); // Add text with adjusted position

        // Date column header
        pdf.rect(margins.left + col1Width, posY - 7, col2Width, 20); // Border around cell
        pdf.text('Date', margins.left + col1Width + 5, posY); // Add text with adjusted position

        // Check-In Time column header
        pdf.rect(margins.left + col1Width + col2Width, posY - 7, col3Width, 20); // Border around cell
        pdf.text('Check-In Time', margins.left + col1Width + col2Width + 5, posY); // Add text with adjusted position

        posY += 18; // Increase posY for table header row

        // Iterate through table rows and add data with borders and padding
        @foreach ($attendanceTimeIn as $attendanceIn)
            pdf.setFontSize(11);

            // Employee ID data
            pdf.rect(margins.left, posY - 5, col1Width, 15); // Border around cell, adjusted height to 15px
            pdf.text('{{ $attendanceIn->employee_id }}', margins.left + 5, posY + 5); // Text alignment with padding

            // Date data
            pdf.rect(margins.left + col1Width, posY - 5, col2Width, 15); // Border around cell, adjusted height to 15px
            pdf.text('{{ date('m-d-Y, (l)', strtotime($attendanceIn->check_in_time)) }}', margins.left + col1Width + 5, posY + 5); // Text alignment with padding

            // Check-In Time data
            pdf.rect(margins.left + col1Width + col2Width, posY - 5, col3Width, 15); // Border around cell, adjusted height to 15px
            pdf.text('{{ date('g:i:s A', strtotime($attendanceIn->check_in_time)) }}', margins.left + col1Width + col2Width + 5, posY + 5); // Text alignment with padding

            posY += 15; // Increase posY for next row, adjusted to 15px height
        @endforeach
        // Save the PDF
        pdf.save('attendance.pdf');
    }
</script>





<script>
    function confirmUpdate(event) {
        event.preventDefault();

        let currentValue = event.target.value;
        let currentDeptID = event.target.DepartmentID;

        Swal.fire({
            title: 'Are you sure?',
            html: `You are about to update <strong>${this.originalValue}</strong> to <strong>${this.value}</strong>. Are you sure you want to proceed?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.value = currentValue;
                event.target.value = currentDeptID;
                event.target.closest('form').submit();
            } else {
                this.editing = false;
                this.cancelEdit();
            }
        });
    }
</script>
<script>
    document.addEventListener('livewire:load', function () {
        flatpickr("#rangeDate", {
            mode: "range",
            dateFormat: "Y-m-d",
        });
    });
</script>
<script>

    function cancelEdit() {
        this.value = this.originalValue;
        this.editing = false;
    }

</script>

<script>

    document.addEventListener('DOMContentLoaded', function() {
        tippy('[data-tippy-content]', {
            allowHTML: true,
            theme: 'light', // Optional: Change the tooltip theme (light, dark, etc.)
            placement: 'right-end', // Optional: Adjust tooltip placement
        });
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
      Fancybox.bind('[data-fancybox]', {
        contentClick: "iterateZoom",
        Images: {
            Panzoom: {
                maxScale: 3,
                },
            initialSize: "fit",
        },
        Toolbar: {
          display: {
            left: ["infobar"],
            middle: [
              "zoomIn",
              "zoomOut",
              "toggle1to1",
              "rotateCCW",
              "rotateCW",
              "flipX",
              "flipY",
            ],
            right: ["slideshow", "download", "thumbs", "close"],
          },
        },
      });    
</script>


<script>
    function confirmDeleteAll(event) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: 'Select Employee to Delete All Records',
            html: `
            
                <select id="department_id_select" class="cursor-pointer hover:border-red-500 swal2-select">
                    <option value="">Select Department</option>
                     @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->department_id }} | {{ $department->department_abbreviation }} - {{ $department->department_name }}</option>
                        @endforeach
                </select>
            `,
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete all!',
            preConfirm: () => {
                const departmentId = Swal.getPopup().querySelector('#department_id_select').value;
                if (!departmentId) {
                    Swal.showValidationMessage(`Please select a department`);
                }
                return { departmentId: departmentId };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const schoolId = result.value.schoolId;
                document.getElementById('department_id_to_delete').value = schoolId;
                document.getElementById('deleteAll').submit();
            }
        });
    }

    function ConfirmDeleteSelected(event, rowId, employeeId, employeeAbbreviation, employeeName) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: `Are you sure you want to delete the employee ${employeeId} - ${employeeAbbreviation} ${employeeName} ?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteSelected');
                // Replace the placeholders with the actual rowId and employeeId
                const actionUrl = form.action.replace(':id', rowId);
                form.action = actionUrl;
                form.submit();
            }
        });

        return false; 
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<script>
function handleImageError(image) {
    // Set the default image
    image.src = "{{ asset('assets/img/user.png') }}";
    
    // Display the error message
    document.getElementById('errorMessage').style.display = 'block';
}
</script>

<script>
         function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
</script>

