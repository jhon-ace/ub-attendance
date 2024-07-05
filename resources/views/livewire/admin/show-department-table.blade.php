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
        <div class="font-bold text-md tracking-tight text-sm text-black  mt-2">Admin / Manage Department</div>
        <div x-data="{ open: false }">
            <button @click="open = true" class="bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add Department
            </button>
            <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div @click.away="open = true" class="w-[35%] bg-white p-6 rounded-lg shadow-lg  mx-auto">
                    <div class="flex justify-between items-center pb-3">
                        <p class="text-xl font-bold">Add Department</p>
                        <button @click="open = false" class=" text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                    </div>
                    <div class="mb-4">
                        <form action="{{ route('admin.department.store') }}" method="POST" class="">
                        <x-caps-lock-detector />
                            @csrf

                                <div class="mb-2">
                                    <label for="school_id" class="block text-gray-700 text-md font-bold mb-2">Department belongs to:</label>
                                    <select id="school_id" name="school_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror" required>
                                        <option value="" selected>Select School</option>
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->abbreviation }} - {{ $school->school_name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                                </div>

                                <div class="mb-2">
                                    <label for="department_id" class="block text-gray-700 text-md font-bold mb-2">Department School ID</label>
                                    <input type="text" name="department_id" id="department_id" value="{{ old('department_id') }}" class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror" required autofocus>
                                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                </div>

                                <div class="mb-2">
                                    <label for="department_abbreviation" class="block text-gray-700 text-md font-bold mb-2">Department Abbreviation</label>
                                    <input type="text" name="department_abbreviation" id="department_abbreviation" value="{{ old('department_abbreviation') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_abbreviation') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('department_abbreviation')" class="mt-2" />
                                </div>

                                <div class="mb-2">
                                    <label for="department_name" class="block text-gray-700 text-md font-bold mb-2">Department Name</label>
                                    <input type="text" name="department_name" id="department_name" value="{{ old('department_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_name') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('department_name')" class="mt-2" />
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
    <div class="flex flex-col md:flex-row items-start md:items-center md:justify-start">
        <!-- Dropdown and Delete Button -->
        <div class="flex items-center w-full md:w-auto">
            <label for="school_id" class="block text-sm text-gray-700 font-bold mt-2 md:mr-4 truncate">Display department by school:</label>
            <select wire:model="selectedSchool" id="school_id" name="school_id" wire:change="updateDepartments"
                    class="cursor-pointer text-sm shadow appearance-none border rounded py-2 px-5 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror md:w-auto"
                    required>
                <option value="">Select School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->abbreviation }} - {{ $school->school_name }}</option>
                @endforeach
            </select>
            <form id="deleteAll" action="{{ route('admin.department.deleteAll') }}" method="POST" onsubmit="return confirmDeleteAll(event);" class="flex ml-4">
                @csrf
                @method('DELETE')
                <input type="hidden" name="school_id" id="school_id_to_delete">
                <button type="submit" class="text-xs lg:text-sm bg-red-500 text-white px-3 py-2 rounded-md hover:bg-red-700">
                    <i class="fa-solid fa-trash fa-sm"></i>
                </button>
            </form>
        </div>
        <!-- Search Input -->
        <div class="w-full flex justify-end mt-4 md:mt-0 md:ml-4">
            <input wire:model.live="search" type="text" class="text-sm border text-black border-gray-300 rounded-md px-3 py-1.5 w-96" placeholder="Search..." autofocus @if(empty($selectedSchool)) disabled @endif>
        </div>
    </div>
    <hr class="border-gray-200 my-4">
    @if($schoolToShow)
        <p class="text-black mt-2 text-sm mb-4">Selected School: {{ $schoolToShow->school_name }}</p>
    @else
        
    @endif
    @if($search && $departments->isEmpty())
     <p class="text-black mt-8 text-center">No employee/s found in {{ $schoolToShow->school_name }} for matching "{{ $search }}"</p>  
    @elseif(!$search && $departments->isEmpty())
        <p class="text-black mt-8 text-center">No data available in table</p>
    @else

        @if($schoolToShow)
            <div class="overflow-x-auto">
                <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                    <thead class="bg-gray-200 text-black">
                        <tr>
                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('department_id')" class="w-full h-full flex items-center justify-center">
                                    Department ID
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
                                <button wire:click="sortBy('department_abbreviation')" class="w-full h-full flex items-center justify-center">
                                    Department Abbreviation
                                    @if ($sortField == 'department_abbreviation')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('department_name')" class="w-full h-full flex items-center justify-center">
                                    Department Name
                                    @if ($sortField == 'department_name')
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
                        @foreach ($departments as $department)
                            <tr class="hover:bg-gray-100" wire:model="selectedDepartment">
                                <td class="text-black border border-gray-400  ">{{ $department->department_id }}</td>
                                <td class="text-black border border-gray-400">{{ $department->department_abbreviation}}</td>
                                <td class="text-black border border-gray-400">{{ $department->department_name}}</td>
                                <td class="text-black border border-gray-400">{{ $department->school->abbreviation }} - {{ $department->school->school_name }}</td>
                                <td class="text-black border border-gray-400 px-1 py-1">
                                    <div class="flex justify-center items-center space-x-2">
                                        <div x-data="{ open: false, 
                                                id: '{{ $department->id }}', 
                                                department_id: '{{ $department->department_id }}',
                                                department_abbreviation: '{{ $department->department_abbreviation }}',
                                                school: '{{ $department->school_id }}',
                                                department_name: '{{ $department->department_name }}',
                                                }">
                                            <a @click="open = true" class="cursor-pointer bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                                                <i class="fa-solid fa-pen fa-xs" style="color: #ffffff;"></i>
                                            </a>
                                            <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                                <div @click.away="open = true" class="w-[35%] bg-white p-6 rounded-lg shadow-lg  mx-auto">
                                                    <div class="flex justify-between items-start pb-3"> <!-- Changed items-center to items-start -->
                                                        <p class="text-xl font-bold">Edit Department</p>
                                                        <a @click="open = false" class="cursor-pointer text-black text-sm px-3 py-2 rounded hover:text-red-500">X</a>
                                                    </div>
                                                    <div class="mb-4">
                                                        <form id="updateStaffForm" action="{{ route('admin.department.update', $department->id )}}" method="POST" class="">
                                                            <x-caps-lock-detector />
                                                            @csrf
                                                            @method('PUT')
                                                                <div class="mb-4">
                                                                    <label for="school_id" class="block text-gray-700 text-md font-bold mb-2 text-left">Department belongs to:</label>
                                                                    <select id="school_id" name="school_id" x-model="school" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror" required>
                                                                        @foreach($schools as $school)
                                                                            <option value="{{ $school->id }}" {{ $department->school_id == $school->id ? 'selected' : '' }}>
                                                                                {{ $school->abbreviation }} - {{ $school->school_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                                                                </div>
                                                                <div class="mb-4">
                                                                    <label for="department_id" class="block text-gray-700 text-md font-bold mb-2 text-left">Department School ID</label>
                                                                    <input type="text" name="department_id" id="department_id" x-model="department_id" class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror" required autofocus>
                                                                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                                                </div>
                                                                <div class="mb-4">
                                                                    <label for="department_abbreviation" class="block text-gray-700 text-md font-bold mb-2 text-left">Department Abbreviation</label>
                                                                    <input type="text" name="department_abbreviation" id="department_abbreviation" x-model="department_abbreviation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_abbreviation') is-invalid @enderror" required>
                                                                    <x-input-error :messages="$errors->get('department_abbreviation')" class="mt-2" />
                                                                </div>

                                                                <div class="mb-4">
                                                                    <label for="department_name" class="block text-gray-700 text-md font-bold mb-2 text-left">Department Name</label>
                                                                    <input type="text" name="department_name" id="department_name" x-model="department_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_name') is-invalid @enderror" required>
                                                                    <x-input-error :messages="$errors->get('department_name')" class="mt-2" />
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
                                        <form id="deleteSelected" action="{{ route('admin.department.destroy', [':id', ':department_id']) }}" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $department->id }}', '{{ $department->department_id }}', '{{ $department->department_abbreviation }}', '{{ $department->department_name }}');">
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
        @else
            <p class="text-black mt-10  text-center">Select table to show data</p>
        @endif
        {{ $departments->links() }}
    @endif
