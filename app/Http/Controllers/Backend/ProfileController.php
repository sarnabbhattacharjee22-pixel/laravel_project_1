<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        return view('backend.profile.index', ['user' => $user]);
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

        return 'update data';
    }

    public function user_info_update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:30',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user),
            ],
            'bio' => ['nullable', 'string', 'max:5000'],
            'cover_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $profileData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'bio' => $validated['bio'] ?? null,
        ];

        if ($request->hasFile('cover_photo')) {
            $imagePath = $request->file('cover_photo')->store('profile-covers', 'public');

            if ($imagePath === false) {
                throw new RuntimeException('Unable to store the uploaded profile cover.');
            }

            $profileData['image'] = $imagePath;
        }

        $user->update($profileData);

        return back()->with('success', 'Your profile information has been updated successfully.');
    }
}
