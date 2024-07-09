<div class="mb-4">
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
        <div class="font-bold text-md tracking-tight text-md text-black  mt-2">Admin / Manage Employee</div>
    </div>

        <div class="flex flex-column overflow-x-auto -mb-5">
            <div class="col-span-3 p-4">
                <label for="school_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate">Display employee by school:</label>
                <select wire:model="selectedSchool" id="school_id" name="school_id" wire:change="updateEmployees"
                        class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror md:w-auto"
                        required>
                    <option value="">Select School</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->id }} | {{ $school->abbreviation }} - {{ $school->school_name }}</option>
                    @endforeach
                </select>
                @if($schoolToShow)
                    <p class="text-black mt-2 text-sm mb-1 ">Selected School ID: <span class="text-red-500 ml-2">{{ $schoolToShow->id }}</span></p>
                    <p class="text-black  text-sm ml-4">Selected School: <span class="text-red-500 ml-2">{{ $schoolToShow->school_name }}</span></p>
                @endif
            </div>

        <div class="col-span-1 p-4">
            @if(!empty($selectedSchool))
                <label for="department_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate">Display by department:</label>
                <select wire:model="selectedDepartment" id="department_id" name="department_id"
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
                    <p class="text-black mt-2 text-sm mb-1">Selected Department ID: <span class="text-red-500 ml-2">{{ $departmentToShow->department_id }}</span></p>
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
    <!--  -->
    @if($departmentToShow)
        @if($search && $employees->isEmpty())
        <p class="text-black mt-8 text-center">No employee/s found in <text class="text-red-500">{{ $departmentToShow->department_name }}</text> for matching "{{ $search }}"</p>
        <p class="text-center mt-5"><button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="$set('search', '')"><i class="fa-solid fa-remove"></i> Clear Search</button></p>
        @elseif(!$search && $employees->isEmpty())
            <p class="text-black mt-8 text-center uppercase">No data available in <text class="text-red-500">{{$departmentToShow->department_abbreviation}} - {{ $departmentToShow->department_name }} department.</text></p>
            <div class="flex justify-center items-center mt-5">
                <div x-data="{ open: false }">
                    <button @click="open = true" class="-mt-1 mb-2 bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                        <!-- <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> {{$departmentToShow->department_id}} - {{$departmentToShow->department_name}} -->
                        <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add Employee in {{$departmentToShow->department_name}} department
                    </button>
                    <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                        <div  class="w-[35%] bg-white p-6 rounded-lg shadow-lg mx-auto max-h-[90vh] overflow-y-auto">
                            <div class="flex justify-between items-center pb-3">
                                <p class="text-xl font-bold">Add Employee</p>
                                <button @click="open = false" class="text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                            </div>
                            <div class="mb-4">
                                <form action="{{ route('admin.employee.store') }}" method="POST" class="" enctype="multipart/form-data">
                                    <x-caps-lock-detector />
                                    @csrf

                                    <div class="mb-2">
                                        <input type="file" name="employee_photo" id="employee_photo" class="hidden" accept="image/*" onchange="previewImage(event)">
                                        <label for="employee_photo" class="cursor-pointer flex flex-col items-center">
                                            <div id="imagePreviewContainer" class="mb-2 text-center">
                                                <img id="imagePreview" src="{{ asset('assets/img/user.png') }}" class="rounded-lg w-48 h-auto">
                                            </div>
                                            <span class="text-sm text-gray-500">Select Photo</span>
                                        </label>
                                        <x-input-error :messages="$errors->get('employee_photo')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="employee_id" class="block text-gray-700 text-md font-bold mb-2">Employee School ID</label>
                                        <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id') }}" class="shadow appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_id') is-invalid @enderror" required autofocus>
                                        <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="employee_lastname" class="block text-gray-700 text-md font-bold mb-2">Employee Lastname</label>
                                        <input type="text" name="employee_lastname" id="employee_lastname" value="{{ old('employee_lastname') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_lastname') is-invalid @enderror" required>
                                        <x-input-error :messages="$errors->get('employee_lastname')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="employee_firstname" class="block text-gray-700 text-md font-bold mb-2">Employee Firstname</label>
                                        <input type="text" name="employee_firstname" id="employee_firstname" value="{{ old('employee_firstname') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_firstname') is-invalid @enderror" required>
                                        <x-input-error :messages="$errors->get('employee_firstname')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="employee_middlename" class="block text-gray-700 text-md font-bold mb-2">Employee Middlename</label>
                                        <input type="text" name="employee_middlename" id="employee_middlename" value="{{ old('employee_middlename') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_middlename') is-invalid @enderror" required>
                                        <x-input-error :messages="$errors->get('employee_middlename')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="employee_rfid" class="block text-gray-700 text-md font-bold mb-2">Employee RF ID No</label>
                                        <input type="text" name="employee_rfid" id="employee_rfid" value="{{ old('employee_rfid') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_rfid') is-invalid @enderror" required>
                                        <x-input-error :messages="$errors->get('employee_rfid')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="school_id" class="block text-gray-700 text-md font-bold mb-2">School ID:</label>
                                        <select id="school_id" name="school_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror" required>
                                            <option value="{{ $departmentToShow->school->id }}">{{ $departmentToShow->school->id }}</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('id')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="department_id" class="block text-gray-700 text-md font-bold mb-2">Department ID:</label>
                                        <select id="department_id" name="department_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror" required>
                                            <option value="{{ $departmentToShow->id }}">{{ $departmentToShow->department_id }}</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('id')" class="mt-2" />
                                    </div>
                                    <div class="flex mb-4 mt-10 justify-center">
                                        <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="flex justify-between">
                <div class="">
                    @if($departmentToShow)
                    <!-- <form id="deleteAll" action="{{ route('admin.employee.deleteAll') }}" method="POST" onsubmit="return confirmDeleteAll(event);" class="flex ml-4">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="department_id" value="{{$departmentToShow->id }}" id="department_id_to_delete">
                        
                        <button type="submit" class="text-xs lg:text-sm bg-red-500 text-white px-3 py-2 -ml-3 -mt-1 rounded-md hover:bg-red-700
                            @if(empty($selectedSchool) || empty($selectedDepartment)) cursor-not-allowed opacity-50 @endif"
                            @if(empty($selectedSchool) || empty($selectedDepartment)) disabled @endif>
                            <i class="fa-solid fa-trash fa-sm"></i> Delete All Records
                        </button>
                    </form> -->
                    @else

                    @endif
                </div>
                <div class="flex justify-center items-center">
                    <div x-data="{ open: false }">
                        <button @click="open = true" class="-mt-1 mb-2 bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                            <!-- <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> {{$departmentToShow->department_id}} - {{$departmentToShow->department_name}} -->
                            <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add Employee
                        </button>
                        <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                            <div  class="w-[35%] bg-white p-6 rounded-lg shadow-lg mx-auto max-h-[90vh] overflow-y-auto">
                                <div class="flex justify-between items-center pb-3">
                                    <p class="text-xl font-bold">Add Employee</p>
                                    <button @click="open = false" class="text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                                </div>
                                <div class="mb-4">
                                    <form action="{{ route('admin.employee.store') }}" method="POST" class="" enctype="multipart/form-data">
                                        <x-caps-lock-detector />
                                        @csrf

                                        <div class="mb-2">
                                            <input type="file" name="employee_photo" id="employee_photo" class="hidden" accept="image/*" onchange="previewImage(event)">
                                            <label for="employee_photo" class="cursor-pointer flex flex-col items-center">
                                                <div id="imagePreviewContainer" class="mb-2 text-center">
                                                    <img id="imagePreview" src="{{ asset('assets/img/user.png') }}" class="rounded-lg w-48 h-auto">
                                                </div>
                                                <span class="text-sm text-gray-500">Select Photo</span>
                                            </label>
                                            <x-input-error :messages="$errors->get('employee_photo')" class="mt-2" />
                                        </div>

                                        <div class="mb-2">
                                            <label for="employee_id" class="block text-gray-700 text-md font-bold mb-2">Employee School ID</label>
                                            <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id') }}" class="shadow appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_id') is-invalid @enderror" required autofocus>
                                            <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                                        </div>
                                        <div class="mb-2">
                                            <label for="employee_rfid" class="block text-gray-700 text-md font-bold mb-2">Employee RF ID No</label>
                                            <input type="text" name="employee_rfid" id="employee_rfid" value="{{ old('employee_rfid') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_rfid') is-invalid @enderror" required>
                                            <x-input-error :messages="$errors->get('employee_rfid')" class="mt-2" />
                                        </div>
                                        <div class="mb-2">
                                            <label for="employee_lastname" class="block text-gray-700 text-md font-bold mb-2">Employee Lastname</label>
                                            <input type="text" name="employee_lastname" id="employee_lastname" value="{{ old('employee_lastname') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_lastname') is-invalid @enderror" required>
                                            <x-input-error :messages="$errors->get('employee_lastname')" class="mt-2" />
                                        </div>

                                        <div class="mb-2">
                                            <label for="employee_firstname" class="block text-gray-700 text-md font-bold mb-2">Employee Firstname</label>
                                            <input type="text" name="employee_firstname" id="employee_firstname" value="{{ old('employee_firstname') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_firstname') is-invalid @enderror" required>
                                            <x-input-error :messages="$errors->get('employee_firstname')" class="mt-2" />
                                        </div>
                                        <div class="mb-2">
                                            <label for="employee_middlename" class="block text-gray-700 text-md font-bold mb-2">Employee Middlename</label>
                                            <input type="text" name="employee_middlename" id="employee_middlename" value="{{ old('employee_middlename') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_middlename') is-invalid @enderror" required>
                                            <x-input-error :messages="$errors->get('employee_middlename')" class="mt-2" />
                                        </div>
                                        <div class="mb-2">
                                            <label for="school_id" class="block text-gray-700 text-md font-bold mb-2">School ID:</label>
                                            <select id="school_id" name="school_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror" required>
                                                <!-- <option value="{{ $departmentToShow->school->id }}">{{ $departmentToShow->school->id }} | {{ $departmentToShow->school->school_name }}</option> -->
                                                 <option value="{{ $departmentToShow->school->id }}">{{ $departmentToShow->school->id }}</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                                        </div>

                                        <div class="mb-2">
                                            <label for="department_id" class="block text-gray-700 text-md font-bold mb-2">Department ID:</label>
                                            <select id="department_id" name="department_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror" required>
                                                <!-- <option value="{{ $departmentToShow->id }}">{{ $departmentToShow->department_id }} | {{ $departmentToShow->department_name }}</option> -->
                                                <option value="{{ $departmentToShow->id }}">{{ $departmentToShow->department_id }}</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('id')" class="mt-2" />
                                        </div>
                                        <div class="flex mb-4 mt-10 justify-center">
                                            <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
                                                Save
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-between mt-1 mb-2">
                <div class="mt-2 text-sm font-bold uppercase">
                    Employee List in {{$departmentToShow->department_abbreviation}} - {{$departmentToShow->department_name}} department
                </div>
                <div>
                    <input wire:model.live="search" type="text" class="text-sm border text-black border-gray-300 rounded-md px-3 py-1.5 w-full md:w-64" placeholder="Search..." autofocus>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                    <thead class="bg-gray-200 text-black">
                        <!-- <tr >
                            <th colspan="9" class="border-none bg-white border border-gray-400 px-3 py-2 uppercase"></th>
                        </tr> -->
                        <tr>
                            <th class="border border-gray-400 px-3 py-2">

                                <button wire:click="sortBy('employee_id')" class="w-full h-full flex items-center justify-center">
                                    Emp ID
                                    @if ($sortField == 'employee_id')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('employee_rfid')" class="w-full h-full flex items-center justify-center">
                                    RFID No
                                    @if ($sortField == 'employee_rfid')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="border border-gray-400 px-3 py-2">
                                Photo
                            </th>
                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('employee_lastname')" class="w-full h-full flex items-center justify-center">
                                    Lastname
                                    @if ($sortField == 'employee_lastname')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('employee_firstname')" class="w-full h-full flex items-center justify-center">
                                    Firstname
                                    @if ($sortField == 'employee_firstname')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('employee_middlename')" class="w-full h-full flex items-center justify-center">
                                    Middlename
                                    @if ($sortField == 'employee_middlename')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('department_id')" class="w-full h-full flex items-center justify-center">
                                    Dept ID
                                    @if ($sortField == 'department_id')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('school_id')" class="w-full h-full flex items-center justify-center">
                                    School
                                    @if ($sortField == 'school_id')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="border border-gray-400 px-3 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody >
                        @foreach ($employees as $employee)
                            <tr class="hover:bg-gray-100" wire:model="selectedDepartment">
                                <td class="text-black border border-gray-400">{{ $employee->employee_id}}</td>
                                <td class="text-black border border-gray-400">{{ $employee->employee_rfid}}</td>
                                <td class="text-black border border-gray-400 border-t-0 border-r-0 border-l-0 px-2 py-1 flex items-center justify-center" >
                                    @if ($employee->employee_photo && Storage::exists('public/employee_photo/' . $employee->employee_photo))
                                        <a  href="{{ asset('storage/employee_photo/' . $employee->employee_photo) }}" 
                                            class="hover:border border-red-500 rounded-full" title="Click to view Picture"
                                            data-fancybox data-caption="{{ $employee->employee_lastname}}, {{ $employee->employee_firstname }} {{ ucfirst(substr($employee->employee_middlename, 0, 1)) }}">
                                            <img src="{{ asset('storage/employee_photo/' . $employee->employee_photo) }}" class="rounded-full w-9 h-9">
                                        </a>
                                    @else
                                        <img data-fancybox src="{{ asset('assets/img/user.png') }}" class="cursor-pointer w-9 h-9 hover:border hover:border-red-500 rounded-full" title="Click to view Picture" >
                                    @endif
                                </td>
                                <td class="text-black border border-gray-400">{{ $employee->employee_lastname}}</td>
                                <td class="text-black border border-gray-400">{{ $employee->employee_firstname}}</td>
                                <td class="text-black border border-gray-400">{{ $employee->employee_middlename}}</td>
                                <td class="text-black border border-gray-400">{{ $employee->department->department_id}}</td>
                                <td class="text-black border border-gray-400">{{ $employee->school->id}}</td>
                                <td class="text-black border border-gray-400">
                                    <div class="flex justify-center items-center space-x-2">
                                        <div x-data="{ open: false
                                                }">
                                            <a @click="open = true" class="cursor-pointer bg-blue-500 text-white text-sm px-2 py-[5px] rounded hover:bg-blue-700">
                                                <i class="fa-solid fa-pen fa-xs" style="color: #ffffff;"></i>
                                            </a>
                                            <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                                <div @click.away="open = true" class="w-[35%] bg-white p-6 rounded-lg shadow-lg max-h-[90vh] overflow-y-auto  mx-auto">
                                                    <div class="flex justify-between items-start pb-3"> <!-- Changed items-center to items-start -->
                                                        <p class="text-xl font-bold">Edit employee</p>
                                                        <a @click="open = false" class="cursor-pointer text-black text-sm px-3 py-2 rounded hover:text-red-500">X</a>
                                                    </div>
                                                    <div class="mb-4">
                                                        <form action="{{ route('admin.employee.update', $employee->id)}}" method="POST" enctype="multipart/form-data">
                                                            <x-caps-lock-detector />
                                                            @csrf
                                                            @method('PUT')

                                                                <div class="mb-4">
                                                                    <input type="file" name="employee_photo" id="employee_photo" class="hidden" accept="image/*" onchange="previewImage(event)">
                                                                    <label for="employee_photo" class="cursor-pointer flex flex-col items-center">
                                                                        <div id="imagePreviewContainer" class="mb-2 text-center">
                                                                            <img id="imagePreview" src="{{ $employee->employee_photo ? asset('storage/employee_photo/' . $employee->employee_photo) : asset('assets/img/user.png') }}"
                                                                            class="rounded-full w-48 h-auto" onerror="handleImageError(this)">
                                                                        </div>
                                                                        <span class="text-sm text-gray-500">Select Photo</span>
                                                                        <span id="errorMessage" class="text-sm" style="display: none; color: red;">Failed to load image from database, default photo displayed.</span>
                                                                        
                                                                    </label>
                                                                    <x-input-error :messages="$errors->get('employee_photo')" class="mt-2" />
                                                                </div>
                                                                <div class="mb-4">
                                                                    <label for="employee_id" class="block text-gray-700 text-md font-bold mb-2 text-left">Employee School ID</label>
                                                                    <input type="text"  name="employee_id" id="employee_id" value="{{ $employee->employee_id }}"  class="shadow appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror" required autofocus>
                                                                    <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                                                                </div>
                                                                <div class="mb-4">
                                                                    <label for="employee_rfid" class="block text-gray-700 text-md font-bold mb-2 text-left">Employee RFID No</label>
                                                                    <input type="text" name="employee_rfid" id="employee_rfid" value="{{ $employee->employee_rfid }}"  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_middlename') is-invalid @enderror" required>
                                                                    <x-input-error :messages="$errors->get('employee_rfid')" class="mt-2" />
                                                                </div>
                                                                <div class="mb-4">
                                                                    <label for="employee_lastname" class="block text-gray-700 text-md font-bold mb-2 text-left">Employee Lastname</label>
                                                                    <input type="text" name="employee_lastname" id="employee_lastname" value="{{ $employee->employee_lastname }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_lastname') is-invalid @enderror" required>
                                                                    <x-input-error :messages="$errors->get('employee_lastname')" class="mt-2" />
                                                                </div>

                                                                <div class="mb-4">
                                                                    <label for="employee_firstname" class="block text-gray-700 text-md font-bold mb-2 text-left">Employee Firstname</label>
                                                                    <input type="text" name="employee_firstname" id="employee_firstname"  value="{{ $employee->employee_firstname }}"  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_firstname') is-invalid @enderror" required>
                                                                    <x-input-error :messages="$errors->get('employee_firstname')" class="mt-2" />
                                                                </div>
                                                                <div class="mb-4">
                                                                    <label for="employee_middlename" class="block text-gray-700 text-md font-bold mb-2 text-left">Employee Middlename</label>
                                                                    <input type="text" name="employee_middlename" id="employee_middlename" value="{{ $employee->employee_middlename }}"  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_middlename') is-invalid @enderror" required>
                                                                    <x-input-error :messages="$errors->get('employee_middlename')" class="mt-2" />
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label for="school_id" class="block text-gray-700 text-md font-bold mb-2 text-left">School ID:</label>
                                                                    <select id="school_id" name="school_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror" required>
                                                                        <option value="{{ $departmentToShow->school->id }}">{{ $departmentToShow->school->id }}</option>
                                                                    </select>
                                                                    <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                                                                </div>

                                                                <div class="mb-2">
                                                                    <label for="department_id" class="block text-gray-700 text-md font-bold mb-2 text-left">Department ID:</label>
                                                                    <select id="department_id" name="department_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror" required>
                                                                        <option value="{{ $departmentToShow->id }}">{{ $employee->department->id }}</option>
                                                                    </select>
                                                                    <x-input-error :messages="$errors->get('id')" class="mt-2" />
                                                                </div>
                                                            <div class="flex mb-4 mt-10 justify-center">
                                                                <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
                                                                    Save Changes
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <form id="deleteSelected" action="{{ route('admin.employee.destroy', [':id']) }}" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $employee->id }}', '{{ $employee->employee_lastname }}', '{{ $employee->employee_firstname }}', '{{ $employee->employee_middlename }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="bg-red-500 text-white text-sm px-2 py-1 rounded hover:bg-red-700" id="hehe">
                                                <i class="fa-solid fa-trash fa-xs" style="color: #ffffff;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($departmentToShow)
                    <tr>
                        <td colspan="2">
                            <div class="flex justify-between">
                                <div class="uppercase text-black mt-2 text-sm mb-4">
                                    @if($search)
                                        {{ $employees->total() }} Search results 
                                    @endif                                    
                                </div>
                                <div class="justify-end">
                                    <p class="text-black mt-2 text-sm mb-4 uppercase">Total # of Employee: <text class="ml-2">{{ $departmentCounts[$departmentToShow->id]->employee_count ?? 0 }}</text></p>
                                    @if($search)
                                        <p><button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="$set('search', '')"><i class="fa-solid fa-remove"></i> Clear Search</button></p>
                                    @endif
                                </div>
                            </div> 
                        </td>
                    </tr>
                @endif
            </div>
            
            <text  class="font-bold uppercase">{{ $employees->links() }}</text>
        @endif
    @else
        
    @endif
   
</div>



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
