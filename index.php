<?php

// Define base path and URL (adjust for your setup)
define('BASE_PATH', __DIR__);
define('BASE_URL', '');  // e.g., '/my-project' or '' for root

require_once __DIR__ . '/includes/Asset.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gulp Workflow Template</title>

    <!-- Library CSS (from dist/) -->
    <?= Asset::css('library-name.css', 'lib') ?>

    <!-- Assets CSS (from assets/) -->
    <?= Asset::css('main.css', 'assets') ?>

</head>
<body>

    <h1>Gulp Workflow Template</h1>
    <p>This is a minimal example showing how to load revisioned assets.</p>

    <h2>Asset Class Usage</h2>
    <pre>
// Get just the revisioned filename
Asset::getFile('main.css', 'assets')  => "<?= Asset::getFile('main.css', 'assets') ?>"

// Get full path
Asset::getPath('main.css', 'assets')  => "<?= Asset::getPath('main.css', 'assets') ?>"

// Get CSS tag
Asset::css('main.css', 'assets')

// Get CSS tag with attributes
Asset::css('main.css', 'assets', ['media' => 'screen'])

// Get JS tag
Asset::js('main.js', 'assets')

// Get JS tag with attributes
Asset::js('main.js', 'assets', ['defer' => true])
Asset::js('main.js', 'assets', ['type' => 'module'])
Asset::js('main.iife.js', 'assets', ['async' => true])

// Library assets (from dist/)
Asset::css('library-name.css', 'lib')
Asset::js('library-name.js', 'lib', ['type' => 'module'])
Asset::js('library-name.iife.js', 'lib')
    </pre>

    <!-- Library JS (ESM module) -->
    <?= Asset::js('library-name.js', 'lib', ['type' => 'module']) ?>

    <!-- Assets JS (IIFE for browser) -->
    <?= Asset::js('main.iife.js', 'assets') ?>

</body>
</html>
