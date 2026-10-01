<?php
session_start();

require_once __DIR__ . '/../../controller/AuthController.php';
require_once __DIR__ . '/../../config/base.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['correo'] ?? ''); 
    $password = trim($_POST['password'] ?? ''); 
    
    if ($email === '' || $password === '') {
        $mensaje = 'Complete todos los campos';
    } else {
        $auth = new AuthController();
        if ($auth->login($email, $password)) {
            header('Location: ../../index.php?page=home');
            exit;
        }
        
        $mensaje = 'Correo o clave incorrectos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= app_url('assertes/css/login.css') ?>?v=5">
    <title>Login - Agenda Pro</title>
</head>
<body>
    <main class="login-wrapper">
        <section class="mitad-login">
            <div class="particles" id="particles"></div>
            <div class="mitad-login__content">
                <h2 class="agenda">Agenda Pro</h2>
                <h1 class="Gestiona">GESTIONA TU NEGOCIO</h1>
                <p class="descripcion">
                    AgendaPro te ayuda a organizar citas, clientes, catálogo y ventas en una sola plataforma pensada para crecer tu negocio.
                </p>

                <div class="benefit-list">
                    <div class="Container" style="--delay: 0s">
                        <div class="benefit-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/></svg>
                        </div>
                        <div>
                            <h3 class="contaniner">Agenda de citas inteligentes</h3>
                            <p class="contaniner">Recordatorios automáticos para clientes</p>
                        </div>
                    </div>
                    <div class="Container" style="--delay: 0.15s">
                        <div class="benefit-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                        </div>
                        <div>
                            <h3 class="contaniner">Catálogo digital completo</h3>
                            <p class="contaniner">Servicios, productos y precios</p>
                        </div>
                    </div>
                    <div class="Container" style="--delay: 0.3s">
                        <div class="benefit-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        </div>
                        <div>
                            <h3 class="contaniner">Reportes y estadísticas en tiempo real</h3>
                            <p class="contaniner">Toma decisiones con datos</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="segunda-mitad-login">
            <div class="login-card">
                <div class="login-card__glow"></div>
                <h2 class="h2-mitad">Bienvenido de vuelta</h2>
                <p class="p-mitad">Inicia sesión para continuar</p>

                <?php if ($mensaje !== ''): ?>
                    <div class="error-message">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>

                <form class="login-form" id="login-form" method="post" action="">
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
                            <input id="password" name="password" type="password" placeholder="Ingresa tu contraseña" required>
                            <button type="button" class="toggle-password" tabindex="-1" aria-label="Mostrar contraseña">
                                <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>Iniciar sesión</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </form>

                <p class="register-text">¿No tienes cuenta? <a href="registro.php" class="registrar">Registrar gratis</a></p>
            </div>
        </section>
    </main>

    <script>
    (function(){
        const container = document.getElementById('particles');
        if (!container) return;
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

        document.querySelectorAll('.Container').forEach((card, i) => {
            card.style.animationDelay = `${i * 0.15}s`;
        });

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

        const form = document.getElementById('login-form');
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
