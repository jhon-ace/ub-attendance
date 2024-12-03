<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\Admin\Fingerprint;

class FingerprintController extends Controller
{
    public function index()
    {
        return view('Admin.fingerprint.index');
    }

    public function activate_fingerprint()
    {
        $fingerprint = Fingerprint::all();
        return view('Admin.fingerprint.activate', compact('fingerprint'));
    }

    public function set_fingerprint(Request $request, $id)
    {
        $request->validate([
            'set_status' => 'integer'
        ]);

        $fingerprint = Fingerprint::findOrFail($id);
        $fingerprint->fingerprint_status = $request->set_status;
        $fingerprint->save();

        return redirect()->route('admin_staff.fingerprint')->with('success', 'Successfully updated fingerprint availability.');
    }
}
