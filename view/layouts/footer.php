<!-- Inclusión de Scripts JS generales -->
    <?php if (!empty($mostrarMainJs ?? true) && file_exists(__DIR__ . '/../../assertes/js/main.js')): ?>
    <script src="<?= app_url('assertes/js/main.js') ?>"></script>
    <?php endif; ?>
</body>
</html>