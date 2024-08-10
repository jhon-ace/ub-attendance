@if (Auth::user()->hasRole('admin'))
    <div x-cloak x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                        window.addEventListener('resize', () => {
                            isFullScreen = (window.innerHeight === screen.height);
                        });
                    " x-show="!isFullScreen" id="sidebarContainer"  class="fixed flex flex-col left-0 w-14 hover:w-48 md:w-48 bg-gradient-to-r from-red-500 to-orange-500 h-full text-black transition-all duration-300 border-r-2 border-gray-300 dark:border-gray-600 sidebar">
        <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow mr-0.5 ">
            <ul class="flex flex-col py-2 space-y-1 text-gray-800" >
                <a href="#" class="flex justify-center items-center">
                    <img class="w-32 h-auto object-contain" src="{{ asset('assets/img/logo.png') }}" alt="SCMS Logo">
                </a>

                <label class="relative flex flex-row justify-center items-center h-2 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent  pr-3 ">
                    <span class=" text-sm tracking-wide truncate text-gray-200">{{ Auth::user()->name }}</span>
                </label>
                <label class="relative flex flex-row justify-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent   ">
                    <span class=" text-xs tracking-wide truncate text-gray-200">{{ Auth::user()->email }}</span>
                </label>
                <div class="border-t"></div>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('admin.dashboard') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-gauge-high fa-sm text-gray-200 "></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Dashboard</span>
                    </a>
                </li>
                <li >
                    <a href="{{ route('admin.school.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('admin.school.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">School Year</span>
                    </a>
                </li>
                <li x-data="{ open: {{ request()->routeIs('admin.department.index')  || request()->routeIs('admin.workinghour.index') ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                            <i class="fa-solid fa-users fa-sm text-gray-200"></i>
                        </span>
                        <span class="text-sm tracking-wide truncate text-gray-200 ml-2">Department</span>
                        <span class="ml-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </a>
                    <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('admin.department.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                            {{ request()->routeIs('admin.department.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <span class="inline-flex justify-center items-center ml-4">
                                    <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                                </span>
                                <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Department</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.workinghour.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                            {{ request()->routeIs('admin.workinghour.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <span class="inline-flex justify-center items-center ml-4">
                                    <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                                </span>
                                <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Working Hour</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('admin.course.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('admin.course.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Courses</span>
                    </a>
                </li>
                <li x-data="{ open: {{ request()->routeIs('admin.staff.index')  || request()->routeIs('admin.employee.index') || request()->routeIs('admin.student.index') ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                            <i class="fa-solid fa-users fa-sm text-gray-200"></i>
                        </span>
                        <span class="text-sm tracking-wide truncate text-gray-200 ml-2">Users</span>
                        <span class="ml-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </a>
                    <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1">
                        <!-- <li>
                            <a href="{{ route('admin.staff.index') }}" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white {{ request()->routeIs('admin.staff.index') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                            <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Admin Staff
                            </a>
                        </li> -->
                        <li>
                            <a href="{{ route('admin.employee.index') }}" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white {{ request()->routeIs('admin.employee.index') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Employee
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.student.index') }}" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white {{ request()->routeIs('admin.student.index') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Student
                            </a>
                        </li>
                    </ul>
                </li>
                <li x-data="{ open: {{ request()->routeIs('admin.attendance.employee_attendance') || request()->routeIs('admin.attendance.student_attendance') || request()->routeIs('admin.attendance.employee_attendance.search') || request()->routeIs('admin.attendance.employee_attendance.payroll') ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="w-full cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                            <i class="fa-solid fa-users fa-sm text-gray-200"></i>
                        </span>
                        <span class="text-sm tracking-wide truncate text-gray-200 ml-2">Attendance</span>
                        <span class="ml-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </a>
                    <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1 w-full">
                        <li>
                            <a href="{{ route('admin.attendance.employee_attendance.search') }}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white {{ request()->routeIs('admin.attendance.employee_attendance.search') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Search Attendance
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.attendance.employee_attendance') }}" class=" w-full flex items-center  h-16 pl-4  text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white 
                            {{ request()->routeIs('admin.attendance.employee_attendance') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Departmental / Individual
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.attendance.employee_attendance.payroll') }}" class="flex items-center  h-16 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white 
                            {{ request()->routeIs('admin.attendance.employee_attendance.payroll') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Data for Payroll By Department
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.attendance.student_attendance') }}" class="flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white {{ request()->routeIs('admin.attendance.student_attendance') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Student
                            </a>
                        </li>
                        <li>
                            <a  href="{{ route('admin.dashboard') }}" class="flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white ">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Daily Monitor
                            </a>
                        </li>
                    </ul>
                </li>
                <li x-data="{ open: {{ request()->routeIs('admin.attendance.gracePeriodSet') || request()->routeIs('admin.attendance.holiday') ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="w-full cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                            <i class="fa-solid fa-cogs text-gray-200"></i>
                        </span>
                        <span class="text-sm tracking-wide truncate text-gray-200 ml-2">Settings</span>
                        <span class="ml-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </a>
                    <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1 w-full">
                        <li>
                            <a href="{{ route('admin.attendance.gracePeriodSet') }}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white 
                            {{ request()->routeIs('admin.attendance.gracePeriodSet') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Set Grace Period
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.attendance.holiday') }}" class=" w-full flex items-center  h-16 pl-4  text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white 
                           {{ request()->routeIs('admin.attendance.holiday') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Set Holiday Date
                            </a>
                        </li>
                        <li>
                            <a href="" class=" w-full flex items-center  h-16 pl-4  text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white 
                            ">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Change Password
                            </a>
                        </li>
                        
                    </ul>
                </li>
                <!-- <li x-data="{ open: false }">
                    <a @click="open = !open" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-file-lines fa-sm text-gray-200"></i>
                        </span>
                        <span class=" text-sm tracking-wide truncate text-gray-200 ml-2">Reports</span>
                        <span class="ml-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </a>
                    <ul x-show="open" @click.away="open = false" x-cloak class="ml-4 mt-1 space-y-1">
                        <li>
                            <a href="" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white">
                               <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Admin Staff
                            </a>
                        </li>
                        <li>
                            <a href="" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white">
                                 <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Employee
                            </a>
                        </li>
                        <li>
                            <a href="" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white">
                                 <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Student
                            </a>
                        </li>
                    </ul>
                </li> -->

                <li>
                    <form id="logout" method="POST" action="{{ route('logout') }}" onsubmit="return confirmLogout(event)">
                        @csrf

                        <button type="submit" class="relative flex flex-row items-center w-full h-11 focus:outline-none  hover:bg-[#172029] text-white] dark:hover:bg-slate-700 text-gray-200 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                            <span class="inline-flex justify-center items-center ml-5">
                                <i class="fa-solid fa-right-from-bracket fa-sm text-gray-200"></i>
                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate text-gray-200">{{ __('Sign Out') }}</span>
                        </button>
                    </form>
                </li>
            </ul>
                <p class="mb-14 px-5 py-3 hidden md:block text-center text-xs text-white">Copyright @2024</p>
        </div>
    </div>










@elseif (Auth::user()->hasRole('admin_staff'))

    <div x-cloak x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                        window.addEventListener('resize', () => {
                            isFullScreen = (window.innerHeight === screen.height);
                        });
                    " x-show="!isFullScreen" id="sidebarContainer"  class="fixed flex flex-col left-0 w-14 hover:w-48 md:w-48 bg-gradient-to-r from-red-500 to-orange-500 h-full text-black transition-all duration-300 border-r-2 border-gray-300 dark:border-gray-600 z-10 sidebar">
        <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow mr-0.5">
            <ul class="flex flex-col py-2 space-y-1 text-gray-800" >
                <a href="#" class="flex justify-center items-center">
                    <img class="w-32 h-auto object-contain" src="{{ asset('assets/img/logo.png') }}" alt="SCMS Logo">
                </a>

                <label class="relative flex flex-row justify-center items-center h-2 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent  pr-3 ">
                    <span class=" text-sm tracking-wide truncate text-gray-200">{{ Auth::user()->name }}</span>
                </label>
                <label class="relative flex flex-row justify-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent   ">
                    <span class=" text-xs tracking-wide truncate text-gray-200">{{ Auth::user()->email }}</span>
                </label>
                <div class="border-t"></div>
                <li>
                    <a href="{{ route('staff.dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('staff.dashboard') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-gauge-high fa-sm text-gray-200 "></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Dashboard</span>
                    </a>
                </li>
                <!-- <li >
                    <a href="{{ route('staff.school.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('staff.school.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">School Year</span>
                    </a>
                </li> -->
                <li x-data="{ open: {{ request()->routeIs('staff.department.index')  || request()->routeIs('staff.workinghour.index') ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                            <i class="fa-solid fa-users fa-sm text-gray-200"></i>
                        </span>
                        <span class="text-sm tracking-wide truncate text-gray-200 ml-2">Department</span>
                        <span class="ml-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </a>
                    <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('staff.department.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                            {{ request()->routeIs('staff.department.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <span class="inline-flex justify-center items-center ml-4">
                                    <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                                </span>
                                <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Department</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.workinghour.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                            {{ request()->routeIs('staff.workinghour.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <span class="inline-flex justify-center items-center ml-4">
                                    <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                                </span>
                                <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Working Hour</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('staff.course.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('staff.course.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Courses</span>
                    </a>
                </li>
                <li x-data="{ open: {{ request()->routeIs('admin.staff.index')  || request()->routeIs('staff.employee.index') || request()->routeIs('staff.student.index') ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                            <i class="fa-solid fa-users fa-sm text-gray-200"></i>
                        </span>
                        <span class="text-sm tracking-wide truncate text-gray-200 ml-2"> Manage Users</span>
                        <span class="ml-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </a>
                    <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1">
                        <!-- <li>
                            <a href="{{ route('admin.staff.index') }}" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white {{ request()->routeIs('admin.staff.index') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                            <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Admin Staff
                            </a>
                        </li> -->
                        <li>
                            <a href="{{ route('staff.employee.index') }}" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white 
                                {{ request()->routeIs('staff.employee.index') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Employee
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.student.index') }}" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white 
                                {{ request()->routeIs('staff.student.index') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Student
                            </a>
                        </li>
                    </ul>
                </li>
                

                <li>
                    <form id="logout" method="POST" action="{{ route('logout') }}" onsubmit="return confirmLogout(event)">
                        @csrf

                        <button type="submit" class="relative flex flex-row items-center w-full h-11 focus:outline-none  hover:bg-[#172029] text-white] dark:hover:bg-slate-700 text-gray-200 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                            <span class="inline-flex justify-center items-center ml-5">
                                <i class="fa-solid fa-right-from-bracket fa-sm text-gray-200"></i>
                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate text-gray-200">{{ __('Sign Out') }}</span>
                        </button>
                    </form>
                </li>
            </ul>
                <p class="mb-14 px-5 py-3 hidden md:block text-center text-xs text-white">Copyright @2024</p>
        </div>
    </div>


    <!-- this is HRD -->
@elseif (Auth::user()->hasRole('employee'))

    <div x-cloak x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                        window.addEventListener('resize', () => {
                            isFullScreen = (window.innerHeight === screen.height);
                        });
                    " x-show="!isFullScreen" id="sidebarContainer"  class="fixed flex flex-col left-0 w-14 hover:w-48 md:w-48 bg-gradient-to-r from-red-500 to-orange-500 h-full text-black transition-all duration-300 border-r-2 border-gray-300 dark:border-gray-600 z-10 sidebar">
        <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow mr-0.5">
            <ul class="flex flex-col py-2 space-y-1 text-gray-800" >
                <a href="#" class="flex justify-center items-center">
                    <img class="w-32 h-auto object-contain" src="{{ asset('assets/img/logo.png') }}" alt="SCMS Logo">
                </a>

                <label class="relative flex flex-row justify-center items-center h-2 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent  pr-3 ">
                    <span class=" text-sm tracking-wide truncate text-gray-200">{{ Auth::user()->name }}</span>
                </label>
                <label class="relative flex flex-row justify-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent   ">
                    <span class=" text-xs tracking-wide truncate text-gray-200">{{ Auth::user()->email }}</span>
                </label>
                <div class="border-t"></div>
                <li>
                    <a href="{{ route('hr.dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('hr.dashboard') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-gauge-high fa-sm text-gray-200 "></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Dashboard</span>
                    </a>
                </li>
                <!-- <li >
                    <a href="{{ route('staff.school.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('staff.school.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">School Year</span>
                    </a>
                </li> -->
                <li x-data="{ open: {{ request()->routeIs('hr.department.index')  || request()->routeIs('hr.workinghour.index') ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                            <i class="fa-solid fa-users fa-sm text-gray-200"></i>
                        </span>
                        <span class="text-sm tracking-wide truncate text-gray-200 ml-2">Department</span>
                        <span class="ml-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </a>
                    <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('hr.department.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                            {{ request()->routeIs('hr.department.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <span class="inline-flex justify-center items-center ml-4">
                                    <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                                </span>
                                <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Department</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('hr.workinghour.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                            {{ request()->routeIs('hr.workinghour.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <span class="inline-flex justify-center items-center ml-4">
                                    <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                                </span>
                                <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Working Hour</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('staff.course.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('staff.course.index') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-school fa-sm text-gray-200 "></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Courses</span>
                    </a>
                </li>
                <li x-data="{ open: {{ request()->routeIs('admin.staff.index')  || request()->routeIs('staff.employee.index') || request()->routeIs('staff.student.index') ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                            <i class="fa-solid fa-users fa-sm text-gray-200"></i>
                        </span>
                        <span class="text-sm tracking-wide truncate text-gray-200 ml-2">Users</span>
                        <span class="ml-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </a>
                    <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1">
                        <!-- <li>
                            <a href="{{ route('admin.staff.index') }}" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white {{ request()->routeIs('admin.staff.index') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                            <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Admin Staff
                            </a>
                        </li> -->
                        <li>
                            <a href="{{ route('staff.employee.index') }}" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white 
                                {{ request()->routeIs('staff.employee.index') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Employee
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.student.index') }}" class="flex items-center h-11 pl-8 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white 
                                {{ request()->routeIs('staff.student.index') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Student
                            </a>
                        </li>
                    </ul>
                </li>
                

                <li>
                    <form id="logout" method="POST" action="{{ route('logout') }}" onsubmit="return confirmLogout(event)">
                        @csrf

                        <button type="submit" class="relative flex flex-row items-center w-full h-11 focus:outline-none  hover:bg-[#172029] text-white] dark:hover:bg-slate-700 text-gray-200 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                            <span class="inline-flex justify-center items-center ml-5">
                                <i class="fa-solid fa-right-from-bracket fa-sm text-gray-200"></i>
                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate text-gray-200">{{ __('Sign Out') }}</span>
                        </button>
                    </form>
                </li>
            </ul>
                <p class="mb-14 px-5 py-3 hidden md:block text-center text-xs text-white">Copyright @2024</p>
        </div>
    </div>

@endif


<!-- end of admin navigation -->
    <script>
            function confirmLogout(event) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: 'Are you sure you want to logout?',
            text: "Save everything before leaving",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, submit the deleteSelectedForm form programmatically
                document.getElementById('logout').submit();
            }
        });
    }
    </script>