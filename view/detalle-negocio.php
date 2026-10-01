?<?php
require_once __DIR__ . '/../model/NegocioModel.php';
require_once __DIR__ . '/../model/ServicioModel.php';
require_once __DIR__ . '/../config/base.php';

if (!isset($negocio)) {
    $negocio = isset($_GET['id']) && ctype_digit($_GET['id'])
        ? (new NegocioModel())->obtenerPorId((int) $_GET['id'])
        : null;
    $servicios = $negocio ? (new ServicioModel())->obtenerPorNegocio((int) $negocio['id']) : [];
}

if (!isset($fotos)) {
    $fotos = $negocio ? (new NegocioModel())->obtenerFotos((int) $negocio['id']) : [];
}

require_once __DIR__ . '/../model/ResenaModel.php';
$resenaModel = new ResenaModel();
$resenas = $negocio ? $resenaModel->obtenerPorNegocio((int) $negocio['id']) : [];
$promedioResenas = $negocio ? $resenaModel->promedioPorNegocio((int) $negocio['id']) : null;

$horario = ($negocio['horario'] ?? '') !== '' ? json_decode($negocio['horario'], true) : null;
$horario = is_array($horario) ? $horario : null;

$noEncontrado = empty($negocio);
$nombre = $negocio['nombre'] ?? 'Negocio sin nombre';
$categoria = $negocio['categoria'] ?? 'Servicios';
$ubicacion = $negocio['ubicacion'] ?? '';
$descripcion = $negocio['descripcion'] ?? '';

$wspLimpio = preg_replace('/\D+/', '', $negocio['whatsapp'] ?? '');
if ($wspLimpio !== '' && strlen($wspLimpio) === 9 && $wspLimpio[0] === '9') {
    $wspLimpio = '51' . $wspLimpio;
}
$tieneWsp = $wspLimpio !== '';
$urlWsp = $tieneWsp ? url_whatsapp($wspLimpio, mensaje_contacto_directorio($nombre)) : '#';

$telLimpio = preg_replace('/\D+/', '', $negocio['telefono'] ?? '');
$urlTel = $telLimpio !== '' ? 'tel:+' . $telLimpio : '#';
$urlCorreo = !empty($negocio['correo']) ? 'mailto:' . $negocio['correo'] : '#';
$iniciales = strtoupper(substr(preg_replace('/\s+/', '', $nombre), 0, 2) ?: 'N');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($noEncontrado ? 'Negocio no encontrado' : $nombre . ' · AgendaPro') ?></title>
    <link rel="stylesheet" href="<?= app_url('assertes/css/directorio-publico.css') ?>?v=5">
    <link rel="stylesheet" href="<?= app_url('assertes/css/detalle-negocio.css') ?>?v=7">
</head>
<body>

<?php if ($noEncontrado): ?>
    <main class="detalle no-encontrado">
        <div class="no-encontrado-icono">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
        </div>
        <h1>Negocio no encontrado</h1>
        <p>El negocio que buscas no existe o ya no está disponible.</p>
    </main>
