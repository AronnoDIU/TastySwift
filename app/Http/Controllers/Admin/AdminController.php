<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use JsonException;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            if ($admin->avatar) {
                // Remove 'public/' from the path if it exists
                $oldPath = str_replace('storage/', '', $admin->avatar);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Store the new avatar
            $path = $request->file('avatar')?->store('admin/avatars', 'public');
            $validated['avatar'] = $path;

            // Clear the avatar URL from the model's cache
            if (method_exists($admin, 'forgetCachedAttributes')) {
                $admin->forgetCachedAttributes(['avatar_url']);
            }
        }

        $admin->update($validated);

        // Clear the avatar URL from the model's cache after update
        if (method_exists($admin, 'forgetCachedAttributes')) {
            $admin->forgetCachedAttributes(['avatar_url']);
        }

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
        $admin = auth('admin')->user();
        $activities = $admin->actions()
            ->latest()
            ->paginate(10); // Changed from take(10)->get() to paginate(10)

        return view('admin.settings', [
            'admin' => $admin,
            'activities' => $activities
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
        $activities = $admin->activities()->latest()->paginate();

        return view('admin.activity-log', compact('activities'));
    }

    /**
     * Export the activity log in the specified format.
     */
    public function exportActivityLog(Request $request): StreamedResponse
    {
        $request->validate([
            'format' => 'required|in:csv,xls,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $admin = auth('admin')->user();
        $activities = $admin->activities()->latest();

        // Apply date filters if provided
        if ($request->filled('start_date')) {
            $activities->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $activities->whereDate('created_at', '<=', $request->end_date);
        }

        $activities = $activities->get();

        $format = $request->input('format');
        $fileName = 'activity-log-' . now()->format('Y-m-d-H-i-s') . '.' . $format;

        if ($format === 'csv') {
            return $this->exportToCsv($activities, $fileName);
        }

        if ($format === 'xls') {
            return $this->exportToExcel($activities, $fileName);
        }

        return $this->exportToPdf($activities, $fileName);
    }

    /**
     * Export activities to CSV format.
     */
    protected function exportToCsv($activities, $fileName): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'wb');

            // Add CSV headers
            fputcsv($file, [
                'Event', 'Description', 'Date', 'IP Address', 'User Agent'
            ]);

            // Add data rows
            foreach ($activities as $activity) {
                fputcsv($file, [
                    ucfirst($activity->event_name),
                    $activity->description,
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $activity->properties->get('ip_address', 'N/A'),
                    $activity->properties->get('user_agent', 'N/A')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export activities to Excel format.
     */
    protected function exportToExcel($activities, $fileName): StreamedResponse
    {
        // For Excel export, we'll just return a CSV for now
        // In a real app, you'd use a package like Maatwebsite/Excel
        return $this->exportToCsv($activities, $fileName);
    }

    /**
     * Export activities to PDF format.
     */
    protected function exportToPdf($activities, $fileName): StreamedResponse
    {
        // For PDF export, we'll just return a CSV for now
        // In a real app, you'd use a package like barryvdh/laravel-dompdf
        return $this->exportToCsv($activities, $fileName);
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

        return back()->with('status', 'API token has been revoked.');
    }

    /**
     * Delete the admin's account.
     */
    public function destroyProfile(Request $request): RedirectResponse
    {
        $request->validateWithBag('adminDeletion', [
            'password' => ['required', 'current-password:admin'],
        ]);

        $admin = $request->user('admin');

        // Log out the admin
        auth('admin')->logout();

        // Delete the admin's profile photo if it exists
        if ($admin->profile_photo_path) {
            Storage::disk('public')->delete($admin->profile_photo_path);
        }

        // Delete the admin
        $admin->delete();

        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('status', 'Your account has been permanently deleted.');
    }

    /**
     * Enable two-factor authentication for the admin.
     */
    public function enableTwoFactorAuth(Request $request): JsonResponse
    {
        $admin = $request->user('admin');

        // Generate a new secret key if one doesn't exist
        if (empty($admin->two_factor_secret)) {
            $google2fa = app('pragmarx.google2fa');
            try {
                $secretKey = $google2fa->generateSecretKey();
            } catch (IncompatibleWithGoogleAuthenticatorException|InvalidCharactersException|SecretKeyTooShortException $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            $admin->forceFill([
                'two_factor_secret' => encrypt($secretKey),
                'two_factor_recovery_codes' => null,
            ])->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Two-factor authentication has been enabled.'
        ]);
    }

    /**
     * Disable two-factor authentication for the admin.
     */
    public function disableTwoFactorAuth(Request $request): JsonResponse
    {
        $admin = $request->user('admin');

        $admin->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Two-factor authentication has been disabled.'
        ]);
    }

    /**
     * Get the two-factor authentication QR code.
     */
    public function getTwoFactorQrCode(Request $request): JsonResponse
    {
        $admin = $request->user('admin');

        if (empty($admin->two_factor_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not enabled.'
            ], 400);
        }

        $google2fa = app('pragmarx.google2fa');
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $admin->email,
            decrypt($admin->two_factor_secret)
        );

        return response()->json([
            'success' => true,
            'qr_code_url' => $qrCodeUrl
        ]);
    }

    /**
     * Get the two-factor authentication secret key.
     */
    public function getTwoFactorSecretKey(Request $request): JsonResponse
    {
        $admin = $request->user('admin');

        if (empty($admin->two_factor_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not enabled.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'secret_key' => decrypt($admin->two_factor_secret)
        ]);
    }

    /**
     * Verify the two-factor authentication code.
     */
    public function verifyTwoFactorCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $admin = $request->user('admin');

        if (empty($admin->two_factor_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Two-factor authentication is not enabled.'
            ], 400);
        }

        $google2fa = app('pragmarx.google2fa');
        try {
            $valid = $google2fa->verifyKey(
                decrypt($admin->two_factor_secret),
                $request->code
            );
        } catch (IncompatibleWithGoogleAuthenticatorException|InvalidCharactersException|SecretKeyTooShortException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        if ($valid) {
            // Generate recovery codes if they don't exist
            if (empty($admin->two_factor_recovery_codes)) {
                $recoveryCodes = collect(
                    array_map(static function () {
                        return strtoupper(Str::random(10));
                    }, range(1, 8))
                )->toArray();

                try {
                    $admin->forceFill([
                        'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes, JSON_THROW_ON_ERROR)),
                    ])->save();
                } catch (JsonException $e) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 400);
                }

                return response()->json([
                    'success' => true,
                    'recovery_codes' => $recoveryCodes,
                    'message' => 'Two-factor authentication has been verified and recovery codes have been generated.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'The code is valid.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The provided code is invalid.'
        ], 422);
    }
}
