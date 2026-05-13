<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::where('role', 'staff')->latest()->get();
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'username' => 'required|unique:users|alpha_dash',
            'email'    => 'nullable|email|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email ?? null,
            'password' => Hash::make($request->password),
            'role'     => 'staff',
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Staff member add ho gaya!');
    }

    public function edit(User $staff)
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        $request->validate([
            'name'     => 'required',
            'username' => 'required|unique:users,username,'.$staff->id,
            'email'    => 'nullable|email|unique:users,email,'.$staff->id,
        ]);

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
        ];

        if ($request->email) {
            $data['email'] = $request->email;
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return redirect()->route('staff.index')
            ->with('success', 'Staff update ho gaya!');
    }

    public function destroy(User $staff)
    {
        $staff->delete();
        return redirect()->route('staff.index')
            ->with('success', 'Staff delete ho gaya!');
    }
}