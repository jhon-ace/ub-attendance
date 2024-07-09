<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Admin\School; 
use \App\Models\Admin\Department;
use \App\Models\Admin\Employee;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Admin.department.index');
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
            'department_id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments')->where(function ($query) use ($request) {
                    return $query->where('school_id', $request->school_id);
                })->ignore($request->department_id, 'department_id'), // Ensure to ignore by 'department_id'
            ],
            'department_abbreviation' => 'required|string|max:255',
            'department_name' => 'required|string|max:255',
            'dept_identifier' => 'required|string|max:255',
        ], [
            'department_id.unique' => 'The department ID is not valid.',
        ]);

        // Attempt to create the Department record
        try {
            Department::create($validatedData);
            
            // If creation succeeds, redirect with success message
            return redirect()->route('admin.department.index')
                ->with('success', 'Department created successfully.');
        } catch (\Exception $e) {
            // If an exception occurs (unlikely in normal validation flow)
            // Handle any specific errors or logging as needed
            // You can redirect back with an error message or do other error handling
            return  redirect()->route('admin.department.index')->with('error','The department ID is already taken in this school.');
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
    public function update(Request $request, Department $department)
    {
        try {
            $validatedData = $request->validate([
                'school_id' => 'required|exists:schools,id',
                'department_id' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('departments')->where(function ($query) use ($request, $department) {
                        return $query->where('school_id', $request->school_id)
                                    ->where('id', '<>', $department->id);
                    }),
                ],
                'department_abbreviation' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('departments')->where(function ($query) use ($request, $department) {
                        return $query->where('school_id', $request->school_id)
                                    ->where('id', '<>', $department->id);
                    }),
                ],
                'department_name' => 'required|string|max:255',
                'dept_identifier' => 'required|string|max:255',
            ]);
            
            $hasChanges = false;
            if ($request->school_id !== $department->school_id ||
                $request->department_id !== $department->department_id ||
                $request->department_abbreviation !== $department->department_abbreviation ||
                $request->department_name !== $department->department_name ||
                $request->dept_identifier !== $department->dept_identifier) 
            {
                $hasChanges = true;
            }

            if (!$hasChanges) {
                return redirect()->route('admin.department.index')->with('info', 'No changes were made.');
            }

            // Update the department record
            $department->update($validatedData);

            return redirect()->route('admin.department.index')->with('success', 'Department updated successfully.');
        } catch (ValidationException $e) {
            $errors = $e->errors();
            return redirect()->back()->withErrors($errors)->with('error', $errors['department_id'][0] ?? 'Validation error');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
         if ($department->employees()->exists()) {
            return redirect()->route('admin.department.index')->with('error', 'Cannot delete department because it has associated data.');
        }

         $department->delete();

        return redirect()->route('admin.department.index')->with('success', 'Department/s deleted successfully.');
    }

    public function deleteAll(Request $request)
    {

        $schoolId = $request->input('school_id');

        if (!$schoolId) {
            return redirect()->back()->with('error', 'No school selected.');
        }

        // Check if there are any departments associated with this school
        $departmentsWithEmployees = Department::where('school_id', $schoolId)->whereHas('employees')->exists();

        if ($departmentsWithEmployees) {
            return redirect()->route('admin.department.index')->with('error', 'Cannot delete departments because they have associated employees.');
        }

        // If no departments have associated employees, proceed with deletion
        Department::where('school_id', $schoolId)->delete();

        return redirect()->back()->with('success', 'All departments for the selected school have been deleted.');

        
    }
}
