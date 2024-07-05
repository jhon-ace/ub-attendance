<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Admin\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;


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
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'department_id' => [
                'required',
                'exists:departments,id',
                Rule::unique('employees')->where(function ($query) use ($request) {
                    return $query->where('employee_id', $request->employee_id)
                        ->where('department_id', '!=', $request->department_id);
                }),
            ],
            'employee_id' => [
                'required',
                'string',
                'max:255',
                // Example: 'exists:employees,employee_id' if it exists in another table
            ],
            'employee_firstname' => 'required|string|max:255',
            'employee_middlename' => 'required|string|max:255',
            'employee_lastname' => 'required|string|max:255',
            'employee_rfid' => 'required|string|max:255|unique:employees',
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

        $employee = new Employee();
        $employee->school_id = $request->input('school_id');
        $employee->department_id = $request->input('department_id');
        $employee->employee_id= $request->input('employee_id');
        $employee->employee_firstname = $request->input('employee_firstname');
        $employee->employee_middlename = $request->input('employee_middlename');
        $employee->employee_rfid = $request->input('employee_rfid');
        $employee->employee_lastname = $request->input('employee_lastname');
        $employee->employee_photo = $fileNameToStore;
        $employee->save();


        return redirect()->route('admin.employee.index')
            ->with('success', 'Employee created successfully.');
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
    public function update(Request $request, Employee $employee)
    {
        $validatedData = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'required|string|max:255|unique:employees,employee_id,' . $employee->id,
            'employee_firstname' => 'required|string|max:255',
            'employee_middlename' => 'required|string|max:255',
            'employee_lastname' => 'required|string|max:255',
            'employee_rfid' => 'required|string|max:255|unique:employees,employee_rfid,' . $employee->id,
        ]);

        // Check for changes
        $changesDetected = false;
        foreach ($validatedData as $key => $value) {
            if ($employee->$key !== $value) {
                $changesDetected = true;
                break;
            }
        }

        if (!$changesDetected) {
            return redirect()->route('admin.employee.index')->with('info', 'No changes were made.');
        }

        // Update the employee record
        $employee->update($validatedData);

        return redirect()->route('admin.employee.index')->with('success', 'Employee updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
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
