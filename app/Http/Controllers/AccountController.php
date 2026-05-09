<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Show delete account form.
     */
    public function delete(): View
    {
        return view('auth.delete-account');
    }

    /**
     * Handle account deletion request.
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        // Validate password
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'La contraseña es incorrecta']);
        }

        // Delete user and all related data (cascade delete)
        $user->delete();

        // Logout and redirect to home
        Auth::logout();
        
        return redirect()->route('home')->with('success', 'Tu cuenta ha sido eliminada correctamente. Esperamos verte pronto de nuevo.');
    }
}
