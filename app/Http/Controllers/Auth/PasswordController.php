<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'different:current_password',
                Password::min(8)->letters()->numbers(),
                'confirmed',
            ],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.different' => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
            'password.min' => 'Kata sandi baru minimal harus berisi 8 karakter.',
            'password.letters' => 'Kata sandi baru harus berisi minimal satu huruf.',
            'password.numbers' => 'Kata sandi baru harus berisi minimal satu angka.',
            'password.confirmed' => 'Konfirmasi kata sandi baru belum sama.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('hadirku-toast', [
            'type' => 'success',
            'message' => 'Kata sandi berhasil diperbarui.',
        ]);
    }
}
