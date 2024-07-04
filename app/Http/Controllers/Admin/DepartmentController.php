<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Admin\School; 
use \App\Models\Admin\Department; 

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
            'department_id' => 'required|string|max:255|unique:departments', 
            'department_abbreviation' => 'required|string|max:255',
            'department_name' => 'required|string|max:255',
        ]);

        Department::create($validatedData);

        return redirect()->route('admin.department.index')
                        ->with('success', 'Department created successfully.');
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
        $validatedData = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'department_id' => 'required|string|max:255|unique:departments,department_id,' . $department->id,
            'department_abbreviation' => 'required|string|max:255|unique:departments,department_abbreviation,' . $department->id,
            'department_name' => 'required|string|max:255',
        ]);

        $hasChanges = false;
        if ($request->school_id !== $department->school_id ||
            $request->department_id !== $department->department_id ||
            $request->department_abbreviation !== $department->department_abbreviation ||
            $request->department_name !== $department->department_name) 
        {
            $hasChanges = true;
        }

        if (!$hasChanges) {
            return redirect()->route('admin.department.index')->with('info', 'No changes were made.');
        }

        // Update the department record
        $department->update($validatedData);

        return redirect()->route('admin.department.index')->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
         $department->delete();

        return redirect()->route('admin.department.index')->with('success', 'Department/s deleted successfully.');
    }

    public function deleteAll(Request $request)
    {
        $count = Department::count();

        if ($count === 0) {
            return redirect()->route('admin.department.index')->with('info', 'There are no department/s to delete.');
        }
        else{
            
            Department::truncate();
            return redirect()->route('admin.department.index')->with('success', 'All department/s deleted successfully.');
        }

        
    }
}
