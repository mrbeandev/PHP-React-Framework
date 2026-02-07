<?php

namespace App\Controllers\Api;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Validation\Validator;
use App\Models\Seo;

class SeoController
{
    public function index(Request $request): Response
    {
        return Response::json(Seo::all());
    }

    public function upsert(Request $request): Response
    {
        $data = Validator::validate($request->json(), [
            'path' => 'required|string|path|max:255',
        ]);

        $payload = $request->json();
        $path = trim($data['path']);

        $seo = Seo::updateOrCreate(
            ['path' => $path],
            [
                'path' => $path,
                'title' => $payload['title'] ?? null,
                'description' => $payload['description'] ?? null,
                'keywords' => $payload['keywords'] ?? null,
                'og_image' => $payload['og_image'] ?? null,
            ]
        );

        return Response::json($seo);
    }
}
