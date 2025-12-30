<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageService
{

    public function handleUpload(Request $request, string $name = 'file', string $path = '', string $current = null)
    {
        if ($request->hasFile($name)) {
            $extension = $request->file($name)->getClientOriginalExtension();

            $this->handleDestroy($current);

            $filename = uniqid() . '_' . time() . '.' . $extension;

            return $request->file($name)->storeAs($path, $filename, 'public');
        }

        return null;
    }

    public function handleDestroy(string $path = null): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
