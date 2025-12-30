<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'picture' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');

            $filename = (string) \Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('public/pictures/items', $filename);

            // Só a URL relativa que você quer salvar no banco
            $relativeUrl = 'pictures/items/' . $filename;

            // Exemplo: salvar no model (supondo que você tenha um model Image)
            \App\Models\Part::create([
                'picture' => $relativeUrl,
                // outros campos que precisar
            ]);

            return response()->json([
                'message' => 'Upload realizado com sucesso',
                'url' => $relativeUrl,
            ], 200);
        }

        return response()->json(['error' => 'Nenhum arquivo enviado'], 400);
    }
}
