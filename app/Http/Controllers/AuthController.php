<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login', ['metaTitle' => 'Customer Login', 'isAdminLogin' => false]);
    }

    public function showRegister()
    {
        return view('auth.register', ['metaTitle' => 'Customer Register']);
    }

    public function showAdminLogin()
    {
        return view('auth.login', ['metaTitle' => 'Admin Login', 'isAdminLogin' => true]);
    }

    public function login(Request $request)
    {
        $guestCart = Cart::where('session_token', $request->session()->getId())->latest()->first();
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid login credentials.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $this->mergeGuestCart($guestCart, $request->user());

        return redirect()->intended(route($request->user()->dashboardRouteName()));
    }

    public function adminLogin(Request $request)
    {
        $response = $this->login($request);

        if ($request->user()?->isAdmin()) {
            return $response;
        }

        Auth::logout();

        return redirect()->route('admin.login')->withErrors(['email' => 'This account does not have admin access.']);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:120'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([...$validated, 'role' => 'customer']);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome! Your customer account is ready.');
    }

    public function redirectToGoogle()
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->withErrors(['email' => 'Google login needs OAuth credentials in the application settings.']);
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $guestCart = Cart::where('session_token', $request->session()->getId())->latest()->first();

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()->route('login')->withErrors(['email' => 'Google login could not be completed. Please try again.']);
        }

        if (! $googleUser->getEmail()) {
            return redirect()->route('login')->withErrors(['email' => 'Your Google account did not provide an email address.']);
        }

        $user = User::where('google_id', $googleUser->getId())->orWhere('email', $googleUser->getEmail())->first();
        if ($user?->isAdmin()) {
            return redirect()->route('login')->withErrors(['email' => 'Admin accounts must use the secure admin login.']);
        }

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: Str::before($googleUser->getEmail(), '@'),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password' => Str::random(40),
                'role' => 'customer',
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $this->mergeGuestCart($guestCart, $user);

        return redirect()->intended(route('dashboard'))->with('success', 'Welcome! You are signed in with Google.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function mergeGuestCart(?Cart $guestCart, User $user): void
    {
        if (! $guestCart || ! $user->isCustomer()) {
            return;
        }

        $userCart = Cart::where('user_id', $user->id)->latest()->first();
        if ($userCart) {
            $guestCart->items()->update(['cart_id' => $userCart->id]);
            $guestCart->delete();
            return;
        }

        $guestCart->update(['user_id' => $user->id, 'session_token' => null]);
    }
}
