<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->email,
                'company_name' => $request->company_name,
                'phone' => $request->phone,
                'inn' => $request->inn,
                'kpp' => $request->kpp,
                'bik' => $request->bik,
                'correspondent_account' => $request->correspondent_account,
                'bank_account' => $request->bank_account,
                'yur_address' => $request->yur_address,
            ]);

            try {
                $user->notify(new WelcomeNotification($request->password));
            } catch (\Exception $e) {
                Log::error('Failed to send welcome notification', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id
                ]);
            }

            event(new Registered($user));
            Auth::login($user);

            return redirect(route('profile.edit', absolute: false));
        } catch (\Exception $e) {
            Log::error('Failed to register user', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
