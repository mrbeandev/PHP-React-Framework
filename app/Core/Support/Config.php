<?php

namespace App\Core\Support;

class Config
{
    private static string $basePath;
    private static array $loaded = [];

    public static function setBasePath(string $basePath): void
    {
        self::$basePath = rtrim($basePath, '/');
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $file = array_shift($segments);

        if ($file === null || $file === '') {
            return $default;
        }

        if (!array_key_exists($file, self::$loaded)) {
            self::$loaded[$file] = self::loadFile($file);
        }

        $value = self::$loaded[$file];

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    private static function loadFile(string $file): array
    {
        $path = self::$basePath . '/config/' . $file . '.php';

        if (!is_file($path)) {
            return [];
        }

        $config = require $path;

        return is_array($config) ? $config : [];
    }
}
