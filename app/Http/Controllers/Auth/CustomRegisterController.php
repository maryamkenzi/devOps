<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

class CustomRegisterController
{
    public function showRegistrationForm()
    {
        return view('auth.register_custom');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        return redirect('/index-custom')
            ->with('success', 'Register page works (Demo mode)');
    }
}