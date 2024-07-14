<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Time Out Portal</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Example of custom focus styles */
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

        body {
            margin: 0;
            font-family: sans-serif;
            background: linear-gradient(to right, #FBBF24, #EF4444);
            color: #000; /* Adjust text color as needed */
            overflow: hidden; /* Prevent scrolling */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100vh;
        }

        .content {
            flex: 1; /* Fill remaining vertical space */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .footer {
            background: linear-gradient(to right, #FBBF24, #EF4444);
            color: white;
            text-align: center;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <div class="content">
        <!-- Logo Section -->
        <div class="flex flex-col items-center justify-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="w-[600px]" style="margin-top:20px">
            <div class="mt-10 text-white text-lg font-semibold" id="my-time"></div> <!-- Date and Time Display -->
        </div>

        <!-- Form Section -->
        
    </div>
    <form id="attendanceForm" action="{{ route('admin.attendance.time-out.store') }}" method="POST" class="mt-10">
            @csrf
            <div class="mb-4">
                <input type="password" id="inputField" name="user_rfid"
                    class="bg-gradient-to-r from-yellow-400 to-red-500 mt-1 p-2 w-full text-[#F9C915]"
                    autocomplete="off" autofocus>
            </div>
        </form>

    <!-- Footer Section -->
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
    <script>
         // Update time function
        var timeDisplayElement = document.querySelector('#my-time');
        function printTime() {
            var now = new Date();
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            var date = now.toLocaleDateString(undefined, options);
            var time = now.toLocaleTimeString();
            timeDisplayElement.innerHTML = date + ' ' + time;
        }
        setInterval(printTime, 1000);
    </script>
</body>
</html>
