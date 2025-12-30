<?php
/**
 * Demo Page Header
 * Shared header for all demo pages
 */

// Ensure constants are defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('BASE_URL')) {
    define('BASE_URL', '/gulp_workflow_template');
}

require_once BASE_PATH . '/includes/Asset.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Gulp Workflow Template' ?></title>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Prevent flash of wrong theme -->
    <script>
        (function() {
            try {
                var mode = JSON.parse(localStorage.getItem('theme-mode')) || 'system';
                var theme = mode;
                if (mode === 'system') {
                    theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <!-- Theme Switcher CSS (always included for theming) -->
    <?= Asset::css('theme-switcher.css', 'lib') ?>

    <?php if (!empty($includeCss)): ?>
        <?php foreach ($includeCss as $css): ?>
            <?= Asset::css($css, 'lib') ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Shared Demo Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: var(--theme-bg);
            color: var(--theme-text);
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .breadcrumb {
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .breadcrumb a {
            color: var(--theme-primary);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb span {
            opacity: 0.5;
            margin: 0 0.5rem;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        h2 {
            font-size: 1.5rem;
            margin: 2rem 0 1rem;
            color: var(--theme-primary);
        }

        h3 {
            font-size: 1.2rem;
            margin: 1.5rem 0 0.75rem;
        }

        p {
            margin-bottom: 1rem;
        }

        ul, ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }

        li {
            margin-bottom: 0.5rem;
        }

        .subtitle {
            opacity: 0.7;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .demo-box {
            background: rgba(128, 128, 128, 0.1);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
        }

        .demo-section {
            background: rgba(128, 128, 128, 0.05);
            border: 1px solid rgba(128, 128, 128, 0.2);
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .demo-section h3 {
            margin-top: 0;
        }

        .demo-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            margin: 1rem 0;
        }

        .code-block {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            padding: 1rem;
            overflow-x: auto;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875rem;
            margin: 0.5rem 0 1rem;
        }

        [data-theme='dark'] .code-block {
            background: rgba(255, 255, 255, 0.1);
        }

        .code-block code {
            white-space: pre;
        }

        .file-list {
            list-style: none;
            padding-left: 0;
            margin: 1rem 0;
        }

        .file-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(128, 128, 128, 0.2);
        }

        .file-list li:last-child {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .badge--css {
            background: #264de4;
            color: white;
        }

        .badge--js {
            background: #f7df1e;
            color: black;
        }

        .note {
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid var(--theme-primary);
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0 6px 6px 0;
        }

        hr {
            border: none;
            border-top: 1px solid rgba(128, 128, 128, 0.2);
            margin: 2rem 0;
        }

        a {
            color: var(--theme-primary);
        }

        button, .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--theme-primary);
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(128, 128, 128, 0.2);
            color: var(--theme-text);
        }

        .btn-secondary:hover {
            background: rgba(128, 128, 128, 0.3);
        }

        footer {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(128, 128, 128, 0.2);
            opacity: 0.7;
            font-size: 0.875rem;
        }

        /* Component Cards */
        .component-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .component-card {
            background: rgba(128, 128, 128, 0.05);
            border: 1px solid rgba(128, 128, 128, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }

        .component-card:hover {
            border-color: var(--theme-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .component-card h3 {
            margin: 0 0 0.5rem;
            color: var(--theme-primary);
        }

        .component-card p {
            margin: 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .component-card .card-footer {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(128, 128, 128, 0.2);
            font-size: 0.8rem;
            opacity: 0.6;
        }
    </style>

    <?php if (!empty($customStyles)): ?>
    <style>
        <?= $customStyles ?>
    </style>
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <?php if (!empty($showBreadcrumb)): ?>
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>/">Components</a>
            <span>/</span>
            <?= $pageTitle ?? 'Demo' ?>
        </nav>
        <?php endif; ?>
