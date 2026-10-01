<?php
if (!defined('APP_LAYOUT')) {
    require_once __DIR__ . '/../../config/base.php';
    header('Location: ' . app_url('index.php?page=cuenta'));
    exit;
}

require_once __DIR__ . '/../../model/Usuario.php';
$usuario = (new Usuario())->obtenerPorId((int) $_SESSION['id_usuario']);
$nombreU = $usuario['nombre'] ?? ($_SESSION['usuario_nombre'] ?? '');
$correoU = $usuario['correo'] ?? ($_SESSION['correo_usuario'] ?? '');
?>
<section class="cuenta-page">
    <?php if (($_GET['cuenta'] ?? '') === 'ok'): ?>
        <p class="sistema-mensaje sistema-exito">Tus datos se actualizaron correctamente.</p>
    <?php elseif (($_GET['cuenta'] ?? '') === 'error'): ?>
        <p class="sistema-mensaje sistema-error">No se pudieron actualizar los datos. Verifica los campos.</p>
    <?php elseif (($_GET['cuenta'] ?? '') === 'correo_en_uso'): ?>
        <p class="sistema-mensaje sistema-error">Ese correo ya está registrado en otra cuenta.</p>
    <?php elseif (($_GET['clave'] ?? '') === 'ok'): ?>
        <p class="sistema-mensaje sistema-exito">Tu contraseña se cambió correctamente.</p>
    <?php elseif (($_GET['clave'] ?? '') === 'error'): ?>
        <p class="sistema-mensaje sistema-error">No se pudo cambiar la contraseña. Verifica la contraseña actual y que ambas coincidan.</p>
    <?php endif; ?>

    <header class="cuenta-header">
        <div>
            <p class="header-eyebrow">Configuración personal</p>
            <h1>Mi Cuenta</h1>
            <p>Administra los datos de tu cuenta y tu contraseña de acceso al panel.</p>
        </div>
        <span class="cuenta-avatar-grande"><?= strtoupper(substr($nombreU, 0, 2)) ?></span>
    </header>

    <div class="cuenta-grid">
        <form class="panel-card cuenta-form" method="post" action="controller/CuentaController.php">
            <input type="hidden" name="action" value="datos">
            <div class="panel-card__header">
                <h2>Datos de la cuenta</h2>
                <p>Nombre y correo con el que inicias sesión</p>
            </div>
            <div class="campo-form">
                <label for="cuenta-nombre">Nombre completo</label>
                <input type="text" id="cuenta-nombre" name="nombre" required maxlength="120" value="<?= htmlspecialchars($nombreU) ?>">
            </div>
            <div class="campo-form">
                <label for="cuenta-correo">Correo electrónico</label>
                <input type="email" id="cuenta-correo" name="correo" required maxlength="150" value="<?= htmlspecialchars($correoU) ?>">
            </div>
            <button class="btn-guardar" type="submit">Guardar cambios</button>
        </form>

        <form class="panel-card cuenta-form" method="post" action="controller/CuentaController.php">
            <input type="hidden" name="action" value="password">
            <div class="panel-card__header">
                <h2>Cambiar contraseña</h2>
                <p>Usa la contraseña actual para confirmar el cambio</p>
            </div>
            <div class="campo-form">
                <label for="cuenta-password-actual">Contraseña actual</label>
                <input type="password" id="cuenta-password-actual" name="password_actual" required>
            </div>
            <div class="campo-form">
                <label for="cuenta-password-nueva">Nueva contraseña</label>
                <input type="password" id="cuenta-password-nueva" name="password_nueva" required minlength="6">
            </div>
            <div class="campo-form">
                <label for="cuenta-password-confirma">Confirmar nueva contraseña</label>
                <input type="password" id="cuenta-password-confirma" name="password_confirma" required minlength="6">
            </div>
            <button class="btn-guardar" type="submit">Cambiar contraseña</button>
        </form>
    </div>
</section>