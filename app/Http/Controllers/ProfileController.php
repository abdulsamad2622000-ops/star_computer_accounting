<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'             => 'required',
            'current_password' => 'required',
            'new_password'     => 'nullable|min:6|confirmed',
        ]);

        $user = auth()->user();

        // Current password check
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => '❌ Current password galat hai!'
            ]);
        }

        // Name update
        $user->name = $request->name;

        // Password update
        if ($request->new_password) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', '✅ Profile update ho gaya!');
    }
}