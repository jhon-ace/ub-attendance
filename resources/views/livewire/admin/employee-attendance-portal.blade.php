<div class="min-h-screen flex flex-col justify-between items-center">
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
  var d = new Date();
  var hours = d.getHours();
  var mins = d.getMinutes();
  var secs = d.getSeconds();
  timeDisplayElement.innerHTML = hours+":"+mins+":"+secs;
}

setInterval(printTime, 1000);
    </script>