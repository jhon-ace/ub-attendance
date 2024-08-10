<x-app-layout>
    @if (Auth::user()->hasRole('admin'))
        <x-user-route-page-name :routeName="'admin.attendance.holiday'" />
    @else
        <x-user-route-page-name :routeName="'staff.attendance.holiday'" />
    @endif
    <x-content-design>
        @if (session('success'))
            <x-sweetalert type="success" :message="session('success')" />
        @endif

        @if (session('info'))
            <x-sweetalert type="info" :message="session('info')" />
        @endif

        @if (session('error'))
            <x-sweetalert type="error" :message="session('error')" />
        @endif
        <!-- Content Area -->
        <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
            window.addEventListener('resize', () => {
                isFullScreen = (window.innerHeight === screen.height);
            });
            " class="rounded-md p-2 sm:p-2 md:p-2 lg:p-2 text-black font-medium">
            <div class="relative">
                <div class="container shadow-lg p-5 sm:p-6 md:p-7 lg:p-8 bg-white rounded-md text-black font-medium"
                    :style="{ 'width': isFullScreen ? 'calc(100vw - 16px)' : 'auto', 'margin-left': isFullScreen ? '-192px' : '0' }">
                    <h1 class="font-bold uppercase">Admin / Manage Holiday</h1>
                    <div class="flex justify-center mt-8 w-full">
                        <div class="w-[50%] flex justify-center mb-4 mx-auto">
                            <form action="{{ route('admin.attendance.setHoliday') }}" method="POST" class="w-[78%] mx-auto">
                                <x-caps-lock-detector />
                                @csrf
                                <br>
                                <p class="text-[14px]">
                                    <text class="text-red-500">Note:</text> This will apply to all employees attendance.
                                </p>
                                <br>
                                <div class="mb-2">
                                    <label for="selected-date" class="block mb-2 text-left">Select a Date:</label>
                                    <input type="date" id="selected-date" name="selected_date" class="block mx-auto mb-4 p-2 border border-gray-300 rounded w-full " required>
                                </div>
                                
                                <div class="flex mb-4 mt-10 justify-center">
                                    <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
                                        Submit Date as Holiday
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </x-content-design>
</x-app-layout>

<x-show-hide-sidebar
    toggleButtonId="toggleButton"
    sidebarContainerId="sidebarContainer"
    dashboardContentId="dashboardContent"
    toggleIconId="toggleIcon"
    toggleIconIdFullscreen="toggleIcon2"
/>