<div class="mb-4">
        @php
            session(['selectedSchool' => $selectedSchool]);
            session(['selectedDepartment5' => $selectedDepartment5]);
            session(['selectedCourse5' => $selectedCourse5]);
            session(['selectedStudent5' => $selectedStudent5]);
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
        <div class="font-bold text-md tracking-tight text-md text-black  mt-2 uppercase">Admin / Manage Student</div>
    </div>

    <div class="flex flex-column overflow-x-auto -mb-5">
        <div class="col-span-3 p-4">
            <label for="school_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate uppercase">School Year:</label>
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
                <label for="department_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate uppercase">Department:</label>
                <select wire:model="selectedDepartment5" id="department_id" name="department_id"
                        wire:change="updateEmployeesByDepartment"
                        class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror md:w-auto"
                        required>
                    @if($departments->isEmpty())
                        <option value="0">No Departments</option>
                    @else
                        <option value="">Select Department</option>
                            @foreach($departments as $department)
                                @php
                                    $cleanedAbbreviation = str_replace('- student', '', $department->department_abbreviation);
                                @endphp
                                <option value="{{ $department->id }}">{{ $cleanedAbbreviation }}</option>
                            @endforeach
                    @endif
                </select>
                @if($departmentToShow)
                    @php
                        $cleanedAbbreviation = str_replace('- student', '', $departmentToShow->department_abbreviation);
                    @endphp

                    <p class="text-black mt-2 text-sm mb-1">
                        Selected Department: 
                        <span class="text-red-500 ml-2">{{ $cleanedAbbreviation }}</span>
                    </p>

                    <!-- <p class="text-black text-sm ml-4">Selected Department: <span class="text-red-500 ml-2">{{ $departmentToShow->department_name }}</span></p> -->
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
    <!--  -->
    
    @if($departmentToShow)
        <label for="course_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate uppercase">Display student by courses:</label>
        <select wire:model="selectedCourse5" id="course_id" name="course_id"
                wire:change="updateStudentsByCourse"
                class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror md:w-auto"
                required>
            @if($courses->isEmpty())
                <option value="0">No Courses yet</option>
            @else
                <option value="">Select Course</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->course_id }} | {{ $course->course_name }}({{ $course->course_abbreviation }})</option>
                @endforeach
            @endif
        </select>

        @if($selectedCourseToShow)
            <p>Selected Course: {{ $selectedCourseToShow->course_id }} - {{ $selectedCourseToShow->course_name }}({{ $selectedCourseToShow->course_abbreviation}})</p>
        @endif

        @if($selectedCourseToShow)
            @if($search && $students->isEmpty())
                <p class="text-black mt-8 text-center">No student/s found in <span class="text-red-500">{{ $selectedCourseToShow->course_id }} - {{ $selectedCourseToShow->course_name }}({{ $selectedCourseToShow->course_abbreviation}})</span> for matching "{{ $search }}"</p>
                <p class="text-center mt-5"><button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="$set('search', '')"><i class="fa-solid fa-remove"></i> Clear Search</button></p>
            @elseif(!$search && $students->isEmpty())
                <p class="text-black mt-8 text-center uppercase">No student available in <text class="text-red-500">{{ $selectedCourseToShow->course_id }} - {{ $selectedCourseToShow->course_name }}({{ $selectedCourseToShow->course_abbreviation}}) department.</text></p>
            @else          
                <label for="department_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate">Display attendance:</label>
                <select wire:model="selectedStudent5" id="department_id" name="department_id"
                        wire:change="updateAttendanceByStudent"
                        class="cursor-pointer text-sm shadow appearance-none border  rounded text-black leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror md:w-auto"
                        required>
                    @if($students->isEmpty())
                        <option value="0">No Students</option>
                    @else
                        <option value="" selected>Select Students</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->student_id }} - {{ $student->student_lastname }}, {{ $student->student_firstname }} {{ ucfirst($student->employee_student) }}</option>
                        @endforeach
                    @endif
                </select>
                @if($selectedStudentToShow)
                    @if($search && $attendanceTimeIn->isEmpty() && $attendanceTimeOut->isEmpty() && !$selectedAttendanceToShow->isEmpty())
                        <p class="text-black mt-8 text-center">No attendance/s found in <span class="text-red-500">{{ $selectedStudentToShow->student_id }} - {{ $selectedStudentToShow->student_lastname }}, {{ $selectedStudentToShow->student_firstname }} {{ ucfirst($selectedStudentToShow->student_middlename) }} </span> for matching "{{ $search }}"</p>
                    @elseif(!$search && $attendanceTimeIn->isEmpty() && $attendanceTimeOut->isEmpty())
                        <p class="text-black mt-8 text-center uppercase">No data available in student <text class="text-red-500">{{ $selectedStudentToShow->student_id }} - {{ $selectedStudentToShow->student_lastname }}, {{ $selectedStudentToShow->student_firstname }} {{ ucfirst($selectedStudentToShow->student_middlename) }}</text></p>
                    @else

                    <div class="flex justify-start mt-1 mb-2">
                        <div class="mt-2 text-sm font-bold ">
                            <text class="uppercase">Attendance of Employee: {{ $selectedStudentToShow->student_id }} - {{ $selectedStudentToShow->student_lastname }}, {{ $selectedStudentToShow->student_firstname }} {{ ucfirst($selectedStudentToShow->student_middlename) }}
                        </div>
                        <!-- <div class="flex flex-col">
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
                                           
                                        >

                                        <label for="endDate" class="text-gray-600">End Date:</label>
                                        <input 
                                            id="endDate" 
                                            type="date" 
                                            class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                            wire:model="endDate"
                                           
                                        >

                                    </div>
                                </div>
                                <button wire:click="generatePDF" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">
                                    <i class="fa-solid fa-file"></i> Print DTR
                                </button>
                            </div>
                        </div> -->
                    </div>
                    <div class="overflow-x-auto">
                        <div class="flex">
                            <!-- Table for Time In -->
                            <div class="w-[29%]">
                                <h3 class="text-center">Time In</h3>
                                <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                                    <thead class="bg-gray-200 text-black">
                                        <tr>
                                            <th class="border border-gray-400 px-3 py-2">
                                                Stud ID
                                            </th>
                                            <th class="border border-gray-400 px-3 py-2">
                                                Date
                                            </th>
                                            <th class="border border-gray-400 px-3 py-2">
                                                Check-In
                                            </th>
                                            <!-- Add other columns as needed -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attendanceTimeIn as $attendanceIn)
                                            <tr class="hover:bg-gray-100">
                                                <td class="text-black border border-gray-400">{{ $attendanceIn->student->student_id }}</td>
                                                <td class="text-black border border-gray-400">
                                                    {{ date('m-d-Y (l)', strtotime($attendanceIn->check_in_time)) }}
                                                </td>
                                                <td class="text-black border border-gray-400">{{ date('g:i:s A', strtotime($attendanceIn->check_in_time)) }}</td>
                                                <!-- Add other columns as needed -->
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <text  class="font-bold uppercase">{{ $attendanceTimeIn->links() }}</text>
                            <div class="w-[1%]"></div>
                            <!-- Table for Time Out -->
                            <div class="w-[24%]">
                                <h3 class="text-center">Time Out</h3>
                                    <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                                        <thead class="bg-gray-200 text-black">
                                            <tr>
                                                <th class="border border-gray-400 px-3 py-2">
                                                    Date
                                                </th>
                                                <th class="border border-gray-400 px-3 py-2">
                                                    Check-Out
                                                </th>
                                                <!-- Add other columns as needed -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($attendanceTimeOut as $attendanceOut)
                                                <tr class="hover:bg-gray-100">
                                                    <td class="text-black border border-gray-400">
                                                        @if ($attendanceOut->check_out_time)
                                                            {{ date('m-d-Y, (l)', strtotime($attendanceOut->check_out_time)) }}
                                                        @else
                                                            No time out recorded
                                                        @endif
                                                    </td>
                                                    <td class="text-black border border-gray-400">{{ date('g:i:s A', strtotime($attendanceOut->check_out_time)) }}</td>
                                                    <!-- Add other columns as needed -->
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <text  class="font-bold uppercase">{{ $attendanceTimeOut->links() }}</text>
                            </div>
                            <div class="w-[1%]"></div>
                           <div class="w-[47%]">
                                <h3 class="text-center">Student Time Monitoring</h3>
                                <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                                    <thead class="bg-gray-200 text-black">
                                        <tr>
                                            <th class="border border-gray-400 px-3 py-2">
                                                Date
                                            </th>
                                            <th class="border border-gray-400 px-3 py-2 text-xs">
                                               AM - hrs. in Campus 
                                            </th>
                                            <th class="border border-gray-400 px-3 py-2 text-xs">
                                                PM - hrs. in Campus 
                                            </th>
                                            <th class="border border-gray-400 px-3 py-2 text-xs">
                                                Total
                                            </th>
                                           <th class="border border-gray-400 px-3 py-2">
                                                Remarks
                                            </th>
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
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-items-end justify-end">
                        <p>Overall Total Hours: {{ round($overallTotalHours,2) }}</p>
                    </div>
                    @endif
                @else
                    <p>No selected Student</p>
                @endif
            @endif
        @else
            @if($courses->isEmpty())
                <p class="text-black text-sm mt-11 mb-4 uppercase text-center">Add Course first in the department</p>
            @else
                <p class="text-black text-sm mt-11 mb-4 uppercase text-center">No selected Course</p>
            @endif
            
        @endif

    @endif
    
</div>
    

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

    function ConfirmDeleteSelected(event, rowId, studentLastname, studentFirstname, studentMiddlename) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: `Are you sure you want to delete this student:  ${studentLastname}, ${studentFirstname} ${studentMiddlename}?`,
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
<!--  -->
<script>
         function readURL2(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#blah2')
                        .attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
</script>
<!--  -->