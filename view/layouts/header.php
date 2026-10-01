<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'AgendaPro - Panel' ?></title>
    <?php if (($page ?? 'home') === 'perfil'): ?>
        <link rel="stylesheet" href="<?= app_url('assertes/css/perfil-principal.css') ?>?v=5">
    <?php elseif (($page ?? 'home') === 'gestion_catalogo'): ?>
        <link rel="stylesheet" href="<?= app_url('assertes/css/gestion-catalogo.css') ?>?v=4">
    <?php elseif (($page ?? 'home') === 'cuenta'): ?>
        <link rel="stylesheet" href="<?= app_url('assertes/css/cuenta.css') ?>?v=1">
    <?php else: ?>
        <link rel="stylesheet" href="<?= app_url('assertes/css/home.css') ?>?v=5">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= app_url('assertes/css/sidebar.css') ?>?v=4">
    <link rel="stylesheet" href="<?= app_url('assertes/css/panel-top.css') ?>?v=3">
</head>
<body>
<?php if (!empty($misNegocios) || isset($_SESSION['usuario'])): ?>
<div class="panel-top">
    <div class="panel-top__interno">
        <span class="panel-top__etiqueta">Gestión de negocios</span>
        <div class="panel-top__selectores">
            <?php if (!empty($misNegocios)): ?>
                <select id="selector-negocio" data-pagina="index.php?page=<?= htmlspecialchars($page ?? 'home') ?>">
                    <?php foreach ($misNegocios as $n): ?>
                        <option value="<?= (int) $n['id'] ?>" <?= isset($negocio['id']) && (int) $negocio['id'] === (int) $n['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($n['nombre']) ?> — <?= htmlspecialchars($n['categoria']) ?><?= ($n['estado'] ?? null) === 'inactivo' ? ' (pausado)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <a class="panel-top__nuevo" href="index.php?page=perfil&negocio_id=0">+ Agregar negocio</a>
        </div>
    </div>
</div>
<script>
(function() {
    var sel = document.getElementById('selector-negocio');
    if (sel) {
        sel.addEventListener('change', function() {
            window.location.href = sel.dataset.pagina + '&negocio_id=' + sel.value;
        });
    }
})();
</script>
<?php endif; ?>