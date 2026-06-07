<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    public function toggleStatus(User $guru): RedirectResponse
    {
        abort_unless($guru->hasRole('guru'), 404);

        $guru->update([
            'is_active' => ! $guru->is_active,
        ]);

        return back()->with('hadirku-toast', [
            'type' => 'success',
            'message' => $guru->is_active
                ? 'Guru berhasil diaktifkan.'
                : 'Guru berhasil dinonaktifkan.',
        ]);
    }

    public function update(Request $request, User $guru): RedirectResponse
    {
        abort_unless($guru->hasRole('guru'), 404);

        $validated = $request->validate([
            'edit_name' => ['required', 'string', 'min:3', 'max:255'],
            'edit_username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($guru->id),
            ],
            'edit_default_class_id' => ['nullable', 'exists:classes,id'],
            'edit_photo' => ['nullable', 'image', 'max:1024'],
            'remove_photo' => ['nullable', 'boolean'],
        ], [
            'edit_name.required' => 'Nama guru wajib diisi.',
            'edit_name.min' => 'Nama guru minimal 3 karakter.',
            'edit_username.required' => 'Nama pengguna wajib diisi.',
            'edit_username.unique' => 'Nama pengguna sudah digunakan.',
            'edit_photo.image' => 'Foto harus berupa gambar.',
            'edit_photo.max' => 'Ukuran foto maksimal 1 MB.',
            'edit_default_class_id.exists' => 'Kelas default tidak valid.',
        ]);

        $photoPath = $request->file('edit_photo')
            ? $request->file('edit_photo')->store('guru-photos', 'public')
            : null;

        $oldPhoto = $guru->photo;
        $removePhoto = (bool) ($validated['remove_photo'] ?? false);

        $guru->update([
            'name' => trim($validated['edit_name']),
            'username' => trim($validated['edit_username']),
            'photo' => $photoPath ?: ($removePhoto ? null : $guru->photo),
            'is_active' => $request->boolean('edit_is_active'),
            'default_class_id' => filled($validated['edit_default_class_id'] ?? null)
                ? $validated['edit_default_class_id']
                : null,
        ]);

        if (($photoPath || $removePhoto) && $oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return back()->with('hadirku-toast', [
            'type' => 'success',
            'message' => 'Data guru berhasil diperbarui.',
        ]);
    }
}
