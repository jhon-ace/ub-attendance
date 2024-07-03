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
        <div class="font-bold text-md tracking-tight text-black  mt-2">Admin / Manage Staff</div>
        <div x-data="{ open: false }">
            <button @click="open = true" class="bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add Staff
            </button>
            <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                <div @click.away="open = true" class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
                    <div class="flex justify-between items-center pb-3">
                        <p class="text-xl font-bold">Add Staff</p>
                        <button @click="open = false" class=" text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                    </div>
                    <div class="mb-4">
                        <form action="{{ route('admin.staff.store') }}" method="POST" class="">
                        <x-caps-lock-detector />
                            @csrf
                            <div class="mb-4">
                                <label for="school_id" class="block text-gray-700 text-md font-bold mb-2">Staff belongs to:</label>
                                <select id="school_id" name="school_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror" required>
                                    <option value="" selected>Select School</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}">{{ $school->abbreviation }} - {{ $school->school_name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <label for="staff_id" class="block text-gray-700 text-md font-bold mb-2">School ID</label>
                                <input type="text" name="staff_id" id="staff_id" value="{{ old('staff_id') }}"  class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_name') is-invalid @enderror" required autofocus>
                                <x-input-error :messages="$errors->get('staff_id')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <label for="staff_name" class="block text-gray-700 text-md font-bold mb-2">Full Name</label>
                                <input type="text" name="staff_name" id="staff_name" value="{{ old('staff_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_description') is-invalid @enderror" required>
                                <x-input-error :messages="$errors->get('staff_name')" class="mt-2" />
                            </div>
                            <div class="mb-4">
                                <label for="access_type" class="block text-gray-700 text-md font-bold mb-2">Access Type</label>
                                <select id="access_type" name="access_type" value="{{ old('access_type') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('department_id') is-invalid @enderror" required>
                                    <option value="" selected>Select Department</option>
                                    <option value="administrative">Administrative</option>
                                    <option value="departmental">Departmental</option>
                                </select>
                                <x-input-error :messages="$errors->get('access_type')" class="mt-2" />
                            </div>
                            <div class="flex mb-4 mt-5 justify-center">
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
        <form id="deleteAll" action="{{ route('admin.staff.deleteAll') }}" method="POST" onsubmit="return confirmDeleteAll(event);">
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


    @if($search && $staffs->isEmpty())
        <p class="text-black mt-8 text-center">No school found for matching "{{ $search }}"</p>
    @elseif(!$search && $staffs->isEmpty())
        <p class="text-black mt-8 text-center">No data available in table</p>
    @else
        <table class="table-auto border-collapse border border-gray-400 w-full text-center mb-4">
            <thead class="bg-gray-200 text-black">
                <tr>
                    <th class="border border-gray-400 px-3 py-2">
                        <button wire:click="sortBy('staff_id')" class="w-full h-full flex items-center justify-center">
                            School ID
                            @if ($sortField == 'staff_id')
                                @if ($sortDirection == 'asc')
                                    &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                @else
                                    &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                @endif
                            @endif
                        </button>
                    </th>
                    <th class="border border-gray-400 px-3 py-2">
                        <button wire:click="sortBy('staff_name')" class="w-full h-full flex items-center justify-center">
                            Full Name
                            @if ($sortField == 'staff_name')
                                @if ($sortDirection == 'asc')
                                    &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                @else
                                    &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                @endif
                            @endif
                        </button>
                    </th>
                    <th class="border border-gray-400 px-3 py-2">
                        <button wire:click="sortBy('access_type')" class="w-full h-full flex items-center justify-center">
                            Access Type
                            @if ($sortField == 'access_type')
                                @if ($sortDirection == 'asc')
                                    &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                @else
                                    &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                @endif
                            @endif
                        </button>
                    </th>
                    <th class="border border-gray-400 px-3 py-2">School</th>
                    <th class="border border-gray-400 px-3 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staffs as $staff)
                    <tr>
                        <td class="text-black border border-gray-400 px-3 py-2">{{ $staff->staff_id }}</td>
                        <td class="text-black border border-gray-400 px-3 py-2">{{ $staff->staff_name}}</td>
                        <td class="text-black border border-gray-400 px-3 py-2 uppercase">{{ $staff->access_type}}</td>
                        <td class="text-black border border-gray-400 px-3 py-2">{{ $staff->school->abbreviation }} - {{ $staff->school->school_name }}</td>
                        <td class="text-black border border-gray-400 px-3 py-2">
                            <div class="flex justify-center items-center space-x-2">
                                <div x-data="{ open: false, 
                                        id: '{{ $staff->id }}', 
                                        staff_id: '{{ $staff->staff_id }}',
                                        staff_name: '{{ $staff->staff_name }}',
                                        access_type: '{{ $staff->access_type }}',
                                         school: '{{ $staff->school_id }}'}">
                                    <a @click="open = true" class="cursor-pointer bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                                        <i class="fa-solid fa-pen fa-xs" style="color: #ffffff;"></i>
                                    </a>
                                    <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                        <div @click.away="open = true" class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
                                            <div class="flex justify-between items-start pb-3"> <!-- Changed items-center to items-start -->
                                                <p class="text-xl font-bold">Edit Staff</p>
                                                <a @click="open = false" class="cursor-pointer text-black text-sm px-3 py-2 rounded hover:text-red-500">X</a>
                                            </div>
                                            <div class="mb-4">
                                                <form id="updateStaffForm" action="{{ route('admin.staff.update', $staff->id )}}" method="POST" class="">
                                                    <x-caps-lock-detector />
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="mb-4">
                                                        <label for="school_id" class="block text-gray-700 text-md font-bold mb-2 text-left">Staff belongs to:</label>
                                                        <select id="school_id" name="school_id" x-model="school" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('school_id') is-invalid @enderror" required>
                                                            @foreach($schools as $school)
                                                                <option value="{{ $school->id }}" {{ $staff->school_id == $school->id ? 'selected' : '' }}>
                                                                    {{ $school->abbreviation }} - {{ $school->school_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                                                    </div>

                                                    <div class="mb-4">
                                                        <label for="staff_id" class="block text-gray-700 text-md font-bold mb-2 text-left">School ID</label>
                                                        <input type="text" name="staff_id" id="staff_id" x-model="staff_id" class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('staff_id') is-invalid @enderror" required autofocus>
                                                        <x-input-error :messages="$errors->get('staff_id')" class="mt-2" />
                                                    </div>
                                                    <div class="mb-4">
                                                        <label for="staff_name" class="block text-gray-700 text-md font-bold mb-2 text-left">Full Name</label>
                                                        <input type="text" name="staff_name" id="staff_name" x-model="staff_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('staff_name') is-invalid @enderror" required>
                                                        <x-input-error :messages="$errors->get('staff_name')" class="mt-2" />
                                                    </div>
                                                    <div class="mt-4">
                                                        <label for="access_type" class="block text-gray-700 text-md font-bold mb-2 text-left">Access Type</label>
                                                        <select id="access_type" name="access_type" value="{{ old('access_type') }}" class="uppercase shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('access_type') is-invalid @enderror" required>
                                                            @if($staff->access_type === 'departmental')
                                                                <option value="{{ $staff->access_type }}" selected>{{ $staff->access_type }}</option>
                                                                <option value="administrative">Administrative</option>
                                                            @else
                                                                <option value="{{ $staff->access_type }}" selected>{{ $staff->access_type }}</option>
                                                                <option value="departmental">Departmental</option>
                                                            @endif
                                                        </select>
                                                        <x-input-error :messages="$errors->get('access_type')" class="mt-2" />
                                                    </div>
                                                    <div class="flex mb-4 mt-5 justify-center">
                                                        <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
                                                            Save
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form id="deleteSelected" action="{{ route('admin.staff.destroy', $staff->id, $staff->staff_id ) }}" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $staff->id }}', '{{ $staff->staff_id }}', '{{ $staff->staff_name }}');">
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
        {{ $staffs->links() }}
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

    function ConfirmDeleteSelected(event, rowId, staffId, staffName) {
    event.preventDefault(); // Prevent form submission initially

    Swal.fire({
        title: `Are you sure you want to delete the staff ${staffName}?`,
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteSelected');
            // Replace the placeholders with the actual rowId and staffId
            const actionUrl = `{{ route('admin.staff.destroy', [':rowId', ':staffId']) }}`
                                .replace(':rowId', rowId)
                                .replace(':staffId', staffId);
            form.action = actionUrl;
            form.submit();
        }
    });

    return false; 
}


</script>