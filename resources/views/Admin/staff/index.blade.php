<x-app-layout>
    <x-content-design>
        <!-- Content Area -->
        <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
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
        </div>
    </x-content-design>
</x-app-layout>

<x-show-hide-sidebar
    toggleButtonId="toggleButton"
    sidebarContainerId="sidebarContainer"
    dashboardContentId="dashboardContent"
    toggleIconId="toggleIcon"
/>