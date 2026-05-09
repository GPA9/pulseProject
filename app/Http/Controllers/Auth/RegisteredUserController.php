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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'account_type' => ['required', 'string', 'in:user,musician'],
        ];

        // Add musician validation rules if musician is selected
        if ($request->account_type === 'musician') {
            $rules = array_merge($rules, [
                'stage_name' => ['required', 'string', 'max:255'],
                'genre' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:100'],
                'province' => ['required', 'string', 'max:100'],
                'autonomous_community' => ['required', 'string', 'max:100'],
                'bio' => ['nullable', 'string', 'max:1000'],
            ]);
        }

        $request->validate($rules);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->account_type,
        ]);

        event(new Registered($user));

        // Create musician profile if musician account type
        if ($request->account_type === 'musician') {
            $user->musicianProfile()->create([
                'stage_name' => $request->stage_name,
                'genre' => $request->genre,
                'city' => $request->city,
                'province' => $request->province,
                'autonomous_community' => $request->autonomous_community,
                'bio' => $request->bio,
            ]);
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
