<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <!-- <meta http-equiv="refresh" content="30"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Time In Portal</title>
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
            box-shadow: none;
            /* background: linear-gradient(to right, #FBBF24, #EF4444); */
            background: linear-gradient(to right, #1e3a8a, #1e3a8a);
            z-index: 1000;
        }

        input[type=password]:focus {
            outline: none;
            box-shadow: none;
            /* background: linear-gradient(to right, #FBBF24, #EF4444); */
            background: linear-gradient(to right, #1e3a8a, #1e3a8a);
            text-align:center;
        }

        /* General body styles */
        body {
        margin: 0;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        font-family: sans-serif;
        /* background: linear-gradient(to right, #FBBF24, #EF4444); */
        /* background: linear-gradient(to right, #1e3a8a, #2563eb); */
        background: linear-gradient(to right, #1e3a8a, #1e3a8a);
        color: #000;
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
        flex: 1;
        overflow: hidden; /* Prevents container scroll */
    }

    /* Table and content styles */
    .flex-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
        margin: 2px;
        overflow: hidden; /* Prevents container scroll */
        margin-top:-50px;
    }

    .table-container {
        border-radius: 4px;
        background-color: rgba(255, 255, 255, 0.8);
        overflow: hidden; /* Prevents container scroll */
        margin-bottom: 2rem; /* Adds space between tables */
        height:70vh;
    }

    table {
        border-collapse: collapse;
        width: 400px; /* Adjust width as needed */
        background-color: rgba(255, 255, 255, 0.8); 
        padding: 1rem;
        margin: 0.5rem; /* Adjusted margin */
        table-layout:fixed;
    }


    tbody {
        display: block;
        width: 100%;
        overflow-y: auto;
        overflow-x: hidden;
        height:500px;
        scrollbar-width: none; 
        -ms-overflow-style: none; 
    }

    td, th {
        padding: 5px;
        width: 350px; /* Adjust width as needed */
        border-right: 1px solid #ccc;
        border: 1px solid;
        text-align:left;
        color:black;
    }

    thead tr {
        background: #FBBF24;
        color: #eee;
        display: block;
        position: relative;
        width: 100%;
        border:1px solid black;
    }

    footer {
        padding: 2rem;
        text-align: center;
        /* background-color: rgba(255, 255, 255, 0.8); */
        position: sticky;
        bottom: 0;
        background: linear-gradient(to right, #1e3a8a, #1e3a8a);
    }

    h2 {
        font-weight: bold;
        font-size: 2rem;
        text-transform: uppercase;
        margin-bottom: 1rem;
        color: #fff; /* Ensure contrast against background */
    }

    #my-time {
        font-size: 120px;
        font-weight: bold;
        text-align: center;
        margin-top: 20px; /* Adjust margin to fit design */
        color: white;
        position: absolute;
        bottom: 20px; /* Position below the logo */
        left: 50%;
        transform: translateX(-50%);
        padding: 20px; /* Padding to make time more visible */
        z-index: 1000;
    }

    </style>
</head>
<body>
<div class="container ">
    <div class="flex-container">
        <div class="table-container">
            <h2 class="font-bold text-2xl text-black uppercase mb-2 mt-4 tracking-widest text-center">Time - In List</h2>
            <table>
                <thead>
                    <tr>
                        <th class="tracking-wider uppercase">Employee Name</th>
                        <th class="tracking-wider uppercase text-center">MM - DD :: TIME</th>
                    </tr>
                </thead>
                <tbody  id="timeInTable" >
                    @foreach($curdateDataIn as $data)
                        <tr>
                            <td class="font-bold text-sm uppercase truncate tracking-wider" style="max-width:214px;">
                                <text>{{ $data->employee->employee_lastname}}, {{ $data->employee->employee_firstname}} {{ $data->employee->employee_middlename}}</text>
                            </td>
                            <td class="font-bold text-md uppercase text-center tracking-wider">{{ date('m-d :: g:i:s A', strtotime($data->check_in_time)) }}</td>
                        </tr>
                    @endforeach
                    <!-- Repeat the above <tr> structure for each row as needed    date('g:i:s A', strtotime($attendanceIn->check_in_time)) -->
                </tbody>
            </table>
        </div>
        <div class="flex-col">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="w-[550px]">
        </div>
        <div class="table-container">
            <h2 class="font-bold text-2xl text-black uppercase mb-2 mt-4 tracking-widest text-center">Time - OUT List</h2>
            <table>
                <thead>
                    <tr>
                        <th class="tracking-wider uppercase">Employee Name</th>
                        <th class="tracking-wider uppercase text-center">MM - DD :: TIME</th>
                    </tr>
                </thead>
                <tbody  id="timeOutTable" >
                    @foreach($curdateDataOut as $dataOut)
                        <tr>
                            <td class="font-bold text-sm uppercase truncate tracking-wider" style="max-width:214px;">
                                <text>{{ $dataOut->employee->employee_lastname}}, {{ $dataOut->employee->employee_firstname}} {{ $dataOut->employee->employee_middlename}}</text>
                            </td>
                            <td class="font-bold text-md uppercase text-center tracking-wider">{{ date('m-d :: g:i:s A', strtotime($dataOut->check_out_time)) }}</td>
                        </tr>
                    @endforeach
                    <!-- Repeat the above <tr> structure for each row as needed -->
                </tbody>
            </table>
        </div>
    </div>
    @if (session('error'))
        <div id="session-error" class="alert alert-danger -mt-[90px] bg-white rounded-md">
            <ul>
                <li class="text-yellow-800  p-2 font-bold text-[20px] shadow-md tracking-widest">&nbsp;{{ session('error') }}&nbsp;</li>
            </ul>
        </div>
    @endif
    
    @if (session('success'))
        <div id="session-success" class="alert alert-success -mt-[90px] bg-white rounded-md">
            <ul>
                <li class="text-yellow-800 p-2 font-bold text-[20px] shadow-md tracking-widest">{{ session('success') }}</li>
            </ul>
        </div>
    @endif


    <div id="my-time" class="text-center tracking-wide w-full flex justify-center"></div> <!-- Date and Time Display -->
</div>

    <div class="w-full z-10">
        <form id="attendanceForm" action="{{ route('admin.attendance.store') }}" method="POST">
            @csrf
            <div class="z-10">
                <input type="password" id="inputField" name="user_rfid"
                    class=" mt-1 p-2 text-[#F9C915] w-full"
                    autocomplete="off" autofocus>
            </div>
        </form>
        <!-- <form id="attendanceForm" action="" method="POST">
            @csrf
            <div class="z-10">
                <input type="password" id="inputField" name="user_rfid"
                    class="bg-gradient-to-r from-yellow-400 to-red-500 mt-1 p-2 text-[#F9C915] w-full"
                    autocomplete="off" autofocus>
            </div>
        </form> -->
    </div>
    <footer class="w-full uppercase  border-t border-white text-white text-center py-3 tracking-widest">
        <div class="w-full mx-auto">
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
    //    document.addEventListener('DOMContentLoaded', function () {
            var timeDisplayElement = document.querySelector('#my-time');
            
            function printTime() {
                var now = new Date();
                var options = { 
                    year: '2-digit', 
                    month: '2-digit', 
                    day: '2-digit'
                };
                var date = now.toLocaleDateString(undefined, options);

                // Manually format the date to match the desired format "Fri, 2024-06-14"
                var dateParts = date.split('/');
                var formattedDate = `${dateParts[0]}`;//-${dateParts[2]}-${dateParts[1]}
                
                var time = now.toLocaleTimeString();
                time = time.replace('AM', 'A.M.').replace('PM', 'P.M.');
                
                timeDisplayElement.innerHTML = `${now.toLocaleDateString(undefined, { weekday: 'short' })}, ${time}`; // ${formattedDate} 
            }
            
            setInterval(printTime, 1000);
        // });

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to scroll to the bottom of a container
            function scrollToBottom(containerId) {
                var container = document.getElementById(containerId);
                container.scrollTop = container.scrollHeight;
            }

            // Example usage: scroll to bottom of timeInTable on page load
            scrollToBottom('timeInTable');
             scrollToBottom('timeOutTable');

        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var prevScrollpos = window.pageYOffset;
            // var header = document.querySelector('.table-container th');

            window.addEventListener('scroll', function () {
                var currentScrollPos = window.pageYOffset;
                if (prevScrollpos > currentScrollPos) {
                    // Scrolling up
                    // header.classList.add('show');
                } else {
                    // Scrolling down
                    // header.classList.remove('show');
                }
                prevScrollpos = currentScrollPos;
            });
        });
    </script>
     <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                var sessionError = document.getElementById('session-error');
                if (sessionError) {
                    sessionError.style.display = 'none';
                }

                var validationErrors = document.getElementById('validation-errors');
                if (validationErrors) {
                    validationErrors.style.display = 'none';
                }
            }, 5000); // 5000 milliseconds = 5 seconds
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                var sessionSuccess = document.getElementById('session-success');
                if (sessionSuccess) {
                    sessionSuccess.style.display = 'none';
                }

                var validationErrors = document.getElementById('validation-errors');
                if (validationErrors) {
                    validationErrors.style.display = 'none';
                }
            }, 5000); // 5000 milliseconds = 5 seconds
        });
    </script>

</body>
</html>
