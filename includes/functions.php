<?php

/**
 * Asset Manager Class
 * Handles versioned asset loading with support for both URL and HTML tag output
 */
class Asset
{
    private static $manifests = array('lib' => null, 'assets' => null, 'common' => null);
    private static $basePath = null;

    private $filename;
    private $source; // 'lib', 'assets', or 'common'
    private $type; // 'js' or 'css'
    private $variant = null; // 'nomodule' for IIFE
    private $attributes = array();

    /**
     * Private constructor - use static factory methods
     */
    private function __construct($filename, $source)
    {
        $this->filename = $filename;
        $this->source = $source;
        $this->type = $this->detectType($filename);
    }

    /**
     * Factory method for application assets (assets/ folder)
     */
    public static function file($filename)
    {
        return new self($filename, 'assets');
    }

    /**
     * Factory method for library files (dist/ folder)
     */
    public static function lib($filename)
    {
        return new self($filename, 'lib');
    }

    /**
     * Factory method for common files (dist/common/ folder)
     */
    public static function common($filename)
    {
        return new self($filename, 'common');
    }

    /**
     * Set variant (e.g., 'nomodule' for IIFE build)
     */
    public function variant($variant)
    {
        $this->variant = $variant;
        return $this;
    }

    /**
     * Shortcut for nomodule variant (adds nomodule attribute)
     * Use this when loading IIFE as fallback alongside ESM module
     */
    public function nomodule()
    {
        $this->variant = 'nomodule';
        return $this;
    }

    /**
     * Shortcut for IIFE variant (no nomodule attribute)
     * Use this when loading IIFE as the primary script
     */
    public function iife()
    {
        $this->variant = 'iife';
        return $this;
    }

    /**
     * Add HTML attribute(s)
     * @param string|array $key Attribute name or array of attributes
     * @param string|null $value Attribute value (optional for boolean attributes)
     */
    public function attr($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->attributes[$k] = $v;
            }
        } else {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    /**
     * Add defer attribute (JS only)
     */
    public function defer()
    {
        $this->attributes['defer'] = true;
        return $this;
    }

    /**
     * Add async attribute (JS only)
     */
    public function async()
    {
        $this->attributes['async'] = true;
        return $this;
    }

    /**
     * Add type="module" attribute (JS only)
     */
    public function module()
    {
        $this->attributes['type'] = 'module';
        return $this;
    }

    /**
     * Add media attribute (CSS only)
     */
    public function media($media)
    {
        $this->attributes['media'] = $media;
        return $this;
    }

    /**
     * Add id attribute
     */
    public function id($id)
    {
        $this->attributes['id'] = $id;
        return $this;
    }

    /**
     * Add crossorigin attribute
     */
    public function crossorigin($value = 'anonymous')
    {
        $this->attributes['crossorigin'] = $value;
        return $this;
    }

    /**
     * Add integrity attribute for SRI
     */
    public function integrity($hash)
    {
        $this->attributes['integrity'] = $hash;
        return $this;
    }

    /**
     * Get the versioned URL only
     */
    public function url()
    {
        $basePath = self::getBasePath();
        $manifestKey = $this->filename;

        // Handle nomodule/iife variant - both use the .iife.js file
        if ($this->type === 'js' && in_array($this->variant, ['nomodule', 'iife'])) {
            $manifestKey = preg_replace('/\.js$/', '.iife.js', $manifestKey);
        }

        switch ($this->source) {
            case 'lib':
                $folder = 'dist';
                break;
            case 'common':
                $folder = 'dist/common';
                break;
            default:
                $folder = 'assets';
        }
        $baseUrl = $basePath . '/' . $folder . '/' . $this->type . '/';
        $manifest = self::getManifest($this->source);

        if (isset($manifest[$manifestKey])) {
            return $baseUrl . $manifest[$manifestKey];
        }

        error_log("[Asset] File not found in manifest: {$this->filename} (key: $manifestKey)");
        return $baseUrl . $manifestKey;
    }

    /**
     * Get the complete HTML tag
     */
    public function tag()
    {
        $url = $this->url();

        if ($this->type === 'js') {
            return $this->buildScriptTag($url);
        } elseif ($this->type === 'css') {
            return $this->buildLinkTag($url);
        }

        return '';
    }

    /**
     * Output the HTML tag directly (echo)
     */
    public function render()
    {
        echo $this->tag();
    }

    /**
     * Magic method for string conversion - returns URL
     */
    public function __toString()
    {
        return $this->url();
    }

    /**
     * Build script tag with attributes
     */
    private function buildScriptTag($url)
    {
        $attrs = array('src' => $url);

        // Add nomodule attribute if variant is set
        if ($this->variant === 'nomodule') {
            $attrs['nomodule'] = true;
        }

        // Merge custom attributes
        $attrs = array_merge($attrs, $this->attributes);

        return '<script' . $this->buildAttributeString($attrs) . '></script>';
    }

    /**
     * Build link tag with attributes
     */
    private function buildLinkTag($url)
    {
        $attrs = array(
            'rel' => 'stylesheet',
            'href' => $url
        );

        // Merge custom attributes
        $attrs = array_merge($attrs, $this->attributes);

        return '<link' . $this->buildAttributeString($attrs) . '>';
    }

    /**
     * Build attribute string from array
     */
    private function buildAttributeString($attrs)
    {
        $parts = array();
        foreach ($attrs as $key => $value) {
            if ($value === true) {
                // Boolean attribute
                $parts[] = htmlspecialchars($key);
            } elseif ($value !== false && $value !== null) {
                // Regular attribute
                $parts[] = htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
            }
        }
        return $parts ? ' ' . implode(' ', $parts) : '';
    }

    /**
     * Detect asset type from filename
     */
    private function detectType($filename)
    {
        if (substr($filename, -3) === '.js') {
            return 'js';
        } elseif (substr($filename, -4) === '.css') {
            return 'css';
        }
        return null;
    }

    /**
     * Get base path (cached)
     */
    private static function getBasePath()
    {
        if (self::$basePath === null) {
            self::$basePath = get_base_path();
        }
        return self::$basePath;
    }

    /**
     * Get manifest data (cached)
     */
    private static function getManifest($source)
    {
        if (self::$manifests[$source] === null) {
            switch ($source) {
                case 'lib':
                    $manifestFile = dirname(__FILE__) . '/../dist/rev/manifest.json';
                    break;
                case 'common':
                    $manifestFile = dirname(__FILE__) . '/../dist/common/rev/manifest.json';
                    break;
                default:
                    $manifestFile = dirname(__FILE__) . '/../assets/rev/manifest.json';
            }

            if (!file_exists($manifestFile)) {
                error_log("[Asset] Manifest file not found: $manifestFile");
                self::$manifests[$source] = array();
            } else {
                $json = file_get_contents($manifestFile);
                $decoded = json_decode($json, true);
                self::$manifests[$source] = $decoded ? $decoded : array();
            }
        }

        return self::$manifests[$source];
    }
}

