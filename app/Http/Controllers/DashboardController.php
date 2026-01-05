<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index()
    {
        // dd(Auth::user()->schoolMaster->school_name);
        $user = Auth::user();
        $session = Session::get(config('sso.session.session_data'));

        // Get school details
        $school = $user->schoolMaster ?? null;

        return view('dashboard', compact('user', 'session', 'school'));
    }

    /**
     * User profile page
     */
    public function profile()
    {
        // $user = Session::get(config('sso.session.user'));
        // $user = Auth::user();

        // if (!$user) {
        //     return redirect()->route('sso.login');
        // }

        // return view('profile', compact('user'));


        $user = Auth::user();
        $role = $user->getRoleNames()->first() ?? 'User';

        return view('profile', compact('user', 'role'));
    }

    public function profileUpdate()
    {
        $user = Auth::user();
        $role = $user->getRoleNames()->first() ?? 'User';

        return view('profile', compact('user', 'role'));
    }

    /**
     * Settings page
     */
    public function settings()
    {
        $user = Session::get(config('sso.session.user'));

        if (!$user) {
            return redirect()->route('sso.login');
        }

        return view('settings', compact('user'));
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $user = Session::get(config('sso.session.user'));

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'theme' => 'required|in:light,dark,system',
            'notifications_email' => 'boolean',
            'notifications_push' => 'boolean',
            'language' => 'required|in:en,es,fr,de',
            'timezone' => 'required|timezone',
            'date_format' => 'required|in:Y-m-d,m/d/Y,d/m/Y',
        ]);

        // In a real application, you would save these to your database
        // For now, we'll store in session
        $userSettings = Session::get('user_settings', []);
        $userSettings[$user['id']] = array_merge($userSettings[$user['id']] ?? [], $validated);
        Session::put('user_settings', $userSettings);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'settings' => $validated
            ]);
        }

        return back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $user = Session::get(config('sso.session.user'));

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // In a real SSO system, you would call the central auth server API
        // to change the password. For now, we'll just return success.

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully'
            ]);
        }

        return back()->with('success', 'Password changed successfully!');
    }
}
