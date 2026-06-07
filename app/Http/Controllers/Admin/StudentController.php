<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->load('user');

        $validated = $request->validate([
            'edit_name' => ['required', 'string', 'min:3', 'max:255'],
            'edit_nis' => [
                'required',
                'string',
                'max:255',
                Rule::unique('students', 'nis')->ignore($student->id),
                Rule::unique('users', 'username')->ignore($student->user_id),
            ],
            'edit_class_id' => ['required', 'exists:classes,id'],
            'edit_gender' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'edit_photo' => ['nullable', 'image', 'max:1024'],
            'remove_photo' => ['nullable', 'boolean'],
        ], [
            'edit_name.required' => 'Nama siswa wajib diisi.',
            'edit_name.min' => 'Nama siswa minimal 3 karakter.',
            'edit_nis.required' => 'NIS wajib diisi.',
            'edit_nis.unique' => 'NIS sudah digunakan.',
            'edit_class_id.required' => 'Kelas wajib dipilih.',
            'edit_class_id.exists' => 'Kelas tidak valid.',
            'edit_gender.required' => 'Jenis kelamin wajib dipilih.',
            'edit_gender.in' => 'Jenis kelamin tidak valid.',
            'edit_photo.image' => 'Foto harus berupa gambar.',
            'edit_photo.max' => 'Ukuran foto maksimal 1 MB.',
        ]);

        $photoPath = $request->file('edit_photo')
            ? $request->file('edit_photo')->store('student-photos', 'public')
            : null;

        $oldPhoto = $student->photo;
        $removePhoto = (bool) ($validated['remove_photo'] ?? false);

        DB::transaction(function () use ($student, $validated, $photoPath, $removePhoto) {
            if ($student->user) {
                $student->user->update([
                    'name' => trim($validated['edit_name']),
                    'username' => trim($validated['edit_nis']),
                ]);
            }

            $student->update([
                'class_id' => $validated['edit_class_id'],
                'nis' => trim($validated['edit_nis']),
                'gender' => $validated['edit_gender'],
                'photo' => $photoPath ?: ($removePhoto ? null : $student->photo),
            ]);
        });

        if (($photoPath || $removePhoto) && $oldPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return back()->with('hadirku-toast', [
            'type' => 'success',
            'message' => 'Data siswa berhasil diperbarui.',
        ]);
    }
}
