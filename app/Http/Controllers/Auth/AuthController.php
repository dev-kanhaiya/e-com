<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\VendorProfile;
use App\Services\MailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login submission.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Check if user is blocked
            if ($user->isBlocked()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your account is blocked.',
                ]);
            }

            $request->session()->regenerate();

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            if ($user->isVendor()) {
                return redirect()->intended(route('vendor.dashboard'));
            }

            return redirect()->intended(route('customer.products.index'));
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    /**
     * Show registration form.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $role = $request->role ?? 'customer';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'status' => 'active',
        ]);

        // If vendor registered, create vendor profile
        if ($role === 'vendor') {
            $storeName = $request->store_name ?: ($request->name."'s Store");
            VendorProfile::create([
                'user_id' => $user->id,
                'store_name' => $storeName,
                'store_slug' => Str::slug($storeName).'-'.Str::random(5),
                'description' => 'Welcome to my store!',
                'status' => 'approved', // Auto-approved or pending by default
            ]);
        }

        Auth::login($user);

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isVendor()) {
            return redirect()->route('vendor.dashboard');
        }

        return redirect()->route('customer.products.index');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    /**
     * Show forgot password view.
     */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Reset password and send new password to registered email.
     */
    public function sendResetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withInput()->withErrors([
                'email' => 'No account found with this email address.',
            ]);
        }

        if ($user->isBlocked()) {
            return back()->withErrors([
                'email' => 'This account is blocked. Please contact support.',
            ]);
        }

        // Generate clean alphanumeric temporary password
        $newPassword = Str::password(10, letters: true, numbers: true, symbols: false);

        $user->password = Hash::make($newPassword);
        $user->save();

        // Send mail
        $sent = MailService::sendPasswordResetMail($user, $newPassword);

        if ($sent) {
            return redirect()->route('login')->with('success', 'A new password has been sent to your registered email address (' . $user->email . '). Please check your inbox/spam and login.');
        } else {
            return redirect()->route('login')->with('success', 'Password reset successfully. Your temporary new password is: ' . $newPassword . ' (Please log in and update it).');
        }
    }
}
