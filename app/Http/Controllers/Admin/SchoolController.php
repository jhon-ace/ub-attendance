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
        
        $validatedData = $request->validate([
            'school_name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:255|unique:schools,abbreviation,' . $school->id,
        ]);

        // Check for changes
        $changes = false;
        foreach ($validatedData as $key => $value) {
            if ($school->$key !== $value) {
                $changes = true;
                break;
            }
        }

        if (!$changes) {
            return redirect()->route('admin.school.index')->with('info', 'No changes were made.');
        }

        $school->update($validatedData);

        return redirect()->route('admin.school.index')->with('success', 'School updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('admin.school.index')->with('success', 'School deleted successfully.');
    }

    public function deleteAll(Request $request)
    {
        $count = School::count();

        if ($count === 0) {
            return redirect()->route('admin.school.index')->with('info', 'There are no schools to delete.');
        }
        else{
            
            School::truncate();
            return redirect()->route('admin.school.index')->with('success', 'All schools deleted successfully.');
        }

        
    }


}
