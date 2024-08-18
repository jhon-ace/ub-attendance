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
                        <p class="text-black mt-8 text-center">No attendance/s found in <span class="text-red-500">{{ $selectedStudentToShow->student_id }} - {{ $selectedStudentToShow->student_lastname }}, {{ $selectedStudentToShow->student_firstname }} {{ ucfirst($selectedStudentToShow->student_middlename) }}</span> for matching "{{ $search }}"</p>
                        <p class="text-center mt-5">
                            <button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="clearSearch">
                                <i class="fa-solid fa-remove"></i> Clear
                            </button>
                        </p>
                    @elseif(!$search && $attendanceTimeIn->isEmpty() && $attendanceTimeOut->isEmpty())
                        <p class="text-black mt-11 text-center uppercase">No Time In and Time Out Recorded!
                            <button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="clearSearch">
                                <i class="fa-solid fa-remove"></i> Clear
                            </button>
                        </p>
                    @else

                        <div class="flex justify-start mt-1 mb-2 mr-10">
                            <div class="mt-2 text-sm font-bold ">
                                <text class="uppercase">Attendance of Employee: {{ $selectedStudentToShow->student_id }} - {{ $selectedStudentToShow->student_lastname }}, {{ $selectedStudentToShow->student_firstname }} {{ ucfirst($selectedStudentToShow->student_middlename) }}
                            </div>
                            <div class="flex flex-col -mt-12">
                                <div class="flex justify-end items-center mb-2">
                                    <div class="grid grid-rows-2 grid-flow-col ml-10">
                                  
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
                                    <button wire:click="generatePDF" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2 mt-10">
                                        <i class="fa-solid fa-file"></i> Print DTR
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            @if ($attendanceTimeIn->isNotEmpty())
                                @php
                                    $groupedAttendance = [];

                                    foreach ($attendanceTimeIn as $attendanceIn) {
                                        $date = date('Y-m-d', strtotime($attendanceIn->check_in_time));
                                        $studentId = $attendanceIn->student_id;

                                        // Initialize the date group if not set
                                        if (!isset($groupedAttendance[$date])) {
                                            $groupedAttendance[$date] = [];
                                        }

                                        $attendanceRecord = [
                                            'student_id' => $attendanceIn->student->student_id,
                                            'check_in_time' => $attendanceIn->check_in_time,
                                            'check_out_time' => null
                                        ];

                                        // Find the corresponding check-out record
                                        $attendanceOut = $attendanceTimeOut->firstWhere(function ($attendanceOut) use ($date, $studentId) {
                                            return date('Y-m-d', strtotime($attendanceOut->check_out_time)) == $date && $attendanceOut->student_id == $studentId;
                                        });

                                        if ($attendanceOut) {
                                            $attendanceRecord['check_out_time'] = $attendanceOut->check_out_time;
                                        }

                                        $groupedAttendance[$date][] = (object) $attendanceRecord;
                                    }
                                @endphp

                                <div class="flex">
                                    <div class="w-[100%]">
                                        <h1 class="text-center">Attendance Records</h1>
                                        <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                                            <thead class="bg-gray-200 text-black">
                                                <tr>
                                                    <th class="border border-gray-400 px-3 py-2 w-60">Date</th>
                                                    <!-- <th class="border border-gray-400 px-3 py-2">Stud ID</th> -->
                                                    <th class="border border-gray-400 px-3 py-2">Check-In</th>
                                                    <th class="border border-gray-400 px-3 py-2">Check-Out</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($groupedAttendance as $date => $records)
                                                    <tr>
                                                        <!-- Date displayed only in the first row -->
                                                        <td class="text-black border border-gray-400" rowspan="{{ count($records) }}">
                                                            {{ date('m-d-Y (l)', strtotime($date)) }}
                                                        </td>
                                                        @foreach ($records as $index => $record)
                                                            @if ($index > 0)
                                                                <tr class="hover:bg-gray-100">
                                                            @endif
                                                            <!-- <td class="text-black border border-gray-400">{{ $record->student_id }}</td> -->
                                                            <td class="text-black border border-gray-400">
                                                                @if ($record->check_in_time)
                                                                    {{ date('g:i:s A', strtotime($record->check_in_time)) }}
                                                                @else
                                                                    No check-in recorded
                                                                @endif
                                                            </td>
                                                            <td class="text-black border border-gray-400">
                                                                @if ($record->check_out_time)
                                                                    {{ date('g:i:s A', strtotime($record->check_out_time)) }}
                                                                @else
                                                                    No check-out recorded
                                                                @endif
                                                            </td>
                                                            </tr>
                                                        @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <text class="font-bold uppercase">{{ $attendanceTimeIn->links() }}</text>
                                    </div>
                                </div>
                            @else
                                <p>No Time In & Time Out Records found.</p>
                            @endif

                        </div>
                        <!-- <div class="flex justify-items-end justify-end">
                            <p>Overall Total Hours: {{ round($overallTotalHours,2) }}</p>
                        </div> -->
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