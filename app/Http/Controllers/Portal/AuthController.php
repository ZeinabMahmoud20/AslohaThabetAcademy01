<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Guardian portal authentication -- fully separate from the staff `web` guard.
 * Login is phone_number + access_code (plaintext by design, see Guardian model),
 * so this does a direct comparison rather than Laravel's default hashed
 * Auth::attempt() flow.
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('guardian')->check()) {
            return redirect()->route('portal.dashboard');
        }

        return view('portal.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone_number' => 'required|string',
            'access_code' => 'required|string',
        ]);

        $guardian = Guardian::where('phone_number', $data['phone_number'])->first();

        if (! $guardian || ! $guardian->access_code || $guardian->access_code !== $data['access_code']) {
            throw ValidationException::withMessages([
                'access_code' => 'رقم الهاتف أو الكود غير صحيح.',
            ]);
        }

        Auth::guard('guardian')->login($guardian);
        $request->session()->regenerate();

        return redirect()->route('portal.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('guardian')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}