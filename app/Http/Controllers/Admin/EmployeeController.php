<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use \App\Models\Admin\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;



class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Admin.employee.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(Request $request)
        {
            // Validate input data
            $request->validate([
                'school_id' => 'required|exists:schools,id',
                'department_id' => [
                    'required',
                    'exists:departments,id',
                ],
                'employee_id' => [
                    'required',
                    'string',
                    'max:255',
                ],
                'employee_firstname' => 'required|string|max:255',
                'employee_middlename' => 'required|string|max:255',
                'employee_lastname' => 'required|string|max:255',
                'employee_rfid' => 'required|string|max:255',
                'employee_photo' => 'image|max:2048', // Example: validation for image upload
            ]);

            // Handle file upload if 'employee_photo' is present
            if ($request->hasFile('employee_photo')) {
                $fileNameWithExt = $request->file('employee_photo')->getClientOriginalName();
                $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('employee_photo')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $path = $request->file('employee_photo')->storeAs('public/employee_photo', $fileNameToStore);
            } else {
                $fileNameToStore = 'user.png'; // Default file if no photo is uploaded
            }

            // Check if an employee with the same employee_id or employee_rfid already exists
            $existingEmployeeById = Employee::where('employee_id', $request->input('employee_id'))->first();
            $existingEmployeeByRfid = Employee::where('employee_rfid', $request->input('employee_rfid'))->first();

            if (!$existingEmployeeById && !$existingEmployeeByRfid) {
                $employee = new Employee();
                $employee->school_id = $request->input('school_id');
                $employee->department_id = $request->input('department_id');
                $employee->employee_id = $request->input('employee_id');
                $employee->employee_firstname = $request->input('employee_firstname');
                $employee->employee_middlename = $request->input('employee_middlename');
                $employee->employee_rfid = $request->input('employee_rfid');
                $employee->employee_lastname = $request->input('employee_lastname');
                $employee->employee_photo = $fileNameToStore;
                $employee->save();

                return redirect()->route('admin.employee.index')
                    ->with('success', 'Employee created successfully.');
            } else {
                $errorMessage = '';
                if ($existingEmployeeById) {
                    $employeeName = $existingEmployeeById->employee_firstname . ' ' . $existingEmployeeById->employee_lastname;
                    $errorMessage .= 'Employee ID ' . $request->input('employee_id') . ' is already taken by ' . $employeeName . '. ';
                }
                if ($existingEmployeeByRfid) {
                    $employeeName = $existingEmployeeByRfid->employee_firstname . ' ' . $existingEmployeeByRfid->employee_lastname;
                    $errorMessage .= 'Employee RFID No. ' . $request->input('employee_rfid') . ' is already taken by ' . $employeeName . '. ';
                }

                return redirect()->route('admin.employee.index')
                    ->with('error', $errorMessage . 'Try again.');
            }
        }





    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   
     public function update(Request $request, $id)
    {
        // Validate input data
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'department_id' => [
                'required',
                'exists:departments,id',
            ],
            'employee_id' => [
                'required',
                'string',
                'max:255',
            ],
            'employee_firstname' => 'required|string|max:255',
            'employee_middlename' => 'required|string|max:255',
            'employee_lastname' => 'required|string|max:255',
            'employee_rfid' => 'required|string|max:255',
            'employee_photo' => 'nullable|image|max:2048', // Validation for image upload
        ]);


        
        // Find the existing employee record
        $employee = Employee::findOrFail($id);

        // Handle file upload if 'employee_photo' is present
        if ($request->hasFile('employee_photo')) {
            // Delete the old photo if it exists
            if ($employee->employee_photo && Storage::exists('public/employee_photo/' . $employee->employee_photo)) {
                Storage::delete('public/employee_photo/' . $employee->employee_photo);
            }

            $fileNameWithExt = $request->file('employee_photo')->getClientOriginalName();
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('employee_photo')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $path = $request->file('employee_photo')->storeAs('public/employee_photo', $fileNameToStore);
        } else {
            $fileNameToStore = $employee->employee_photo; // Keep the current photo if no new photo is uploaded
        }

        // Check if an employee with the same employee_id or employee_rfid already exists, excluding the current employee
        $existingEmployeeById = Employee::where('employee_id', $request->input('employee_id'))->where('id', '!=', $id)->first();
        $existingEmployeeByRfid = Employee::where('employee_rfid', $request->input('employee_rfid'))->where('id', '!=', $id)->first();

        if (!$existingEmployeeById && !$existingEmployeeByRfid) {
            // Update employee attributes
            $employee->school_id = $request->input('school_id');
            $employee->department_id = $request->input('department_id');
            $employee->employee_id = $request->input('employee_id');
            $employee->employee_firstname = $request->input('employee_firstname');
            $employee->employee_middlename = $request->input('employee_middlename');
            $employee->employee_lastname = $request->input('employee_lastname');
            $employee->employee_rfid = $request->input('employee_rfid');
            $employee->employee_photo = $fileNameToStore;
            $employee->save();

            return redirect()->route('admin.employee.index')
                ->with('success', 'Employee updated successfully.');
        } else {
            $errorMessage = '';
            if ($existingEmployeeById) {
                $employeeName = $existingEmployeeById->employee_firstname . ' ' . $existingEmployeeById->employee_lastname;
                $errorMessage .= 'Employee ID ' . $request->input('employee_id') . ' is already taken by ' . $employeeName . '. ';
            }
            if ($existingEmployeeByRfid) {
                $employeeName = $existingEmployeeByRfid->employee_firstname . ' ' . $existingEmployeeByRfid->employee_lastname;
                $errorMessage .= 'RFID ' . $request->input('employee_rfid') . ' is already taken by ' . $employeeName . '. ';
            }

            return redirect()->route('admin.employee.index')
                ->with('error', $errorMessage . 'Try again.');
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

         $employee = Employee::findOrFail($id);

        $employee->delete();

        return redirect()->route('admin.employee.index')->with('success', 'Employee deleted successfully.');
    }

    public function deleteAll(Request $request)
    {
        $count = Employee::count();

        if ($count === 0) {
            return redirect()->route('admin.employee.index')->with('info', 'There are no employee/s to delete.');
        }
        else{
            
            Employee::truncate();
            return redirect()->route('admin.employee.index')->with('success', 'All employee/s deleted successfully.');
        }

        
    }
}
