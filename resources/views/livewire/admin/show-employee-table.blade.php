<div>
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
        <div class="font-bold text-md tracking-tight text-sm text-black  mt-2">Admin / Manage Employee</div>
        <div x-data="{ open: false }">
            <button @click="open = true" class="bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add Employee
            </button>
            <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div @click.away="open = true" class="w-[35%] bg-white p-6 rounded-lg shadow-lg  mx-auto">
                    <div class="flex justify-between items-center pb-3">
                        <p class="text-xl font-bold">Add Employee</p>
                        <button @click="open = false" class=" text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                    </div>
                    <div class="mb-4">
                        <form action="{{ route('admin.employee.store') }}" method="POST" class="">
                        <x-caps-lock-detector />
                            @csrf

                            <div class="mb-4 grid grid-cols-2 gap-4">
                                <div>
                                    <label for="school_id" class="block text-gray-700 text-md font-bold mb-2">Employee belongs to:</label>
                                    <select wire:model="selectedSchool" id="school_id" name="school_id" wire:change="updateDepartments"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror"
                                            required>
                                        <option value="">Select School</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->abbreviation }} - {{ $school->school_name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="department_id" class="block text-gray-700 text-md font-bold mb-2">Departments:</label>
                                    <select wire:model="selectedDepartment" id="department_id" name="department_id"
                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror"
                                            @if(empty($selectedSchool)) disabled @endif required>
                                        <option value="">Select Department</option>
                                        @if ($departments->isEmpty())
                                            <option value="0">No department</option>
                                        @else
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->department_abbreviation }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="employee_id" class="block text-gray-700 text-md font-bold mb-2">Employee School ID</label>
                                    <input type="text" name="employee_id" id="employee_id" value="{{ old('employee_id') }}" class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_id') is-invalid @enderror" required autofocus>
                                    <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="employee_firstname" class="block text-gray-700 text-md font-bold mb-2">First Name</label>
                                    <input type="text" name="employee_firstname" id="employee_firstname" value="{{ old('employee_firstname') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_firstname') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('employee_firstname')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="employee_middlename" class="block text-gray-700 text-md font-bold mb-2">Middle Name</label>
                                    <input type="text" name="employee_middlename" id="employee_middlename" value="{{ old('employee_middlename') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_middlename') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('employee_middlename')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="employee_lastname" class="block text-gray-700 text-md font-bold mb-2">Last Name</label>
                                    <input type="text" name="employee_lastname" id="employee_lastname" value="{{ old('employee_lastname') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('staff_lastname') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('employee_lastname')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="employee_rfid" class="block text-gray-700 text-md font-bold mb-2">RF ID No</label>
                                    <input type="text" name="employee_rfid" id="employee_rfid" value="{{ old('employee_rfid') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('staff_rfid') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('employee_rfid')" class="mt-2" />
                                </div>

                                
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
    <hr class="border-gray-200 my-4">
    <div class="flex items-center mb-4 justify-between">
    <div class="flex w-24 mr-2 sm:mr-0">
        <form id="deleteAll" action="{{ route('admin.employee.deleteAll') }}" method="POST" onsubmit="return confirmDeleteAll(event);">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs lg:text-sm w-full mt-2 bg-red-500  text-white px-4 py-2.5 rounded-md hover:bg-red-700">
                Delete All
            </button>
        </form>
    </div>
    <div class="flex w-full sm:w-auto mt-2 sm:mt-0 sm:ml-2">
        <input wire:model.live="search" type="text" class="border text-black border-gray-300 rounded-md p-2 w-full" placeholder="Search..." autofocus>
    </div>
</div>


    @if($search && $employees->isEmpty())
        <p class="text-black mt-8 text-center">No employee/s found for matching "{{ $search }}"</p>
    @elseif(!$search && $employees->isEmpty())
        <p class="text-black mt-8 text-center">No data available in table</p>
    @else
    <div class="overflow-x-auto">
        <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
            <thead class="bg-gray-200 text-black">
                <tr>
                    <th class="border border-gray-400 px-3 py-2">
                        <button wire:click="sortBy('employee_id')" class="w-full h-full flex items-center justify-center">
                            Employee ID
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
                        <button wire:click="sortBy('employee_lastname')" class="w-full h-full flex items-center justify-center">
                            Last Name
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
                            First Name
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
                            Middle Name
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
                        <button wire:click="sortBy('department_id')" class="w-full h-full flex items-center justify-center">
                            Department
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
            <tbody>
                @foreach ($employees as $employee)
                    <tr class="hover:bg-gray-100">
                        <td class="text-black border border-gray-400  ">{{ $employee->employee_id }}</td>
                        <td class="text-black border border-gray-400">{{ $employee->employee_lastname}}</td>
                        <td class="text-black border border-gray-400">{{ $employee->employee_firstname}}</td>
                        <td class="text-black border border-gray-400">{{ $employee->employee_middlename}}</td>
                        <td class="text-black border border-gray-400">{{ $employee->employee_rfid}}</td>
                        <td class="text-black border border-gray-400">
                             @if ($employee->department)
                                {{ $employee->department->department_id }} - {{ $employee->department->department_abbreviation }} 
                            @else
                                No department assigned
                            @endif
                        </td>
                        <td class="text-black border border-gray-400">{{ $employee->school->abbreviation }} - {{ $employee->school->school_name }}</td>
                        <td class="text-black border border-gray-400 px-1 py-1">
                            <div class="flex justify-center items-center space-x-2">
                                <div x-data="{ open: false, 
                                        id: '{{ $employee->id }}', 
                                        employee_id: '{{ $employee->employee_id }}',
                                        employee_name: '{{ $employee->employee_name }}',
                                        school: '{{ $employee->school_id }}',
                                        employee_firstname: '{{ $employee->employee_firstname }}',
                                        employee_middlename: '{{ $employee->employee_middlename }}',
                                        employee_lastname: '{{ $employee->employee_lastname }}',
                                        employee_rfid: '{{ $employee->employee_rfid }}',
                                        }">
                                    <a @click="open = true" class="cursor-pointer bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                                        <i class="fa-solid fa-pen fa-xs" style="color: #ffffff;"></i>
                                    </a>
                                    <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                        <div @click.away="open = true" class="w-[35%] bg-white p-6 rounded-lg shadow-lg  mx-auto">
                                            <div class="flex justify-between items-start pb-3"> <!-- Changed items-center to items-start -->
                                                <p class="text-xl font-bold">Edit Employee</p>
                                                <a @click="open = false" class="cursor-pointer text-black text-sm px-3 py-2 rounded hover:text-red-500">X</a>
                                            </div>
                                            <div class="mb-4">
                                                <form id="updateStaffForm" action="{{ route('admin.employee.update', $employee->id )}}" method="POST" class="">
                                                    <x-caps-lock-detector />
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-4 grid grid-cols-2 gap-4">
                                                        <div>
                                                            <div class="mb-4">
                                                                <label for="school_id" class="block text-gray-700 text-md font-bold mb-2 text-left">Staff belongs to:</label>
                                                                <select id="school_id" name="school_id" x-model="school" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror" required>
                                                                    @foreach($schools as $school)
                                                                        <option value="{{ $school->id }}" {{ $employee->school_id == $school->id ? 'selected' : '' }}>
                                                                            {{ $school->abbreviation }} - {{ $school->school_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="mb-4">
                                                                <label for="employee_id" class="block text-gray-700 text-md font-bold mb-2 text-left">Employee School ID</label>
                                                                <input type="text" name="employee_id" id="employee_id" x-model="employee_id" class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_id') is-invalid @enderror" required autofocus>
                                                                <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="mb-4">
                                                                <label for="employee_firstname" class="block text-gray-700 text-md font-bold mb-2 text-left">First Name</label>
                                                                <input type="text" name="employee_firstname" id="employee_firstname" x-model="employee_firstname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_firstname') is-invalid @enderror" required>
                                                                <x-input-error :messages="$errors->get('employee_firstname')" class="mt-2" />
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="mb-4">
                                                                <label for="employee_middlename" class="block text-gray-700 text-md font-bold mb-2 text-left">Middle Name</label>
                                                                <input type="text" name="employee_middlename" id="employee_middlename" x-model="employee_middlename" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_middlename') is-invalid @enderror" required>
                                                                <x-input-error :messages="$errors->get('employee_middlename')" class="mt-2" />
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="mb-4">
                                                                <label for="employee_lastname" class="block text-gray-700 text-md font-bold mb-2 text-left">Last Name</label>
                                                                <input type="text" name="employee_lastname" id="employee_lastname" x-model="employee_lastname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('staff_lastname') is-invalid @enderror" required>
                                                                <x-input-error :messages="$errors->get('employee_lastname')" class="mt-2" />
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="mb-4">
                                                                <label for="employee_rfid" class="block text-gray-700 text-md font-bold mb-2 text-left">RFID No</label>
                                                                <input type="text" name="employee_rfid" id="employee_rfid" x-model="employee_rfid" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('employee_rfid') is-invalid @enderror" required>
                                                                <x-input-error :messages="$errors->get('employee_rfid')" class="mt-2" />
                                                            </div>
                                                        </div>
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
                                <form id="deleteSelected" action="{{ route('admin.employee.destroy', [':id', ':employee_id']) }}" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $employee->id }}', '{{ $employee->employee_id }}', '{{ $employee->employee_lastname }}', '{{ $employee->employee_firstname }}', '{{ $employee->employee_middlename }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white text-sm px-3 py-2 rounded hover:bg-red-700">
                                        <i class="fa-solid fa-trash fa-xs" style="color: #ffffff;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
        {{ $employees->links() }}
    @endif
</div>


<script>

    function confirmDeleteAll(event) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: 'Are you sure to delete all records?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete all!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, submit the form programmatically
                document.getElementById('deleteAll').submit();
            }
        });
    }

    function ConfirmDeleteSelected(event, rowId, employeeId, employeeLastname, employeeFirstname, employeeMiddlename) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: `Are you sure you want to delete the employee ${employeeFirstname} ${employeeMiddlename} ${employeeLastname}?`,
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
                const actionUrl = form.action.replace(':id', rowId).replace(':employee_id', employeeId);
                form.action = actionUrl;
                form.submit();
            }
        });

        return false; 
    }




</script>