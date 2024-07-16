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
        color: #000;
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
        overflow: hidden; Prevents container scroll
        background-color:red;
    }

    /* Table and content styles */
    .flex-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
        margin: 2px;
        overflow: hidden; /* Prevents container scroll */
        
    }

    .table-container {
        border-radius: 3px;
        background-color: rgba(255, 255, 255, 0.8);
        overflow: hidden; /* Prevents container scroll */
        margin-bottom: 2rem; /* Adds space between tables */
    }

    table {
        border-collapse: collapse;
        width: 100%; /* Adjust width as needed */
        background-color: rgba(255, 255, 255, 0.8);
        padding: 1rem;
        margin: 0.5rem;
    }

    tbody {
        display: block;
        max-height: 360px; /* Adjust maximum height */
        overflow-y: auto;
    }

        footer {
            padding: 2rem;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            position: sticky;
            bottom: 0;
        }

        #my-time {
            font-size: 90px;
            font-weight: bold;
            text-align: center;
            margin-top: 50px; /* Adjust margin to fit design */
            color: white;
            position: absolute;
            bottom: 20px; /* Position below the logo */
            left: 50%;
            transform: translateX(-50%);
            padding: 20px; /* Padding to make time more visible */
            z-index: 1000;
        }


        .table-container {
            border-radius: 3px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        table {
            border-collapse: collapse;
            width: 400px; /* Adjust width as needed */
            background-color: rgba(255, 255, 255, 0.8); 
            padding: 1rem;
            margin: 0.5rem; /* Adjusted margin */
            
        }
        
        td, th {
            padding: 5px;
            width: 250px; /* Adjust width as needed */
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
        
        tbody {
            display: block;
            width: 100%;
            height:100vh;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */

        }

        h2 {
            font-weight: bold;
            font-size: 2rem;
            text-transform: uppercase;
            margin-bottom: 1rem;
            color: #fff; /* Ensure contrast against background */
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
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td class="font-bold text-sm uppercase truncate tracking-wider" style="max-width: 118px;">
                            <text>RECITAS, EMMANUEL KENNETH dcdscsdcds</text>
                        </td>
                        <td class="font-bold text-md uppercase text-center tracking-wider">07-15 :: 08:33 AM</td>
                    </tr>
                    <!-- Repeat the above <tr> structure for each row as needed -->
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
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr><tr>
                        <td>Content</td>
                        <td>Content</td>
                    </tr>
                    <tr>
                        <td class="font-bold text-sm uppercase truncate tracking-wider" style="max-width: 118px;">
                            <text>RECITAS, EMMANUEL KENNETH dcdscsdcds</text>
                        </td>
                        <td class="font-bold text-md uppercase text-center tracking-wider">07-15 :: 08:33 AM</td>
                    </tr>
                    <!-- Repeat the above <tr> structure for each row as needed -->
                </tbody>
            </table>
        </div>
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var timeDisplayElement = document.querySelector('#my-time');
            function printTime() {
                var now = new Date();
                var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                var date = now.toLocaleDateString(undefined, options);
                var time = now.toLocaleTimeString();
                time = time.replace('AM', 'A.M.').replace('PM', 'P.M.');
                timeDisplayElement.innerHTML = time;
                // date + ' ' 
            }
            setInterval(printTime, 1000);
        });
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

            // Example of adding a row and scrolling to bottom
            // function addRowToTable() {
            //     var table = document.getElementById('timeInTable').getElementsByTagName('tbody')[0];
            //     var newRow = table.insertRow();
            //     var cell1 = newRow.insertCell(0);
            //     var cell2 = newRow.insertCell(1);
            //     cell1.textContent = 'New Employee';
            //     cell2.textContent = '07-16 :: 09:00 AM';

            //     // After adding a row, scroll to the bottom of the timeInTable
            //     scrollToBottom('timeInTable');
            // }

            // // Example: Call addRowToTable after 3 seconds (simulate new data arrival)
            // setTimeout(addRowToTable, 3000);
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
</body>
</html>
