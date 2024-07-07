<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use \App\Models\Admin\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;


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
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'school_id' => 'sometimes|required|exists:schools,id',
                'department_id' => 'sometimes|required|exists:departments,id',
                'employee_id' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('employees')->where(function ($query) use ($request, $employee) {
                        if (isset($request->department_id)) {
                            return $query->where('department_id', $request->department_id)
                                        ->where('id', '!=', $employee->id);
                        }
                        // Handle the case when department_id is not set
                        return $query->where('id', '!=', $employee->id);
                    }),
                ],
                'employee_firstname' => 'sometimes|required|string|max:255',
                'employee_middlename' => 'sometimes|required|string|max:255',
                'employee_lastname' => 'sometimes|required|string|max:255',
                'employee_rfid' => [
                    'sometimes',
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('employees')->where(function ($query) use ($request, $employee) {
                        if (isset($request->department_id)) {
                            return $query->where('department_id', $request->department_id)
                                        ->where('employee_rfid', '!=', $employee->employee_rfid);
                        }
                        // Handle the case when department_id is not set
                        return $query->where('employee_rfid', '!=', $employee->employee_rfid);
                    }),
                ],
            ]);

            // Check if department_id is set in validatedData
            $departmentId = $validatedData['department_id'] ?? null;

            // Check if employee_id already exists in the selected department
            if (isset($validatedData['employee_id']) && $departmentId &&
                Employee::where('employee_id', $validatedData['employee_id'])
                        ->where('department_id', $departmentId)
                        ->where('id', '!=', $employee->id)
                        ->exists()) {
                $existingEmployee = Employee::where('employee_id', $validatedData['employee_id'])
                                            ->where('department_id', $departmentId)
                                            ->first();
                return redirect()->route('admin.employee.index')->with('error', 'Employee ID: ' . $validatedData['employee_id'] . ' is already taken by ' . $existingEmployee->employee_lastname . ', ' . $existingEmployee->employee_firstname . ' in the selected department.');
            }

            // Update the employee with validated data
            $employee->fill($validatedData);

            if (!$employee->isDirty()) {
                return redirect()->route('admin.employee.index')->with('info', 'No changes were made.');
            }

            $employee->save();

            return redirect()->route('admin.employee.index')->with('success', 'Employee updated successfully.');

        } catch (ValidationException $e) {
            $errors = $e->errors();
            return redirect()->back()->withErrors($errors)->with('error', $errors['employee_id'][0] ?? 'Validation error');
        }
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