/**
 * Get the base URL path for the project
 */
function get_base_path()
{
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $parts = explode('/', trim($scriptDir, '/'));
    $subDirs = array('api', 'includes', 'usage', 'graphs', 'docs', 'setup');

    while (!empty($parts) && in_array(end($parts), $subDirs)) {
        array_pop($parts);
    }

    $path = implode('/', $parts);
    return $path ? '/' . $path : '';
}

// =============================================================================
// HELPER FUNCTIONS (shortcuts for common use cases)
// =============================================================================

/**
 * Get asset URL (shortcut)
 */
function asset($filename, $variant = null)
{
    $asset = Asset::file($filename);
    if ($variant) {
        $asset->variant($variant);
    }
    return $asset->url();
}

/**
 * Get library URL (shortcut)
 */
function lib($filename, $variant = null)
{
    $asset = Asset::lib($filename);
    if ($variant) {
        $asset->variant($variant);
    }
    return $asset->url();
}

/**
 * Get common URL (shortcut)
 */
function common($filename, $variant = null)
{
    $asset = Asset::common($filename);
    if ($variant) {
        $asset->variant($variant);
    }
    return $asset->url();
}

/**
 * Output favicon link tags
 * Include this in the <head> of every page
 */
function favicon()
{
    $basePath = get_base_path();
    echo '<link rel="icon" href="' . $basePath . '/favicon.ico" sizes="32x32">' . "\n";
    echo '    <link rel="icon" type="image/svg+xml" href="' . $basePath . '/assets/images/favicon.svg">' . "\n";
    echo '    <link rel="icon" type="image/png" sizes="32x32" href="' . $basePath . '/assets/images/favicon-32.png">' . "\n";
    echo '    <link rel="apple-touch-icon" href="' . $basePath . '/assets/images/favicon-180.png">';
}
