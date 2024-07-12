<x-app-layout>
    <x-user-route-page-name :routeName="'admin.attendance.employee_attendance.portal'" />
    @if (session('success'))
        <x-sweetalert type="success" :message="session('success')" />
        <script>
            setTimeout(function() {
                document.querySelector('.swal2-container').remove();
                document.getElementById('inputField').focus(); // Focus back on inputField
            }, 1000);
        </script>
    @endif
    @if (session('error'))
        <x-sweetalert type="error" :message="session('error')" />
    @endif


    <div class="min-h-screen flex flex-col justify-between items-center bg-[#FA940C]">
        <!-- Logo Section -->
        <div class="flex-grow flex flex-col justify-center items-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class=" w-[600px]"> <!-- Replace with your logo URL -->
            <div class="mt-4 text-white text-lg font-semibold" id="my-time"></div> <!-- Date and Time Display -->
        </div>
        <div class="w-full -z-10">
            <form id="attendanceForm" action="{{ route('admin.employee_attendance.store') }}" method="POST">
                @csrf
                <div class="mb-4 z-10">
                    <label for="inputField" class="block text-sm font-medium text-gray-700">Time In RFID Scanner</label>
                    <input type="password" id="inputField" name="employee_rfid" placeholder="Scan RFID No..." class="mt-1 p-2 border border-gray-300 rounded-md w-full" autofocus>
                </div>
            </form>
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
</x-app-layout>
