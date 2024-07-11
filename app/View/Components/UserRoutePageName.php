<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class UserRoutePageName extends Component
{
    /**
     * Create a new component instance.
     */

    public $title;

    public function __construct(string $routeName)
    {
        $this->setTitle($routeName);
    }

     protected function setTitle(string $routeName)
    {
        if (!Auth::check()) {
            $this->title = __('University of Bohol Attendance System');
            return;
        }

         if (Auth::user()->hasRole('admin')) {
            
            $titles = [
                //admin route pages name
                'admin.dashboard' => __('Admin Dashboard'),
                //route page name for managing school
                'admin.school.index' => __('Admin - Manage School'),
                'admin.department.index' => __('Admin - Manage Department'),
                'admin.staff.index' => __('Admin - Manage Admin Staff'),
                'admin.employee.index' => __('Admin - Manage Employee'),
                'admin.student.index' => __('Admin - Manage Student'),
                'admin.course.index' => __('Admin - Manage Courses'),
                'admin.attendance.employee_attendance' => __('Admin - Employee Attendance'),
                'admin.attendance.employee_attendance.portal' => __('Employee Attendance Portal'),
            ];

            $this->title = $titles[$routeName] ?? __('University of Bohol Attendance System');

        }
        else if (Auth::user()->hasRole('employee')) {

            $titles = [

                //employee route pages name
                'employee.dashboard' => __('Employee Dashboard'),
            ];

            $this->title = $titles[$routeName] ?? __('University of Bohol Attendance System');

        }
         else {
            $this->title = __('University of Bohol Attendance System');
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-route-page-name');
    }
}
