<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Admin\Employee;

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
        $validatedData = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'employee_id' => 'required|string|max:255|unique:employees', 
            'employee_firstname' => 'required|string|max:255',
            'employee_middlename' => 'required|string|max:255',
            'employee_lastname' => 'required|string|max:255',
            'employee_rfid' => 'required|string|max:255|unique:employees',

        ]);

        Employee::create($validatedData);

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
