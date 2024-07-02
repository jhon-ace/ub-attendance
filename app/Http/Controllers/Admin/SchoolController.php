<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Admin\School; 

class SchoolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Admin.school.index');
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
            'abbreviation' => 'required|string|max:255',
            'school_name' => 'required|string|max:255',
        ]);

        $school = School::create($validatedData);

    
        return redirect()->route('admin.school.index')
                        ->with('success', 'School created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, School $school)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        //
    }

        public function deleteSelected(Request $request)
    {

        $selectedSchool = $request->input('selected');

        if ($selectedSchool) {
            // // Fetch departments associated with deans
            // $departmentsWithDeans = \DB::table('deans')
            //     ->whereIn('department_id', $selectedDepartments)
            //     ->pluck('department_id')
            //     ->toArray();

            // // Get the departments that are not associated with deans
            // $departmentsWithoutDeans = array_diff($selectedDepartments, $departmentsWithDeans);

            // Attempt to delete departments without deans
            // try {
            //     if (!empty($departmentsWithoutDeans)) {
                    School::whereIn('id', $selectedSchool)->delete();
                    $message = 'Selected school/s  have been deleted successfully.';
                // }
               
                // if (!empty($departmentsWithDeans)) {
                //     $message .= ' However, the following departments could not be deleted because they are associated with deans: ' 
                //         . implode(', ', Department::whereIn('id', $departmentsWithDeans)->pluck('department_name')->toArray()) . '.';
                // }

                return redirect()->route('admin.school.index')->with('success', $message);
            // } catch (\Exception $e) {
            //     return redirect()->route('admin.department.index')->with('error', 'The following departments could not be deleted because they are associated with deans: ' 
            //     . implode(', ', Department::whereIn('id', $departmentsWithDeans)->pluck('department_name')->toArray()) . '.');
            // }
        } else {
            return redirect()->route('admin.school.index')->with('error', 'No school/s selected.');
        }
        
    }


}
