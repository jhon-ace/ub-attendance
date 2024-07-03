<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Admin\Student;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Admin.student.index');
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
            'student_id' => 'required|string|max:255|unique:students', 
            'student_firstname' => 'required|string|max:255',
            'student_middlename' => 'required|string|max:255',
            'student_lastname' => 'required|string|max:255',
            'student_rfid' => 'required|string|max:255|unique:students',

        ]);

        Student::create($validatedData);

        return redirect()->route('admin.student.index')
                        ->with('success', 'Student created successfully.');
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
    public function update(Request $request, Student $student)
    {
            $validatedData = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'student_id' => 'required|string|max:255|unique:students,student_id,' . $student->id,
            'student_firstname' => 'required|string|max:255',
            'student_middlename' => 'required|string|max:255',
            'student_lastname' => 'required|string|max:255',
            'student_rfid' => 'required|string|max:255|unique:students,student_rfid,' . $student->id,
        ]);

        $hasChanges = false;
        if ($request->school_id !== $student->school_id ||
            $request->student_id !== $student->student_id ||
            $request->student_firstname !== $student->student_firstname ||
            $request->student_middlename !== $student->student_middlename ||
            $request->student_lastname !== $student->student_lastname ||
            $request->student_rfid !== $student->student_rfid) 
        {
            $hasChanges = true;
        }

        if (!$hasChanges) {
            return redirect()->route('admin.student.index')->with('info', 'No changes were made.');
        }

        // Update the student record
        $student->update($validatedData);

        return redirect()->route('admin.student.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
         $student->delete();

        return redirect()->route('admin.student.index')->with('success', 'student deleted successfully.');
    }

    public function deleteAll(Request $request)
    {
        $count = Student::count();

        if ($count === 0) {
            return redirect()->route('admin.student.index')->with('info', 'There are no student/s to delete.');
        }
        else{
            
            Student::truncate();
            return redirect()->route('admin.student.index')->with('success', 'All student/s deleted successfully.');
        }

        
    }

}
