<x-app-layout>
    @if (session('success'))
        <x-sweetalert type="success" :message="session('success')" />
    @endif
    @if (session('info'))
        <x-sweetalert type="info" :message="session('info')" />
    @endif

    @if (session('error'))
        <x-sweetalert type="error" :message="session('error')" />
    @endif

    @if (Auth::user()->hasRole('admin'))
        <x-user-route-page-name :routeName="'admin.dashboard'" />
    @elseif(Auth::user()->hasRole('employee'))
        <x-user-route-page-name :routeName="'employee.dashboard'" />
    @else

    @endif
    <x-content-design>
        <!-- Content Area -->
          <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
            window.addEventListener('resize', () => {
                isFullScreen = (window.innerHeight === screen.height);
            });
            " class="rounded-md p-2 sm:p-2 md:p-2 lg:p-2 text-black font-medium">
            <div class="relative">
                <div class="container shadow-lg p-5 sm:p-6 md:p-7 lg:p-8 bg-white rounded-md text-black font-medium"
                    :style="{ 'width': isFullScreen ? 'calc(100vw - 16px)' : 'auto', 'margin-left': isFullScreen ? '-192px' : '0' }">
                    <div class="flex flex-row justify-center">
                        <div class="flex-1 bg-green-500">
                            <div class="text-center">
                                <div class="">TIME-IN LIST</div>
                            </div>
                        </div>
                        <div class="flex-1 bg-red-500">
                            <div class="text-center">
                                ddsd
                            </div>
                        </div>
                        <div class="flex-1 bg-green-500">
                            <div class="text-center font-bold">TIME-OUT LIST</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
            window.addEventListener('resize', () => {
                isFullScreen = (window.innerHeight === screen.height);
            });
            " class="rounded-md p-2 sm:p-2 md:p-2 lg:p-2 text-black font-medium">
            <div  class="container mx-auto p-1 max-h-full bg-white">
                <div class="flex flex-row justify-center">
                    <div class="flex-1 bg-green-500">
                        <div class="text-center">
                            <div class="">TIME-IN LIST</div>
                        </div>
                    </div>
                    <div class="flex-1 bg-red-500">
                        <div class="text-center">
                            ddsd
                        </div>
                    </div>
                    <div class="flex-1 bg-green-500">
                        <div class="text-center font-bold">TIME-OUT LIST</div>
                    </div>
                </div>
            </div>
        </div> -->
    </x-content-design>
</x-app-layout>

<x-show-hide-sidebar
    toggleButtonId="toggleButton"
    sidebarContainerId="sidebarContainer"
    dashboardContentId="dashboardContent"
    toggleIconId="toggleIcon"
/>

