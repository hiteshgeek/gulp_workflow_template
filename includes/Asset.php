<?php

/**
 * Asset Helper Class
 *
 * Loads revisioned assets from gulp manifest files.
 * Provides methods to get filenames or complete HTML tags.
 */
class Asset
{
    private static array $manifests = [];

    // Manifest configurations
    private static array $config = [
        'lib' => [
            'manifest' => 'dist/rev/manifest.json',
            'css_path' => 'dist/css/',
            'js_path' => 'dist/js/',
        ],
        'assets' => [
            'manifest' => 'assets/rev/manifest.json',
            'css_path' => 'assets/css/',
            'js_path' => 'assets/js/',
        ],
    ];

    /**
     * Load manifest file
     */
    private static function loadManifest(string $type): array
    {
        if (!isset(self::$manifests[$type])) {
            $manifestPath = self::getBasePath() . '/' . self::$config[$type]['manifest'];
            if (file_exists($manifestPath)) {
                self::$manifests[$type] = json_decode(file_get_contents($manifestPath), true) ?? [];
            } else {
                self::$manifests[$type] = [];
            }
        }
        return self::$manifests[$type];
    }

    /**
     * Get base path (override this if needed)
     */
    private static function getBasePath(): string
    {
        return defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__);
    }

    /**
     * Get base URL for assets (override this if needed)
     */
    private static function getBaseUrl(): string
    {
        return defined('BASE_URL') ? BASE_URL : '';
    }

    /**
     * Get revisioned filename
     *
     * @param string $file Original filename (e.g., 'main.css', 'library-name.js')
     * @param string $type 'lib' or 'assets'
     * @return string Revisioned filename or original if not found
     */
    public static function getFile(string $file, string $type = 'assets'): string
    {
        $manifest = self::loadManifest($type);
        return $manifest[$file] ?? $file;
    }

    /**
     * Get full path to revisioned file
     *
     * @param string $file Original filename
     * @param string $type 'lib' or 'assets'
     * @return string Full URL path to the file
     */
    public static function getPath(string $file, string $type = 'assets'): string
    {
        $revisioned = self::getFile($file, $type);
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        $pathKey = ($ext === 'css') ? 'css_path' : 'js_path';
        $basePath = self::$config[$type][$pathKey];

        return self::getBaseUrl() . '/' . $basePath . $revisioned;
    }

    /**
     * Get CSS link tag
     *
     * @param string $file Original filename (e.g., 'main.css')
     * @param string $type 'lib' or 'assets'
     * @param array $attributes Additional attributes
     * @return string HTML link tag
     */
    public static function css(string $file, string $type = 'assets', array $attributes = []): string
    {
        $path = self::getPath($file, $type);
        $attrs = self::buildAttributes(array_merge([
            'rel' => 'stylesheet',
            'href' => $path,
        ], $attributes));

        return "<link {$attrs}>";
    }

    /**
     * Get JS script tag
     *
     * @param string $file Original filename (e.g., 'main.js')
     * @param string $type 'lib' or 'assets'
     * @param array $attributes Additional attributes (e.g., ['defer' => true, 'type' => 'module'])
     * @return string HTML script tag
     */
    public static function js(string $file, string $type = 'assets', array $attributes = []): string
    {
        $path = self::getPath($file, $type);
        $attrs = self::buildAttributes(array_merge([
            'src' => $path,
        ], $attributes));

        return "<script {$attrs}></script>";
    }

    /**
     * Build HTML attributes string
     */
    private static function buildAttributes(array $attributes): string
    {
        $parts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $parts[] = htmlspecialchars($key);
            } elseif ($value !== false && $value !== null) {
                $parts[] = htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            }
        }
        return implode(' ', $parts);
    }

    /**
     * Clear cached manifests (useful for development)
     */
    public static function clearCache(): void
    {
        self::$manifests = [];
    }
}
