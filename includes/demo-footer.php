        <footer>
            <p>Built with the Gulp Workflow Template</p>
        </footer>
    </div>

    <!-- Theme Switcher JS (always included) -->
    <?= Asset::js('theme-switcher.iife.js', 'lib') ?>

    <?php if (!empty($includeJs)): ?>
        <?php foreach ($includeJs as $js): ?>
            <?= Asset::js($js, 'lib') ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($customScripts)): ?>
    <script>
        <?= $customScripts ?>
    </script>
    <?php endif; ?>
</body>
</html>
