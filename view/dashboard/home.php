<?php
if (!defined('APP_LAYOUT')) {
    require_once __DIR__ . '/../../config/base.php';
    header('Location: ' . app_url('index.php?page=home'));
    exit;
}
$totalServicios = count($servicios ?? []);
$disponibles = count(array_filter($servicios ?? [], static fn ($s) => $s['estado'] === 'disponible'));
$misNegocios = $misNegocios ?? [];
$totalNegocios = count($misNegocios);
$saludo = (int) date('H') < 12 ? 'Buenos días' : ((int) date('H') < 19 ? 'Buenas tardes' : 'Buenas noches');
$nombreAdmin = $_SESSION['usuario_nombre'] ?? 'Administrador';

$progresoPerfil = !empty($negocio) ? (!empty($negocio['nombre']) && !empty($negocio['categoria']) ? 70 : 40) : 0;
$progresoServicios = $totalServicios > 0 ? min(100, round(($totalServicios / 6) * 100)) : 0;
$progresoDisponibilidad = $totalServicios > 0 ? round(($disponibles / $totalServicios) * 100) : 0;

require_once __DIR__ . '/../../model/ResenaModel.php';
$resenaModel = new ResenaModel();
$negocioId = !empty($negocio) ? (int) $negocio['id'] : 0;
$promedioResenas = $negocioId > 0 ? $resenaModel->promedioPorNegocio($negocioId) : null;
$totalResenas = (int) ($promedioResenas['total'] ?? 0);
?>
<div class="dashboard-home">
    <?php if (($_GET['borrado'] ?? '') === 'ok'): ?>
        <p class="home-alerta home-alerta--exito">El negocio se eliminó correctamente.</p>
    <?php elseif (($_GET['borrado'] ?? '') === 'error'): ?>
        <p class="home-alerta home-alerta--error">No se pudo eliminar el negocio.</p>
    <?php endif; ?>
    <!-- Bienvenida -->
    <section class="dashboard-bienvenida">
        <div class="bienvenida-copy">
            <p class="bienvenida-eyebrow">Panel de administración</p>
            <h1><?= $saludo ?>, <?= htmlspecialchars(explode(' ', $nombreAdmin)[0]) ?> 👋</h1>
            <p>Este es el estado actual de tu negocio. Todo listo para seguir creciendo.</p>
        </div>
        <div class="bienvenida-fecha">
            <?php $meses = [1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE']; ?>
            <strong><?= date('d') ?></strong>
            <span><?= $meses[(int) date('n')] ?></span>
        </div>
    </section>

    <!-- Tarjetas de estadísticas -->
    <section class="stats-grid">
        <a class="stat-card" href="index.php?page=gestion_catalogo" style="--grad: linear-gradient(135deg,#7c3aed,#5c3de2)">
            <div class="stat-icono">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 15v-4"/><path d="M11 15V8"/><path d="M15 15v-6"/><path d="M19 15v-8"/></svg>
            </div>
            <div class="stat-copy">
                <span class="stat-valor" data-contar="<?= $totalServicios ?>">0</span>
                <span class="stat-label">Servicios en catálogo</span>
            </div>
            <span class="stat-tendencia" data-tendencia="+">+</span>
        </a>
        <a class="stat-card" href="<?= app_url('detalle.php?id=' . $negocioId . '#resenas') ?>" target="_blank" style="--grad: linear-gradient(135deg,#ec4899,#db2777)">
            <div class="stat-icono">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div class="stat-copy">
                <span class="stat-valor" data-contar="<?= $totalResenas ?>">0</span>
                <span class="stat-label">Reseñas recibidas</span>
            </div>
            <span class="stat-tendencia" data-tendencia="+">+</span>
        </a>
        <div class="stat-card" style="--grad: linear-gradient(135deg,#10b981,#059669)">
            <div class="stat-icono">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.12"/><path d="M22 12c0 5.52-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2s10 4.48 10 10z"/></svg>
            </div>
            <div class="stat-copy">
                <span class="stat-valor"><?= $promedioResenas ? htmlspecialchars($promedioResenas['promedio']) : '—' ?></span>
                <span class="stat-label">Valoración promedio</span>
            </div>
            <span class="stat-tendencia" data-tendencia="★"><abbr title="de 5">/5</abbr></span>
        </div>
    </section>

    <div class="dashboard-grid-dos">
        <!-- Acciones rápidas -->
        <section class="panel-card">
            <div class="panel-card__header">
                <h2>Acciones rápidas</h2>
                <p>Atajos para tu día a día</p>
            </div>
            <div class="acciones-grid">
                <a href="index.php?page=gestion_catalogo" class="accion-item">
                    <span class="accion-icono accion-icono--violeta">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </span>
                    <div>
                        <strong>Nuevo servicio</strong>
                        <small>Agrega un servicio al catálogo</small>
                    </div>
                </a>
                <a href="<?= app_url('detalle.php?id=' . $negocioId) ?>" target="_blank" class="accion-item">
                    <span class="accion-icono accion-icono--verde">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </span>
                    <div>
                        <strong>Reservar por WhatsApp</strong>
                        <small>Abre tu chat directo con tus clientes</small>
                    </div>
                </a>
                <a href="index.php?page=perfil" class="accion-item">
                    <span class="accion-icono accion-icono--esmeralda">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <div>
                        <strong>Editar perfil</strong>
                        <small>Actualiza tu información</small>
                    </div>
                </a>
                <a class="accion-item" href="<?= app_url('view/index.php') ?>" target="_blank">
                    <span class="accion-icono accion-icono--ambar">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    </span>
                    <div>
                        <strong>Ver directorio</strong>
                        <small>Así te ven tus clientes</small>
                    </div>
                </a>
                <a href="index.php?page=perfil&negocio_id=0" class="accion-item">
                    <span class="accion-icono accion-icono--rosa">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19c-4.4 0-7-2.3-7-5.6C5 10 8 9.4 8 6.6c0-2 1.5-3.6 3-3.6 1.3 0 3 1.5 3 3.9 0 2.6-.6 3.9.6 3.9 1 0 1.4-.5 1.4-1.9 0-1.4-.9-2.6-1.3-3.4 1.1.1 2.8-.2 4.1-2.1 0 0 1.2 5.4.7 7.6-.4 1.8-1.9 2.4-3.1 3.7"/><path d="M15 17a4 4 0 11 4-4"/></svg>
                    </span>
                    <div>
                        <strong>Agregar negocio</strong>
                        <small>Registra otro emprendimiento</small>
                    </div>
                </a>
            </div>
        </section>

        <!-- Estado del negocio -->
        <section class="panel-card">
            <div class="panel-card__header">
                <h2>Estado del negocio</h2>
                <p>Resumen de tu presencia</p>
            </div>
            <?php if (empty($misNegocios)): ?>
                <div class="sin-negocio">
                    <p>Todavía no tienes ningún negocio registrado.</p>
                    <a href="index.php?page=perfil&negocio_id=0" class="panel-enlace">Registrar mi primer negocio
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            <?php elseif (!empty($negocio)): ?>
                <div class="negocio-estado">
                    <div class="negocio-estado__avatar">
                        <?php if (!empty($negocio['logo'])): ?>
                            <img src="<?= htmlspecialchars($negocio['logo']) ?>" alt="<?= htmlspecialchars($negocio['nombre'] ?? '') ?>">
                        <?php else: ?>
                            <span><?= strtoupper(substr($negocio['nombre'] ?? 'N', 0, 1)) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="negocio-estado__copy">
                        <strong><?= htmlspecialchars($negocio['nombre'] ?? 'Sin nombre') ?></strong>
                        <span><?= htmlspecialchars($negocio['categoria'] ?? 'Sin categoría') ?></span>
                    </div>
                    <span class="negocio-estado__badge <?= ($negocio['estado'] ?? null) === 'inactivo' ? 'pausado' : '' ?>">
                        <span class="badge-punto"></span> <?= ($negocio['estado'] ?? null) === 'inactivo' ? 'Pausado' : 'Activo' ?>
                    </span>
                </div>
                <ul class="resumen-lista">
                    <li>
                        <span>Perfil completado</span>
                        <div class="barra-progreso"><i style="width: <?= $progresoPerfil ?>%"></i></div>
                    </li>
                    <li>
                        <span>Servicios publicados</span>
                        <div class="barra-progreso"><i style="width: <?= $progresoServicios ?>%"></i></div>
                    </li>
                    <li>
                        <span>Disponibilidad</span>
                        <div class="barra-progreso"><i style="width: <?= $progresoDisponibilidad ?>%"></i></div>
                    </li>
                </ul>
                <a href="index.php?page=perfil&negocio_id=<?= (int) $negocio['id'] ?>" class="panel-enlace">Completar mi perfil
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            <?php endif; ?>
        </section>
    </div>

    <!-- Últimos servicios -->
    <?php if (!empty($servicios)): ?>
    <section class="panel-card">
        <div class="panel-card__header">
            <div>
                <h2>Últimos servicios</h2>
                <p>Lo más reciente en tu catálogo</p>
            </div>
            <a href="index.php?page=gestion_catalogo" class="panel-enlace ver-todo">Ver todo</a>
        </div>
        <div class="mini-servicios">
            <?php foreach (array_slice($servicios, 0, 4) as $servicio): ?>
                <div class="mini-servicio">
                    <span class="mini-servicio__punto <?= $servicio['estado'] === 'disponible' ? 'disponible' : 'no-disponible' ?>"></span>
                    <div class="mini-servicio__copy">
                        <strong><?= htmlspecialchars($servicio['nombre']) ?></strong>
                        <small><?= htmlspecialchars($servicio['categoria'] ?? 'Servicio') ?></small>
                    </div>
                    <span class="mini-servicio__precio">$<?= number_format((float) $servicio['precio'], 2) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<script>
(function() {
    const animarContador = (el) => {
        const objetivo = parseInt(el.dataset.contar || '0', 10);
        const duracion = 900;
        const inicio = performance.now();
        const paso = (ahora) => {
            const progreso = Math.min((ahora - inicio) / duracion, 1);
            const suavizado = 1 - Math.pow(1 - progreso, 3);
            el.textContent = Math.round(objetivo * suavizado);
            if (progreso < 1) requestAnimationFrame(paso);
        };
        requestAnimationFrame(paso);
    };

    document.querySelectorAll('.stat-valor[data-contar]').forEach(animarContador);
})();
</script>