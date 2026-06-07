<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{
    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $validated = $request->validate([
            'class_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes', 'name')->ignore($class->id),
            ],
        ], [
            'class_name.required' => 'Nama kelas wajib diisi.',
            'class_name.max' => 'Nama kelas maksimal 255 karakter.',
            'class_name.unique' => 'Nama kelas sudah digunakan.',
        ]);

        $class->update([
            'name' => trim($validated['class_name']),
        ]);

        return back()->with('hadirku-toast', [
            'type' => 'success',
            'message' => 'Kelas berhasil diperbarui.',
        ]);
    }
}
