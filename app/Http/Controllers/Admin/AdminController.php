<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'admin' => auth('admin')->user(),
            'stats' => [
                'totalAdmins' => Admin::count(),
                'activeAdmins' => Admin::where('status', 'active')->count(),
                'inactiveAdmins' => Admin::where('status', 'inactive')->count(),
                'suspendedAdmins' => Admin::where('status', 'suspended')->count(),
            ]
        ]);
    }

    /**
     * Display the admin's profile page.
     */
    public function profile(): View
    {
        return view('admin.profile', [
            'admin' => auth('admin')->user()
        ]);
    }

    /**
     * Update the admin's profile information.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $admin = $request->user('admin');
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins')->ignore($admin->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($admin->avatar && Storage::exists('public/' . $admin->avatar)) {
                Storage::delete('public/' . $admin->avatar);
            }
            
            $path = $request->file('avatar')->store('admin/avatars', 'public');
            $validated['avatar'] = $path;
        }

        $admin->update($validated);

        return back()->with('status', 'Profile updated successfully!');
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->user('admin')->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'Password updated successfully!');
    }

    /**
     * Display the admin settings page.
     */
    public function settings(): View
    {
        return view('admin.settings', [
            'admin' => auth('admin')->user()
        ]);
    }

    /**
     * Update the admin's settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'timezone' => ['required', 'string', 'timezone'],
            'date_format' => ['required', 'string', 'in:Y-m-d,d/m/Y,m/d/Y,d M Y,M d, Y'],
            'time_format' => ['required', 'string', 'in:H:i,h:i A'],
            'per_page' => ['required', 'integer', 'min:5', 'max:100'],
        ]);

        $admin = $request->user('admin');
        $admin->settings = array_merge((array) $admin->settings, $validated);
        $admin->save();

        return back()->with('status', 'Settings updated successfully!');
    }

    /**
     * Display the admin activity log.
     */
    public function activityLog(): View
    {
        $admin = auth('admin')->user();
        $activities = $admin->activities()->latest()->paginate(15);
        
        return view('admin.activity-log', compact('activities'));
    }

    /**
     * Generate a new API token for the admin.
     */
    public function generateApiToken(Request $request): RedirectResponse
    {
        $token = $request->user('admin')->createToken('api-token');
        
        return back()->with([
            'status' => 'API token generated successfully!',
            'api_token' => $token->plainTextToken,
        ]);
    }

    /**
     * Revoke the current API token.
     */
    public function revokeApiToken(Request $request): RedirectResponse
    {
        $request->user('admin')->tokens()->delete();
        
        return back()->with('status', 'API token revoked successfully!');
    }
}
