<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Company;
use App\Models\Creator;
use App\Models\Skill;

class AuthController extends Controller
{
    /* ───────────────────────── LOGIN ───────────────────────── */

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->to($this->homeFor(Auth::user()));
        }

        return view('auth.login', [
            'seo' => [
                'title'       => 'Log in — Quick GIGS',
                'description' => 'Log in to your Quick GIGS account to post gigs, track live deliveries and manage escrow payments.',
                'canonical'   => url('/login'),
            ],
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttle = 'login:' . Str::lower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttle, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many attempts. Try again in ' . RateLimiter::availableIn($throttle) . ' seconds.',
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttle, 300);

            return back()
                ->withErrors(['email' => 'That email and password combination is not correct.'])
                ->onlyInput('email');
        }

        RateLimiter::clear($throttle);

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'This account has been disabled. Contact support.']);
        }

        $request->session()->regenerate();
        $this->rememberWorkspace($request, $user);

        return redirect()->intended($this->homeFor($user))
            ->with('toast', 'Welcome back, ' . Str::before($user->name, ' ') . '.');
    }

    /* ───────────────────────── REGISTER ───────────────────────── */

    public function showRegister(Request $request)
    {
        if (Auth::check()) {
            return redirect()->to($this->homeFor(Auth::user()));
        }

        $type = in_array($request->query('type'), ['business', 'creator'], true)
            ? $request->query('type')
            : 'business';

        return view('auth.register', [
            'type'        => $type,
            'plan'        => $request->query('plan'),
            'skillGroups' => Skill::grouped(),
            'seo'  => [
                'title'       => 'Create your free account — Quick GIGS',
                'description' => 'Sign up in 30 seconds. Hire verified freelancers or start earning as a pro on Quick GIGS. Free to join, escrow protected.',
                'canonical'   => url('/register'),
            ],
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'account_type'  => ['required', 'in:business,creator'],
            'name'          => ['required', 'string', 'max:80'],
            'email'         => ['required', 'email', 'max:120', 'unique:users,email'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'password'      => ['required', 'confirmed', Password::min(8)],
            'company_name'  => ['nullable', 'required_if:account_type,business', 'string', 'max:80'],
            'handle'        => ['nullable', 'string', 'max:40'],
            'skills'        => ['nullable', 'array', 'max:8'],
            'skills.*'      => ['string', 'max:60'],
            'terms'         => ['accepted'],
        ], [
            'company_name.required_if' => 'Tell us your company or brand name.',
            'terms.accepted'           => 'Please accept the terms to continue.',
            'email.unique'             => 'An account already exists with this email — try logging in instead.',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'password'  => Hash::make($data['password']),
            'role'      => $data['account_type'],
            'is_active' => true,
        ]);

        if ($data['account_type'] === 'business') {
            $company = Company::create([
                'name'        => $data['company_name'],
                'slug'        => Str::slug($data['company_name']) . '-' . Str::lower(Str::random(4)),
                'person_name' => $data['name'],
                'email'       => $data['email'],
                'phone'       => $data['phone'] ?? null,
                'plan'        => $request->input('plan') ? ucfirst($request->input('plan')) : 'Starter',
                'is_verified' => false,
                'is_active'   => true,
            ]);

            $user->update(['company_id' => $company->id]);
            $request->session()->put('company_id', $company->id);
        } else {
            $handle = Str::of(($data['handle'] ?? '') ?: $data['name'])->slug('')->lower()->limit(30, '');
            $handle = '@' . ($handle->isEmpty() ? 'creator' . $user->id : (string) $handle);

            if (Creator::where('handle', $handle)->exists()) {
                $handle .= $user->id;
            }

            $creator = Creator::create([
                'user_id'      => $user->id,
                'name'         => $data['name'],
                'handle'       => $handle,
                'email'        => $data['email'],
                'phone'        => $data['phone'] ?? null,
                'headline'     => 'New on Quick GIGS',
                'profile_type' => 'video_editor',
                'skills'       => array_values(array_unique(array_filter(array_map('trim', (array) ($data['skills'] ?? []))))),
                'price_from'   => 1299,
                'rating'       => 5.0,
                'is_available' => true,
                'is_verified'  => false,
            ]);

            $user->update(['creator_id' => $creator->id]);
            $request->session()->put('creator_id', $creator->id);
        }

        Auth::login($user, true);
        $request->session()->regenerate();
        $this->rememberWorkspace($request, $user->fresh());

        $message = $data['account_type'] === 'business'
            ? 'Account created. Post your first gig — it is free.'
            : 'Welcome aboard. Complete your profile to get verified and start receiving gigs.';

        return redirect()->to($this->homeFor($user->fresh()))->with('toast', $message);
    }

    /* ───────────────────────── PASSWORD RESET ───────────────────────── */

    public function showForgot()
    {
        return view('auth.forgot', [
            'seo' => [
                'title'       => 'Reset your password — Quick GIGS',
                'description' => 'Send yourself a secure link to choose a new Quick GIGS password.',
                'canonical'   => url('/forgot-password'),
                'noindex'     => true,
            ],
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $throttle = 'reset:' . Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttle, 3)) {
            throw ValidationException::withMessages([
                'email' => 'Too many reset requests. Try again in ' . RateLimiter::availableIn($throttle) . ' seconds.',
            ]);
        }

        RateLimiter::hit($throttle, 900);

        try {
            PasswordBroker::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            report($e);   // SMTP down must not reveal anything or 500 the page
        }

        // Deliberately identical whether or not the address exists.
        return back()->with('toast', 'If that email is registered, a reset link is on its way.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('auth.reset', [
            'token' => $token,
            'email' => (string) $request->query('email'),
            'seo'   => [
                'title'       => 'Choose a new password — Quick GIGS',
                'description' => 'Set a new password for your Quick GIGS account.',
                'canonical'   => url('/reset-password'),
                'noindex'     => true,
            ],
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $status = PasswordBroker::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== PasswordBroker::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)])->onlyInput('email');
        }

        return redirect()->route('login')->with('toast', 'Password updated. Log in with your new password.');
    }

    /* ───────────────────────── LOGOUT ───────────────────────── */

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('toast', 'You are logged out.');
    }

    /* ───────────────────────── helpers ───────────────────────── */

    private function homeFor(User $user): string
    {
        if ($user->isAdmin())            return route('admin.dashboard');
        if ($user->hasRole('creator'))   return route('creator.dashboard');

        return route('business.home');
    }

    private function rememberWorkspace(Request $request, User $user): void
    {
        if ($user->company_id) $request->session()->put('company_id', $user->company_id);
        if ($user->creator_id) $request->session()->put('creator_id', $user->creator_id);
    }
}
