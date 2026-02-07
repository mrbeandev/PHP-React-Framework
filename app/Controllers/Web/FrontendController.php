<?php

namespace App\Controllers\Web;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Models\Seo;
use App\Models\Setting;
use RuntimeException;

class FrontendController
{
    public function __construct(private readonly string $distPath)
    {
    }

    public function serve(Request $request): Response
    {
        $uri = $request->path();

        $assetPath = $this->resolveAssetPath($uri);
        if ($assetPath !== null) {
            return Response::file($assetPath);
        }

        $indexPath = $this->distPath . '/index.html';
        if (!is_file($indexPath)) {
            return Response::text("Frontend not built yet. Run 'npm run build'.", 503);
        }

        $html = file_get_contents($indexPath);
        if ($html === false) {
            throw new RuntimeException('Failed to read frontend build output.');
        }

        $seo = $this->resolveSeo($uri);

        $placeholders = [
            '%title%' => $seo['title'],
            '%description%' => $seo['description'],
            '%keywords%' => $seo['keywords'],
            '%og_image%' => $seo['og_image'],
        ];

        return Response::html(str_replace(array_keys($placeholders), array_values($placeholders), $html));
    }

    private function resolveAssetPath(string $uri): ?string
    {
        if ($uri === '/') {
            return null;
        }

        $candidate = $this->distPath . $uri;
        $resolvedPath = realpath($candidate);
        $resolvedDist = realpath($this->distPath);

        if ($resolvedPath === false || $resolvedDist === false || !is_file($resolvedPath)) {
            return null;
        }

        if (!str_starts_with($resolvedPath, $resolvedDist)) {
            return null;
        }

        return $resolvedPath;
    }

    private function resolveSeo(string $uri): array
    {
        $defaults = [
            'title' => 'TaskFlow',
            'description' => 'A high-performance boilerplate for unified PHP and React development.',
            'keywords' => 'php, react, template, eloquent, vite, tailwind',
            'og_image' => '',
        ];

        $isSeoEnabled = Setting::get('enable_dynamic_seo', '1') === '1';
        if (!$isSeoEnabled) {
            return $defaults;
        }

        $seo = Seo::where('path', $uri)->first();
        if (!$seo) {
            return $defaults;
        }

        return [
            'title' => $seo->title ?? $defaults['title'],
            'description' => $seo->description ?? $defaults['description'],
            'keywords' => $seo->keywords ?? $defaults['keywords'],
            'og_image' => $seo->og_image ?? $defaults['og_image'],
        ];
    }
}