<?php else: ?>
    <!-- Portada del negocio -->
    <header class="detalle-portada">
        <div class="detalle-particulas" id="detalle-particulas"></div>
        <div class="detalle-portada__contenido">
            <div class="detalle-logo-wrapper">
                <?php if (!empty($negocio['logo'])): ?>
                    <img class="detalle-logo" src="<?= htmlspecialchars(app_url($negocio['logo'])) ?>" alt="Logo de <?= htmlspecialchars($nombre) ?>">
                <?php else: ?>
                    <span class="detalle-logo detalle-logo-texto"><?= htmlspecialchars($iniciales) ?></span>
                <?php endif; ?>
            </div>
            <p class="detalle-eyebrow">Directorio AgendaPro</p>
            <h1><?= htmlspecialchars($nombre) ?></h1>
            <span class="detalle-categoria"><?= htmlspecialchars($categoria) ?></span>
            <?php if ($promedioResenas): ?>
                <div class="detalle-rating">
                    <span class="detalle-rating__estrellas" aria-hidden="true"><?= str_repeat('★', (int) round((float) $promedioResenas['promedio'])) . str_repeat('☆', 5 - (int) round((float) $promedioResenas['promedio'])) ?></span>
                    <span><?= htmlspecialchars($promedioResenas['promedio']) ?> · <?= (int) $promedioResenas['total'] ?> reseñas</span>
                </div>
            <?php endif; ?>
            <?php if ($descripcion !== ''): ?>
                <p class="detalle-descripcion"><?= nl2br(htmlspecialchars($descripcion)) ?></p>
            <?php endif; ?>
        </div>
    </header>

    <main class="detalle-contenido">
        <!-- Tarjeta de contacto -->
        <section class="detalle-contacto">
            <h2>Contacta con el negocio</h2>
            <div class="detalle-acciones">
                <?php if ($tieneWsp): ?>
                    <a class="accion-contacto accion-wsp" href="<?= $urlWsp ?>" target="_blank" rel="noopener">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WhatsApp
                        <small><?= htmlspecialchars($negocio['whatsapp']) ?></small>
                    </a>
                <?php endif; ?>
                <?php if ($urlTel !== '#'): ?>
                    <a class="accion-contacto" href="<?= $urlTel ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                        Llamar
                        <small><?= htmlspecialchars($negocio['telefono']) ?></small>
                    </a>
                <?php endif; ?>
                <?php if ($urlCorreo !== '#'): ?>
                    <a class="accion-contacto" href="<?= $urlCorreo ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        Correo
                        <small><?= htmlspecialchars($negocio['correo']) ?></small>
                    </a>
                <?php endif; ?>
                <?php if ($ubicacion !== ''): ?>
                    <div class="accion-contacto accion-ubicacion">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Ubicación
                        <small><?= htmlspecialchars($ubicacion) ?></small>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Horario de atención -->
        <?php if ($horario): ?>
        <section class="detalle-horario">
            <div class="detalle-seccion-titulo">
                <div>
                    <p class="detalle-eyebrow">Atención</p>
                    <h2>Horario de atención</h2>
                </div>
            </div>
            <ul class="detalle-horario__lista">
                <?php
                $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                foreach ($diasSemana as $dia):
                    $configDia = $horario[$dia] ?? null;
                    $abierto = is_array($configDia) && !empty($configDia['abierto']);
                ?>
                    <li class="detalle-horario__dia <?= $abierto ? 'abierto' : 'cerrado' ?>">
                        <span class="detalle-horario__nombre"><?= $dia ?></span>
                        <?php if ($abierto): ?>
                            <span class="detalle-horario__hora"><?= htmlspecialchars($configDia['abre'] ?? '') ?> – <?= htmlspecialchars($configDia['cierra'] ?? '') ?></span>
                            <span class="detalle-horario__badge">Abierto</span>
                        <?php else: ?>
                            <span class="detalle-horario__hora">Cerrado</span>
                            <span class="detalle-horario__badge">—</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endif; ?>

        <!-- Galería de fotos del negocio -->
        <?php if (!empty($fotos)): ?>
        <section class="detalle-galeria">
            <div class="detalle-seccion-titulo">
                <div>
                    <p class="detalle-eyebrow">Galería</p>
                    <h2>Fotos del negocio</h2>
                </div>
                <span class="detalle-contador"><?= count($fotos) ?> fotos</span>
            </div>
            <div class="detalle-galeria-grid">
                <?php foreach ($fotos as $i => $foto): ?>
                    <figure class="detalle-galeria-item">
                        <img src="<?= htmlspecialchars(app_url($foto['imagen'])) ?>" alt="Foto <?= $i + 1 ?> de <?= htmlspecialchars($nombre) ?>" loading="lazy">
                    </figure>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Servicios del negocio -->
        <?php if (!empty($servicios)): ?>
        <section class="detalle-servicios">
            <div class="detalle-seccion-titulo">
                <div>
                    <p class="detalle-eyebrow">Catálogo</p>
                    <h2>Servicios de <?= htmlspecialchars(ucfirst($nombre)) ?></h2>
                </div>
                <span class="detalle-contador"><?= count($servicios) ?> servicios</span>
            </div>
            <div class="detalle-servicios-grid">
                <?php foreach ($servicios as $servicio): ?>
                    <article class="detalle-servicio">
                        <?php if (!empty($servicio['imagen'])): ?>
                            <div class="detalle-servicio__img">
                                <img src="<?= htmlspecialchars(app_url($servicio['imagen'])) ?>" alt="<?= htmlspecialchars($servicio['nombre']) ?>">
                            </div>
                        <?php endif; ?>
                        <div class="detalle-servicio__top">
                            <span class="detalle-servicio__categoria"><?= htmlspecialchars($servicio['categoria'] ?? 'Servicio') ?></span>
                            <span class="detalle-servicio__estado <?= $servicio['estado'] === 'disponible' ? 'ok' : 'pausa' ?>">
                                <?= $servicio['estado'] === 'disponible' ? 'Disponible' : 'No disponible' ?>
                            </span>
                        </div>
                        <h3><?= htmlspecialchars($servicio['nombre']) ?></h3>
                        <?php if (!empty($servicio['descripcion'])): ?>
                            <p><?= nl2br(htmlspecialchars($servicio['descripcion'])) ?></p>
                        <?php endif; ?>
                        <div class="detalle-servicio__footer">
                            <div class="detalle-servicio__precio">
                                <small>Precio</small>
                                <strong>S/ <?= number_format((float) $servicio['precio'], 2) ?></strong>
                            </div>
                            <div class="detalle-servicio__duracion">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <?= (int) $servicio['duracion_minutos'] ?> min
                            </div>
                            <?php if ($tieneWsp && $servicio['estado'] === 'disponible'): ?>
                                <a class="detalle-servicio__wsp" href="<?= url_whatsapp($wspLimpio, mensaje_reservar_servicio($nombre, $servicio['nombre'])) ?>" target="_blank" rel="noopener">
                                    Reservar
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php elseif ($descripcion !== ''): ?>
            <section class="detalle-servicios">
                <div class="detalle-seccion-titulo">
                    <div>
                        <p class="detalle-eyebrow">Catálogo</p>
                        <h2>Servicios de <?= htmlspecialchars(ucfirst($nombre)) ?></h2>
                    </div>
                </div>
                <section class="estado-vacio">
                    <p>Este negocio aún no ha publicado servicios. Escríbeles al WhatsApp para conocer su oferta.</p>
                    <?php if ($tieneWsp): ?>
                        <a class="btn-contactar btn-whatsapp" style="max-width: 260px; margin: 16px auto 0;" href="<?= $urlWsp ?>" target="_blank" rel="noopener">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Escríbeles por WhatsApp
                        </a>
                    <?php endif; ?>
                </section>
            </section>
        <?php endif; ?>

        <!-- Reserva tu cita -->
        <section class="detalle-reserva" id="reservar">
            <div class="detalle-seccion-titulo">
                <div>
                    <p class="detalle-eyebrow">AgendaPro</p>
                    <h2>Reserva tu cita</h2>
                    <p class="detalle-reserva__sub">Escríbele directamente por WhatsApp para agendar tu cita.</p>
                </div>
            </div>

            <?php if ($tieneWsp): ?>
                <a class="detalle-btn-reservar detalle-btn-wsp" href="<?= $urlWsp ?>" target="_blank" rel="noopener" style="max-width:320px;margin:24px auto 8px;justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Reservar por WhatsApp
                </a>
                <p class="detalle-reserva__sub" style="text-align:center;margin-top:0;">Te responderán lo antes posible.</p>
            <?php else: ?>
                <p class="detalle-alerta">Este negocio no tiene WhatsApp configurado para recibir reservas.</p>
            <?php endif; ?>
        </section>

        <!-- Reseñas -->
        <section class="detalle-resenas" id="resenas">
            <div class="detalle-seccion-titulo">
                <div>
                    <p class="detalle-eyebrow">Opiniones</p>
                    <h2>Reseñas de clientes</h2>
                </div>
                <?php if ($promedioResenas): ?>
                    <div class="detalle-rating detalle-rating--grande">
                        <span class="detalle-rating__estrellas" aria-hidden="true"><?= str_repeat('★', (int) round((float) $promedioResenas['promedio'])) . str_repeat('☆', 5 - (int) round((float) $promedioResenas['promedio'])) ?></span>
                        <span><?= htmlspecialchars($promedioResenas['promedio']) ?> de 5 · <?= (int) $promedioResenas['total'] ?> reseñas</span>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (($_GET['resena'] ?? '') === 'ok'): ?>
                <p class="detalle-alerta detalle-alerta--exito">¡Gracias por tu reseña! Se publicó correctamente.</p>
            <?php elseif (($_GET['resena'] ?? '') === 'error'): ?>
                <p class="detalle-alerta detalle-alerta--error">No se pudo publicar tu reseña. Completa el nombre, elije una puntuación y escribe un comentario.</p>
            <?php endif; ?>

            <div class="detalle-resenas__grid">
                <form class="detalle-form-resena" method="post" action="<?= app_url('controller/ResenaController.php') ?>">
                    <input type="hidden" name="negocio_id" value="<?= (int) $negocio['id'] ?>">
                    <div class="campo-reserva">
                        <label for="resena-nombre">Tu nombre</label>
                        <input type="text" id="resena-nombre" name="cliente_nombre" required maxlength="120" placeholder="Tu nombre">
                    </div>
                    <div class="campo-reserva">
                        <label>Tu puntuación</label>
                        <div class="resena-estrellas" id="resena-estrellas" data-valor="5">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="estrella-<?= $i ?>" name="rating" value="<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
                                <label for="estrella-<?= $i ?>" data-valor="<?= $i ?>" title="<?= $i ?> estrellas">★</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="campo-reserva">
                        <label for="resena-comentario">Tu comentario</label>
                        <textarea id="resena-comentario" name="comentario" rows="3" required maxlength="600" placeholder="¿Cómo fue tu experiencia?"></textarea>
                    </div>
                    <button class="detalle-btn-reservar detalle-btn-resena" type="submit">Publicar reseña</button>
                </form>

                <div class="detalle-resenas__lista">
                    <?php if (empty($resenas)): ?>
                        <p class="detalle-resenas__vacio">Aún no hay reseñas. ¡Sé el primero en opinar!</p>
                    <?php else: ?>
                        <?php foreach ($resenas as $resena): ?>
                            <article class="resena-item">
                                <div class="resena-item__top">
                                    <span class="resena-item__avatar"><?= htmlspecialchars(strtoupper(substr($resena['cliente_nombre'], 0, 1))) ?></span>
                                    <div>
                                        <strong><?= htmlspecialchars($resena['cliente_nombre']) ?></strong>
                                        <span class="resena-item__estrellas" aria-hidden="true"><?= str_repeat('★', (int) $resena['rating']) . str_repeat('☆', 5 - (int) $resena['rating']) ?></span>
                                    </div>
                                    <span class="resena-item__fecha"><?= date('d/m/Y', strtotime($resena['created_at'])) ?></span>
                                </div>
                                <p class="resena-item__texto"><?= nl2br(htmlspecialchars($resena['comentario'])) ?></p>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="directorio-footer">
        <p>AgendaPro &copy; <?= date('Y') ?> &mdash; Directorio de Emprendimientos</p>
    </footer>

    <script>
        (function () {
            const contenedor = document.getElementById('detalle-particulas');
            if (contenedor) {
                for (let i = 0; i < 26; i++) {
                    const p = document.createElement('div');
                    p.className = 'detalle-particula';
                    const size = Math.random() * 3 + 1;
                    p.style.cssText = `
                        width:${size}px;height:${size}px;
                        left:${Math.random() * 100}%;top:${Math.random() * 100}%;
                        animation-duration:${Math.random() * 10 + 7}s;
                        animation-delay:${Math.random() * 5}s;
                    `;
                    contenedor.appendChild(p);
                }
            }

            const items = document.querySelectorAll('.detalle-galeria-item img');
            if (items.length) {
                const lightbox = document.createElement('div');
                lightbox.className = 'detalle-lightbox';
                lightbox.innerHTML = '<button type="button" class="detalle-lightbox__cerrar" aria-label="Cerrar">&times;</button>';
                const lbImg = document.createElement('img');
                lightbox.appendChild(lbImg);
                document.body.appendChild(lightbox);

                items.forEach(function (img) {
                    img.addEventListener('click', function () {
                        lbImg.src = img.src;
                        lbImg.alt = img.alt;
                        lightbox.classList.add('abierto');
                    });
                });

                lightbox.addEventListener('click', function (e) {
                    if (e.target !== lbImg) lightbox.classList.remove('abierto');
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') lightbox.classList.remove('abierto');
                });
            }

            // Estrellas de la reseña
            const estrellas = document.getElementById('resena-estrellas');
            if (estrellas) {
                const radios = estrellas.querySelectorAll('input[name="rating"]');
                const pintar = (valor) => {
                    estrellas.querySelectorAll('label').forEach((label) => {
                        label.classList.toggle('activa', parseInt(label.dataset.valor || '0', 10) <= valor);
                    });
                };
                estrellas.addEventListener('change', () => {
                    pintar(parseInt(estrellas.dataset.valor || '0', 10));
                });
                pintar(parseInt(estrellas.dataset.valor || '5', 10));
            }
        })();
    </script>
<?php endif; ?>
</body>
</html>
