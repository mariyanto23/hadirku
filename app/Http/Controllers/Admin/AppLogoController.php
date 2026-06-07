<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppLogoController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'logo' => [
                'nullable',
                'file',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:2048',
            ],
            'favicon' => [
                'nullable',
                'file',
                'mimes:ico,jpg,jpeg,png,webp',
                'max:1024',
            ],
            'remove_logo' => [
                'nullable',
                'boolean',
            ],
            'remove_favicon' => [
                'nullable',
                'boolean',
            ],
        ]);

        $settings = AttendanceSetting::current();
        $oldLogo = null;
        $oldFavicon = null;
        $logoPath = $settings->logo_path;
        $faviconPath = $settings->favicon_path;

        if ($request->boolean('remove_logo')) {
            $oldLogo = $settings->logo_path;
            $logoPath = null;
        }

        if ($request->boolean('remove_favicon')) {
            $oldFavicon = $settings->favicon_path;
            $faviconPath = null;
        }

        if ($request->hasFile('logo')) {
            $oldLogo = $settings->logo_path;
            $logoPath = $request->file('logo')->store('app-logos', 'public');
        }

        if ($request->hasFile('favicon')) {
            $oldFavicon = $settings->favicon_path;
            $faviconPath = $request->file('favicon')->store('app-favicons', 'public');
        }

        $settings->update([
            'logo_path' => $logoPath,
            'favicon_path' => $faviconPath,
        ]);

        if ($oldLogo && $oldLogo !== $logoPath) {
            Storage::disk('public')->delete($oldLogo);
        }

        if ($oldFavicon && $oldFavicon !== $faviconPath) {
            Storage::disk('public')->delete($oldFavicon);
        }

        return back()->with('status', 'identity-updated');
    }
}
