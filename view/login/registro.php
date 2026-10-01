<?php
session_start();

require_once __DIR__ . '/../../config/base.php';

if (!empty($_SESSION['usuario'])) {
    header('Location: ' . app_url('index.php?page=home'));
    exit;
}

$error = $_GET['error'] ?? '';
$mensajes = [
    'campos'     => 'Completa todos los campos.',
    'correo'     => 'Ingresa un correo electrónico válido.',
    'clave'      => 'La contraseña debe tener al menos 6 caracteres.',
    'confirmar'  => 'Las contraseñas no coinciden.',
    'existente'  => 'Ya existe una cuenta con ese correo. Inicia sesión.',
    'bd'         => 'Ocurrió un error al crear la cuenta. Inténtalo de nuevo.',
];
$mensaje = $mensajes[$error] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= app_url('assertes/css/login.css') ?>?v=5">
    <link rel="stylesheet" href="<?= app_url('assertes/css/registro.css') ?>?v=5">
    <title>Crear cuenta - Agenda Pro</title>
</head>
<body>
    <main class="login-wrapper">
        <section class="mitad-login">
            <div class="particles" id="particles"></div>
            <div class="mitad-login__content">
                <h2 class="agenda">Agenda Pro</h2>
                <h1 class="Gestiona">REGISTRA TU NEGOCIO</h1>
                <p class="descripcion">
                    Crea tu cuenta gratis y empieza a gestionar tu emprendimiento:
                    registra tu negocio, agrega tus servicios y aparece en el directorio para que tus clientes te encuentren.
                </p>

                <div class="benefit-list">
                    <div class="Container" style="--delay: 0s">
                        <div class="benefit-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div>
                            <h3 class="contaniner">Perfil de negocio</h3>
                            <p class="contaniner">Portada, logo y tus datos de contacto</p>
                        </div>
                    </div>
                    <div class="Container" style="--delay: 0.15s">
                        <div class="benefit-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                        </div>
                        <div>
                            <h3 class="contaniner">Catálogo de servicios</h3>
                            <p class="contaniner">Muestra todo lo que sabes hacer</p>
                        </div>
                    </div>
                    <div class="Container" style="--delay: 0.3s">
                        <div class="benefit-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                        </div>
                        <div>
                            <h3 class="contaniner">Aparece en el directorio</h3>
                            <p class="contaniner">Clientes te contactan por WhatsApp</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="segunda-mitad-login">
            <div class="login-card">
                <div class="login-card__glow"></div>
                <h2 class="h2-mitad">Crear cuenta gratuita</h2>
                <p class="p-mitad">Regístrate para gestionar tu negocio</p>

                <?php if ($mensaje !== ''): ?>
                    <div class="error-message">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <form class="login-form" id="registro-form" method="post" action="../../controller/RegistroController.php">
                    <div class="campo-grupo">
                        <label for="nombre">TU NOMBRE</label>
                        <div class="campo-input-icono">
                            <svg class="campo-icono" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input id="nombre" name="nombre" type="text" placeholder="Ej. María Pérez" required>
                        </div>
                    </div>
                    <div class="campo-grupo">
                        <label for="email">CORREO ELECTRÓNICO</label>
                        <div class="campo-input-icono">
                            <svg class="campo-icono" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input id="email" name="correo" type="email" placeholder="tucorreo@ejemplo.com" required>
                        </div>
                    </div>
                    <div class="campo-grupo">
                        <label for="password">CONTRASEÑA</label>
                        <div class="campo-input-icono">
                            <svg class="campo-icono" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            <input id="password" name="password" type="password" placeholder="Mínimo 6 caracteres" required>
                            <button type="button" class="toggle-password" tabindex="-1" aria-label="Mostrar contraseña">
                                <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="campo-grupo">
                        <label for="password2">REPETIR CONTRASEÑA</label>
                        <div class="campo-input-icono">
                            <svg class="campo-icono" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            <input id="password2" name="password2" type="password" placeholder="Repite la contraseña" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>Crear mi cuenta</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </form>

                <p class="register-text">¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p>
            </div>
        </section>
    </main>

    <script>
    (function(){
        const container = document.getElementById('particles');
        if (container) {
            for (let i = 0; i < 30; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                const size = Math.random() * 4 + 1;
                p.style.cssText = `
                    width:${size}px;height:${size}px;
                    left:${Math.random()*100}%;top:${Math.random()*100}%;
                    animation-duration:${Math.random()*8+6}s;
                    animation-delay:${Math.random()*5}s;
                `;
                container.appendChild(p);
            }
        }

        const toggleBtn = document.querySelector('.toggle-password');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const input = document.getElementById('password');
                const openEye = this.querySelector('.eye-open');
                const closedEye = this.querySelector('.eye-closed');
                if (input.type === 'password') {
                    input.type = 'text';
                    openEye.style.display = 'none';
                    closedEye.style.display = 'block';
                } else {
                    input.type = 'password';
                    openEye.style.display = 'block';
                    closedEye.style.display = 'none';
                }
            });
        }

        const form = document.getElementById('registro-form');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('.btn-submit');
                btn.classList.add('loading');
                btn.disabled = true;
            });
        }
    })();
    </script>
</body>
</html>