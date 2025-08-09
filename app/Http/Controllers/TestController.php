<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function testAuth()
    {
        // Check if the user is authenticated as admin
        if (Auth::guard('admin')->check()) {
            return 'Authenticated as admin';
        }
        
        // Check if the user is authenticated as a regular user
        if (Auth::check()) {
            return 'Authenticated as regular user';
        }
        
        return 'Not authenticated';
    }
    
    public function testSession()
    {
        // Dump the session data
        return response()->json([
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
            'auth_guard' => Auth::getDefaultDriver(),
            'auth_check' => Auth::check(),
            'auth_user' => Auth::user(),
            'admin_check' => Auth::guard('admin')->check(),
            'admin_user' => Auth::guard('admin')->user(),
        ]);
    }
}
