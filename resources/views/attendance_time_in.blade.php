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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js"></script>
        <script src="https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.js"></script>

        <!-- Scripts -->
         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-slate-300">
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

            @if (session('info'))
                <div class="alert alert-info" role="alert">
                    {{ session('info') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="min-h-screen flex flex-col justify-between items-center bg-[#FA940C]">
                <!-- Logo Section -->
                <div class="flex-grow flex flex-col justify-center items-center">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class=" w-[600px]"> <!-- Replace with your logo URL -->
                    <div class="mt-10 text-white text-lg font-semibold" id="my-time"></div> <!-- Date and Time Display -->
                </div>
                <div class="w-full -z-10">
                    <form id="attendanceForm" action="{{ route('attendance.store') }}" method="POST">
                        @csrf
                        <div class="mb-4 z-10">
                            <label for="inputField" class="block text-sm font-medium text-gray-700">Time In RFID Scanner</label>
                            <input type="password" id="inputField" name="employee_rfid" placeholder="Scan RFID No..." class="mt-1 p-2 border border-gray-300 rounded-md w-full" autofocus>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <script>
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

