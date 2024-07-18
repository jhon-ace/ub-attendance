<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee Profile Out</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Styles for input fields */
        input[type=password] {
            display: block;
            outline: none;
            border: none;
            height: 2em;
            font-size: 16px;
            margin-bottom: 1px;
            outline: none;
            box-shadow: none;
            background: linear-gradient(to right, #FBBF24, #EF4444);
        }

        input[type=password]:focus {
            outline: none;
            box-shadow: none;
            background: linear-gradient(to right, #FBBF24, #EF4444);
            
        }

        /* General body styles */
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: sans-serif;
            background: linear-gradient(to right, #FBBF24, #EF4444);
            color: #000; /* Adjust text color as needed */
            overflow:hidden;
        }

        /* Container styles */
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
            flex: 1; /* Fill remaining vertical space */
        }

        /* Footer styles */
        footer {
            padding: 1rem;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
            position: sticky;
            bottom: 0;
        }
        .hehe{
            font-size:70px;
            
        }
    </style>
    <script>
        setTimeout(function() {
            window.location.href = "{{ route('admin.attendance.time-in.portal') }}";
        }, 1000); // 5000 milliseconds = 5 seconds
    </script>
</head>
<body>
    <div class="hehe uppercase font-bold text-3xl text-center mt-16 text-white tracking-widest shadow-lg pb-8">Time - Out</div>
    <div class="container mt-5">
        @forelse ($employees as $employee)
        <div class="flex w-full">
            <div style="width: 600px;" class="pl-16 ml-5">
                @if ($employee->employee_photo && Storage::exists('public/employee_photo/' . $employee->employee_photo))
                <div class="flex justify-center mb-4 mt-5">
                    <img src="{{ asset('storage/employee_photo/' . $employee->employee_photo) }}" class="rounded-lg object-contain" alt="Employee Photo">
                </div>
                @else
                <div class="flex justify-center mb-4">
                    <img data-fancybox src="{{ asset('assets/img/user.png') }}" class="cursor-pointer w-48 h-48 object-cover hover:border hover:border-red-500 rounded-sm" title="Click to view Picture" alt="Default User Photo">
                </div>
                @endif
            </div>
            <div class="flex flex-1 flex-col w-full -pl-8 mt-5">
                <div class="font-bold uppercase flex justify-center">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="w-[230px]">
                </div>
                <div class="p-2 mb-2 mt-5 font-bold uppercase">
                    <span class="text-sm">Employee ID</span><br>
                    <span style="font-size: 35px;" class="text-white shadow-sm">{{ $employee->employee_id }}</span>
                </div>
                <div class="p-2 mb-2 font-bold uppercase">
                    <span class="text-sm">Employee Name</span><br>
                    <span style="font-size: 40px;" class="text-white tracking-wide shadow-sm">{{ $employee->employee_lastname }}, {{ $employee->employee_firstname }}, {{ ucfirst($employee->employee_middlename[0]) }}</span>
                </div>
                <div class="p-2 font-bold uppercase">
                    <span class="text-sm">Department/Office</span><br>
                    <span style="font-size: 35px;" class="text-white shadow-sm tracking-wide">{{ $employee->department->department_abbreviation }}</span>
                </div>
            </div>
        </div>
        
        @empty
        <p>No employee found.</p>
        @endforelse
    </div>
    <div class="w-full z-10">
            <form id="attendanceForm" action="{{ route('admin.attendance.time-in.store') }}" method="POST">
                @csrf
                <div class="z-10">
                    <input type="password" id="inputField" name="user_rfid"
                        class="bg-gradient-to-r from-yellow-400 to-red-500 mt-1 p-2 text-[#F9C915] w-full"
                        autocomplete="off" autofocus>
                </div>
            </form>
        </div>
    <footer class="bg-gradient-to-r from-yellow-400 to-red-500 text-white text-center py-3 tracking-wide">
        <div class="max-w-screen-lg mx-auto">
            A premier university transforming lives for a great future. Anchored on: SCHOLARSHIP, CHARACTER, SERVICE
        </div>
    </footer>

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
