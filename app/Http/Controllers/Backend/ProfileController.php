<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Pest\Support\View;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('backend.profile.index');
    }

    public function update(Request $request)
    {
$validated = $request->validate([
    'previous_password' => ['required'],
    'new_password' => ['required', 'same:confirm_password'],
    'confirm_password' => ['required'],
        ]);

        $user = Auth::user();

if ($user && Hash::check($validated['previous_password'], $user->password)) {
            $user->update([
        'password' => Hash::make($validated['new_password']),
            ]);

            return back()->with('success', 'Your password has been updated successfully.');
        }

        return "update data";
    }
}

    