</div>


<script>

function searchDepartments(event) {
        let searchTerm = event.target.value.toLowerCase();
        if (searchTerm === '') {
            this.departmentsToShow = @json($departmentsToShow->toArray());
        } else {
            this.departmentsToShow = this.departmentsToShow.filter(department =>
                department.department_name.toLowerCase().includes(searchTerm) ||
                department.department_abbreviation.toLowerCase().includes(searchTerm) ||
                department.school.school_name.toLowerCase().includes(searchTerm)
            );
        }
    }

        function confirmDeleteAll(event) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: 'Select School to Delete All Records',
            html: `
                <select id="school_id_select" class="swal2-select">
                    <option value="">Select School</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->abbreviation }} - {{ $school->school_name }}</option>
                    @endforeach
                </select>
            `,
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete all!',
            preConfirm: () => {
                const schoolId = Swal.getPopup().querySelector('#school_id_select').value;
                if (!schoolId) {
                    Swal.showValidationMessage(`Please select a school`);
                }
                return { schoolId: schoolId };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const schoolId = result.value.schoolId;
                document.getElementById('school_id_to_delete').value = schoolId;
                document.getElementById('deleteAll').submit();
            }
        });
    }

    function ConfirmDeleteSelected(event, rowId, departmentId, departmentAbbreviation, departmentName) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: `Are you sure you want to delete the department ${departmentId} - ${departmentAbbreviation} ${departmentName} ?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteSelected');
                // Replace the placeholders with the actual rowId and departmentId
                const actionUrl = form.action.replace(':id', rowId).replace(':department_id', departmentId);
                form.action = actionUrl;
                form.submit();
            }
        });

        return false; 
    }




</script>