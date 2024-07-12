<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/fontawesome.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/solid.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/regular.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        setTimeout(function() {
            window.location.href = "{{ route('attendance.portal') }}";
        }, 5000); // 5000 milliseconds = 5 seconds
    </script>
</head>
<body class="font-sans antialiased">

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
                <script>
                    setTimeout(function() {
                        document.querySelector('.alert').remove();
                        document.getElementById('inputField').focus(); // Focus back on inputField
                    }, 1000);
                </script>
            @endif

            <!-- @if (session('info'))
                <div class="alert alert-info" role="alert">
                    {{ session('info') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif -->


                <div class="min-h-screen flex flex-col w-full h-full bg-[#FA940C] p-4 shadow-md">
                    @forelse ($employees as $employee)
                        <div class="flex w-full">
                            <div style="width:600px;" class="pl-8 pt-8">
                                @if ($employee->employee_photo && Storage::exists('public/employee_photo/' . $employee->employee_photo))
                                    <div class="flex justify-center mb-4">
                                        <img src="{{ asset('storage/employee_photo/' . $employee->employee_photo) }}" class="rounded-lg object-contain" alt="Employee Photo">
                                    </div>
                                @else
                                    <div class="flex justify-center mb-4">
                                        <img data-fancybox src="{{ asset('assets/img/user.png') }}" class="cursor-pointer w-48 h-48 object-cover hover:border hover:border-red-500 rounded-sm" title="Click to view Picture" alt="Default User Photo">
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col w-full pl-8 pt-8 mt-11">
                                <div class="p-2 mb-2 font-bold uppercase">
                                    <span style="font-size: 38px;">{{ $employee->employee_lastname }}, {{ $employee->employee_firstname }}, {{ $employee->employee_middlename }}</span>
                                </div>
                                <div class="p-2 mb-2 font-bold uppercase">
                                    <span style="font-size: 20px;">{{ $employee->department->department_name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="w-full -z-10">
                            <form id="attendanceForm" action="{{ route('attendance.store') }}" method="POST">
                                @csrf
                                <div class="z-10">
                                    <label for="inputField" class="block text-sm font-medium text-gray-700">Time In RFID Scanner</label>
                                    <input type="password" id="inputField" name="employee_rfid" placeholder="Scan RFID No..." class="mt-1 p-2 border border-gray-300 rounded-md w-full" autofocus>
                                </div>
                            </form>
                        </div>
                    @empty
                        <p>No employee found.</p>
                    @endforelse
                </div>

     @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputField = document.getElementById('inputField');
            const form = document.getElementById('attendanceForm');

            inputField.addEventListener('input', function () {
                form.submit();
            });
        });
    </script>
    @endpush

</body>
</html>


