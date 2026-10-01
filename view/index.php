<?php
require_once __DIR__ . '/../config/base.php';

if (!isset($negocios)) {
    require_once __DIR__ . '/../model/NegocioModel.php';
    $negocios = (new NegocioModel())->obtenerTodos();
}

require_once __DIR__ . '/../model/ResenaModel.php';
$promediosNegocios = (new ResenaModel())->promediosPorNegocios(array_column($negocios, 'id'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio de negocios</title>
    <link rel="stylesheet" href="<?= app_url('assertes/css/directorio-publico.css') ?>?v=6">
</head>
<body>
    <header class="directorio-hero">
        <div class="hero-particles" id="hero-particles"></div>
        <div class="hero-grid-overlay"></div>

        <nav class="directorio-nav">
            <a class="marca" href="<?= app_url() ?>">
                <span class="marca-icono">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                </span>
                AgendaPro
            </a>
            <div class="nav-acciones">
                <a class="acceso acceso-secundario" href="<?= app_url('view/login/registro.php') ?>">
                    Registrar mi negocio
                </a>
                <a class="acceso" href="<?= app_url('view/login/login.php') ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Ingresar
                </a>
            </div>
        </nav>
        <div class="hero-contenido">
            <p class="eyebrow">Directorio local</p>
            <h1>Encuentra negocios que<br><span class="texto-gradiente">hacen la diferencia.</span></h1>
            <p class="hero-texto">Descubre servicios, conoce sus especialidades y contacta directamente con el negocio que necesitas.</p>
            <label class="busqueda-publica">
                <svg class="busqueda-icono" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="search" id="buscar-negocios" placeholder="Buscar por nombre, categoría o ubicación" aria-label="Buscar negocios">
            </label>
        </div>
    </header>

    <main class="directorio-contenido">
        <div class="seccion-encabezado">
            <div>
                <p class="eyebrow">Explora</p>
                <h2>Negocios destacados</h2>
            </div>
            <span class="contador">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                <?= count($negocios) ?> registrados
            </span>
        </div>

        <?php if (empty($negocios)): ?>
            <section class="estado-vacio">
                <div class="vacio-icono">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <h2>Aún no hay negocios registrados</h2>
                <p>Pronto encontrarás nuevos negocios en este directorio.</p>
            </section>
        <?php else: ?>
            <section class="negocios-grid" id="negocios-grid">
                <?php foreach ($negocios as $i => $negocio): ?>
                    <?php
                    $nombre = $negocio['nombre'] ?? 'Negocio sin nombre';
                    $categoria = $negocio['categoria'] ?? 'Servicios';
                    $ubicacion = $negocio['ubicacion'] ?? 'Ubicación no registrada';
                    $contacto = $negocio['whatsapp'] ?: ($negocio['correo'] ?? 'Contacto no registrado');

                    // Normaliza el número de WhatsApp: solo dígitos y agrega código de país si es nacional (Perú: 51)
                    $wspLimpio = preg_replace('/\D+/', '', $negocio['whatsapp'] ?? '');
                    if ($wspLimpio !== '') {
                        if (strlen($wspLimpio) === 9 && $wspLimpio[0] === '9') {
                            $wspLimpio = '51' . $wspLimpio;
                        }
                    }
                    $tieneWsp = $wspLimpio !== '';
                    $urlContacto = $tieneWsp
                        ? url_whatsapp($wspLimpio, mensaje_contacto_directorio($nombre))
                        : 'mailto:' . htmlspecialchars($negocio['correo'] ?? '');

                    $statsResenas = $promediosNegocios[(int) ($negocio['id'] ?? 0)] ?? null;
                    if ($statsResenas) {
                        $renderEstrellas = str_repeat('★', (int) round((float) $statsResenas['promedio'])) . str_repeat('☆', 5 - (int) round((float) $statsResenas['promedio']));
                    }
                    ?>
                    <article class="negocio-card" style="--i: <?= $i ?>" data-search="<?= htmlspecialchars(strtolower($nombre . ' ' . $categoria . ' ' . $ubicacion)) ?>">
                        <a class="card-portada-link" href="<?= app_url('detalle.php?id=' . (int) ($negocio['id'] ?? 0)) ?>" aria-label="Ver portada de <?= htmlspecialchars($nombre) ?>"></a>
                        <div class="negocio-portada">
                            <div class="portada-decoracion"></div>
                            <?php if (!empty($negocio['logo'])): ?>
                                <img src="<?= htmlspecialchars(app_url($negocio['logo'])) ?>" alt="Logo de <?= htmlspecialchars($nombre) ?>">
                            <?php else: ?>
                                <span class="logo-inicial"><?= htmlspecialchars(strtoupper(substr($nombre, 0, 1))) ?></span>
                            <?php endif; ?>
                            <span class="etiqueta-categoria"><?= htmlspecialchars($categoria) ?></span>
                        </div>
                        <div class="negocio-detalle">
                            <h3><?= htmlspecialchars($nombre) ?></h3>
                            <?php if ($statsResenas): ?>
                                <div class="negocio-rating" aria-label="<?= htmlspecialchars($statsResenas['promedio']) ?> de 5, <?= (int) $statsResenas['total'] ?> reseñas">
                                    <span class="negocio-rating__estrellas" aria-hidden="true"><?= $renderEstrellas ?></span>
                                    <span class="negocio-rating__info">
                                        <?= htmlspecialchars($statsResenas['promedio']) ?>
                                        <span class="negocio-rating__punto">·</span>
                                        <?= (int) $statsResenas['total'] ?> reseña<?= (int) $statsResenas['total'] !== 1 ? 's' : '' ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <p><?= htmlspecialchars($negocio['descripcion'] ?: 'Conoce los servicios de este negocio.') ?></p>
                            <div class="negocio-meta">
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <?= htmlspecialchars($ubicacion) ?>
                                </span>
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                                    <?= htmlspecialchars($contacto) ?>
                                </span>
                            </div>
                            <a class="btn-contactar <?= $tieneWsp ? 'btn-whatsapp' : 'btn-correo' ?>" href="<?= $urlContacto ?>" target="_blank" rel="noopener">
                                <?php if ($tieneWsp): ?>
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    WhatsApp
                                <?php else: ?>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    Enviar correo
                                <?php endif; ?>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer class="directorio-footer">
        <p>AgendaPro &copy; <?= date('Y') ?> &mdash; Directorio de Emprendimientos</p>
    </footer>

    <script>
        (function(){
            const particles = document.getElementById('hero-particles');
            if (particles) {
                for (let i = 0; i < 40; i++) {
                    const p = document.createElement('div');
                    p.className = 'hero-particle';
                    const size = Math.random() * 3 + 1;
                    p.style.cssText = `
                        width:${size}px;height:${size}px;
                        left:${Math.random()*100}%;top:${Math.random()*100}%;
                        animation-duration:${Math.random()*12+8}s;
                        animation-delay:${Math.random()*6}s;
                    `;
                    particles.appendChild(p);
                }
            }

            const buscador = document.querySelector('#buscar-negocios');
            const tarjetas = document.querySelectorAll('.negocio-card');

            buscador?.addEventListener('input', (evento) => {
                const termino = evento.target.value.trim().toLowerCase();
                let visibleCount = 0;
                tarjetas.forEach((tarjeta) => {
                    const visible = termino === '' || tarjeta.dataset.search.includes(termino);
                    tarjeta.hidden = !visible;
                    if (visible) visibleCount++;
                });
                const contador = document.querySelector('.contador');
                if (contador && termino !== '') {
                    contador.textContent = visibleCount + ' encontrado' + (visibleCount !== 1 ? 's' : '');
                }
            });
        })();
    </script>
</body>
</html>