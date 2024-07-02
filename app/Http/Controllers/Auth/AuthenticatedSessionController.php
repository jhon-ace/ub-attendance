<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {

        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::check()) 
        {
            if (Auth::user()->hasRole('admin')) 
            {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Successful Login');
            } 
            else if (Auth::user()->hasRole('employee')) 
            {
                return redirect()->intended(route('employee.dashboard'))->with('success', 'Successful Login');
            } 
            else if (Auth::user()->hasRole('student')) 
            {
                return redirect()->intended(route('student.dashboard'))->with('success', 'Successful login');
            }
        }

        // Default redirect if no specific role matches
        return redirect()->intended(route('/'));
        

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
