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

    <!-- Content Area -->
    <div class="container shadow-lg p-5 bg-white text-black font-medium h-full">
        <form id="attendanceForm" action="{{ route('admin.employee_attendance.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="inputField" class="block text-sm font-medium text-gray-700">Input Field</label>
                <input type="text" id="inputField" name="employee_rfid" placeholder="Enter something..." class="mt-1 p-2 border border-gray-300 rounded-md w-full" autofocus>
            </div>
        </form>
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
</x-app-layout>
