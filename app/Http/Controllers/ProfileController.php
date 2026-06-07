<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->load('student');
        $validated = $request->validated();
        $oldPhotos = [];

        unset($validated['photo'], $validated['remove_photo']);

        if ($request->boolean('remove_photo')) {
            $oldPhotos[] = $user->photo;
            $oldPhotos[] = $user->student?->photo;
            $validated['photo'] = null;
        }

        if ($request->hasFile('photo')) {
            $oldPhotos[] = $user->photo;
            $oldPhotos[] = $user->student?->photo;
            $validated['photo'] = $request->file('photo')->store('profile-photos', 'public');
        }

        DB::transaction(function () use ($user, $validated) {
            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            if ($user->hasRole('siswa') && $user->student && array_key_exists('photo', $validated)) {
                $user->student->update([
                    'photo' => $validated['photo'],
                ]);
            }
        });

        foreach (array_unique(array_filter($oldPhotos)) as $oldPhoto) {
            if ($oldPhoto !== $user->photo && $oldPhoto !== $user->student?->photo) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }

        return Redirect::route('profile.edit')->with('hadirku-toast', [
            'type' => 'success',
            'message' => 'Profil berhasil diperbarui.',
        ]);
    }

}
