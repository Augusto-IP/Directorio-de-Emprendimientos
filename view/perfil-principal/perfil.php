<?php
require_once __DIR__ . '/../../config/base.php';
if (!defined('APP_LAYOUT')) {
    header('Location: ' . app_url('index.php?page=perfil'));
    exit;
}
$modoEdicion = !empty($negocio['id']);
$fotosGaleria = $modoEdicion ? (new NegocioModel())->obtenerFotos((int) $negocio['id']) : [];
$horarioGuardado = ($negocio['horario'] ?? '') !== '' ? json_decode($negocio['horario'], true) : null;
$horarioGuardado = is_array($horarioGuardado) ? $horarioGuardado : [];
$diasHorario = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
?>
<div class="profile-wrapper">
    <div class="toast-contenedor" id="toast-contenedor"></div>

    <?php if (($_GET['status'] ?? '') === 'success'): ?>
        <p class="mensaje-exito" id="mensaje-exito">Los datos del negocio se guardaron correctamente.</p>
    <?php elseif (($_GET['status'] ?? '') === 'error'): ?>
        <p class="mensaje-error">No se pudieron guardar los datos del negocio.</p>
    <?php elseif (($_GET['error'] ?? '') === 'campos_requeridos'): ?>
        <p class="mensaje-error">Completa el nombre, la categoría y un correo válido.</p>
    <?php elseif (($_GET['galeria'] ?? '') === 'ok'): ?>
        <p class="mensaje-exito">La foto se eliminó de la galería.</p>
    <?php elseif (($_GET['galeria'] ?? '') === 'error'): ?>
        <p class="mensaje-error">No se pudo eliminar la foto.</p>
    <?php elseif (($_GET['visible'] ?? '') === 'pausa'): ?>
        <p class="mensaje-exito">El negocio se pausó y ya no aparece en el directorio público.</p>
    <?php elseif (($_GET['visible'] ?? '') === 'activo'): ?>
        <p class="mensaje-exito">El negocio volvió a estar visible en el directorio público.</p>
    <?php elseif (($_GET['visible'] ?? '') === 'error'): ?>
        <p class="mensaje-error">No se pudo cambiar la visibilidad del negocio.</p>
    <?php endif; ?>
    
    <!-- Header -->
    <div class="profile-header">
        <div>
            <p class="header-eyebrow">Personalización</p>
            <h1><?= $modoEdicion ? 'Perfil del Negocio' : 'Registrar Nuevo Negocio' ?></h1>
        </div>
        <?php if ($modoEdicion): ?>
            <div class="profile-header__acciones">
                <button type="button" class="boton-compartir" id="boton-compartir" data-enlace="<?= htmlspecialchars(app_url('detalle.php?id=' . (int) $negocio['id'])) ?>">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    Compartir
                </button>
                <a class="boton-ver-publico" href="<?= app_url('detalle.php?id=' . (int) $negocio['id']) ?>" target="_blank">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    Ver página pública
                </a>
            </div>
        <?php endif; ?>
    </div>

    <form id="perfil-form" action="controller/NegocioController.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="negocio_id" value="<?= $modoEdicion ? (int) $negocio['id'] : 0 ?>">
        
        <!-- Tarjeta 1: Portada y Logo -->
        <div class="profile-card banner-card">
            <div class="banner-decoracion"></div>
            <div class="banner-body">
                <div class="avatar-wrapper">
                    <img src="<?= htmlspecialchars(app_url($negocio['logo'] ?? 'uploads/logos/default_logo.png')) ?>" alt="Logo de la empresa" class="avatar-img" id="avatar-preview">
                    <input type="file" name="logo" id="input-logo" accept="image/*">
                    <label for="input-logo" class="btn-add" title="Subir nuevo logo">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    </label>
                </div>
                <div class="banner-copy">
                    <strong id="banner-nombre"><?= htmlspecialchars($negocio['nombre'] ?? 'Tu negocio') ?></strong>
                    <span><?= htmlspecialchars($negocio['categoria'] ?? 'Agrega una categoría') ?></span>
                </div>
                <label for="input-logo" class="btn-cambiar-logo">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    Cambiar logo
                </label>
            </div>
        </div>

        <!-- Tarjeta 2: Información General -->
        <div class="profile-card">
            <div class="card-titulo-icono">
                <span class="card-icono">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <h3>Información General</h3>
            </div>
            
            <div class="fila-formulario grid-2">
                <div class="campo-formulario">
                    <label for="perfil-nombre">Nombre del Negocio</label>
                    <input type="text" id="perfil-nombre" name="nombre" value="<?= htmlspecialchars($negocio['nombre'] ?? '') ?>" placeholder="Ej. Bella Studio" required />
                </div>

                <div class="campo-formulario">
                    <label for="perfil-categoria">Categoría</label>
                    <input type="text" id="perfil-categoria" name="categoria" value="<?= htmlspecialchars($negocio['categoria'] ?? '') ?>" placeholder="Ej. Salón de Belleza" required />
                </div>
            </div>

            <div class="campo-formulario">
                <label for="perfil-descripcion">Descripción</label>
                <textarea id="perfil-descripcion" name="descripcion" placeholder="Escribe una breve descripción..." rows="3"><?= htmlspecialchars($negocio['descripcion'] ?? '') ?></textarea>
                <span class="contador-caracteres" id="contador-caracteres">0 / 240</span>
            </div>
        </div>

        <!-- Tarjeta 3: Datos de Contacto -->
        <div class="profile-card">
            <div class="card-titulo-icono">
                <span class="card-icono card-icono--esmeralda">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                </span>
                <h3>Datos de Contacto</h3>
            </div>
            
            <div class="fila-formulario grid-2">
                <div class="campo-formulario">
                    <label for="perfil-wsp">WhatsApp de Atención</label>
                    <input type="tel" id="perfil-wsp" name="wsp" value="<?= htmlspecialchars($negocio['whatsapp'] ?? '') ?>" placeholder="+51 987 654 321" />
                </div>
                <div class="campo-formulario">
                    <label for="perfil-correo">Correo Electrónico Comercial</label>
                    <input type="email" id="perfil-correo" name="correo" value="<?= htmlspecialchars($negocio['correo'] ?? '') ?>" placeholder="contacto@minegocio.com" />
                </div>
            </div>

            <div class="fila-formulario grid-2">
                <div class="campo-formulario">
                    <label for="perfil-telefono">Teléfono Fijo / Secundario</label>
                    <input type="tel" id="perfil-telefono" name="telefono" value="<?= htmlspecialchars($negocio['telefono'] ?? '') ?>" placeholder="+51 123 456 789" />
                </div>
                <div class="campo-formulario">
                    <label for="perfil-ubicacion">Ubicación / Dirección</label>
                    <input type="text" id="perfil-ubicacion" name="ubicacion" value="<?= htmlspecialchars($negocio['ubicacion'] ?? '') ?>" placeholder="Av. Principal 123" />
                </div>
            </div>
        </div>

        <!-- Tarjeta 4: Galería de fotos -->
        <div class="profile-card">
            <div class="card-titulo-icono">
                <span class="card-icono card-icono--ambar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </span>
                <h3>Fotos del Negocio</h3>
            </div>
            <p class="galeria-instruccion">Sube fotos de tus trabajos, local o servicios. Al elegirlas quedan listas y se guardan al presionar el botón.</p>

            <?php if ($modoEdicion && !empty($fotosGaleria)): ?>
                <div class="galeria-existente" id="galeria-existente">
                    <?php foreach ($fotosGaleria as $foto): ?>
                        <div class="galeria-item">
                            <img src="<?= htmlspecialchars(app_url($foto['imagen'])) ?>" alt="Foto del negocio">
                            <form action="controller/NegocioController.php" method="post" class="galeria-form-eliminar">
                                <input type="hidden" name="action" value="eliminar_foto">
                                <input type="hidden" name="foto_id" value="<?= (int) $foto['id'] ?>">
                                <input type="hidden" name="negocio_id" value="<?= (int) $negocio['id'] ?>">
                                <button type="submit" class="galeria-eliminar" title="Eliminar foto" aria-label="Eliminar foto">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <label class="galeria-carga" for="input-fotos">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/><polyline points="16 16 12 12 8 16"/></svg>
                Subir fotos
            </label>
            <input type="file" name="fotos[]" id="input-fotos" accept="image/*" multiple hidden>
            <div class="galeria-previews" id="galeria-previews"></div>
        </div>

        <!-- Tarjeta 5: Horario de atención -->
        <div class="profile-card">
            <div class="card-titulo-icono">
                <span class="card-icono card-icono--esmeralda">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </span>
                <h3>Horario de Atención</h3>
            </div>
            <p class="galeria-instruccion">Marca los días que atiendes y define el horario. Los clientes verán esta información en tu página pública.</p>
            <input type="hidden" name="horario" id="perfil-horario" value="<?= htmlspecialchars(json_encode($horarioGuardado), ENT_QUOTES) ?>">
            <div class="horario-lista" id="horario-lista">
                <?php foreach ($diasHorario as $dia):
                    $configDia = $horarioGuardado[$dia] ?? [];
                    $abierto = !empty($configDia['abierto']); ?>
                    <div class="horario-fila <?= $abierto ? 'activo' : '' ?>">
                        <label class="horario-dia-toggle">
                            <input type="checkbox" class="horario-dia-check" data-dia="<?= htmlspecialchars($dia) ?>" <?= $abierto ? 'checked' : '' ?>>
                            <span><?= $dia ?></span>
                        </label>
                        <div class="horario-tiempos">
                            <input type="time" class="horario-abre" data-dia="<?= htmlspecialchars($dia) ?>" value="<?= htmlspecialchars($configDia['abre'] ?? '09:00') ?>" <?= $abierto ? '' : 'disabled' ?>>
                            <span class="horario-guion">hasta</span>
                            <input type="time" class="horario-cierra" data-dia="<?= htmlspecialchars($dia) ?>" value="<?= htmlspecialchars($configDia['cierra'] ?? '18:00') ?>" <?= $abierto ? '' : 'disabled' ?>>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="form-actions">
            <button type="button" class="btn-descartar">Descartar cambios</button>
            <button type="submit" class="btn-guardar">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <?= $modoEdicion ? 'Guardar Cambios' : 'Registrar Negocio' ?>
            </button>
        </div>

    </form>

    <?php if ($modoEdicion): ?>
    <!-- Tarjeta de visibilidad: pausar / activar el negocio -->
    <section class="profile-card perfil-visibilidad">
        <div class="perfil-visibilidad__info">
            <span class="perfil-visibilidad__icono">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </span>
            <div>
                <h3>Visibilidad del negocio</h3>
                <p>
                    <?php if (($negocio['estado'] ?? null) === 'inactivo'): ?>
                        <strong>Tu negocio está pausado</strong> y no aparece en el directorio público. Tus citas y reseñas se conservan.
                    <?php else: ?>
                        Tu negocio está <strong>visible</strong> en el directorio público. Cualquier cliente puede verlo y reservar.
                    <?php endif; ?>
                </p>
            </div>
            <span class="perfil-visibilidad__badge <?= ($negocio['estado'] ?? null) === 'inactivo' ? 'pausado' : 'activo' ?>">
                <?= ($negocio['estado'] ?? null) === 'inactivo' ? 'Pausado' : 'Activo' ?>
            </span>
        </div>
        <form action="controller/NegocioController.php" method="post" class="perfil-visibilidad__form">
            <input type="hidden" name="negocio_id" value="<?= (int) $negocio['id'] ?>">
            <?php if (($negocio['estado'] ?? null) === 'inactivo'): ?>
                <input type="hidden" name="action" value="activar_negocio">
                <button type="submit" class="btn-activar-negocio">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
                    Mostrar en el directorio
                </button>
            <?php else: ?>
                <input type="hidden" name="action" value="pausar_negocio">
                <button type="submit" class="btn-pausar-negocio">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                    Pausar (ocultar del directorio)
                </button>
            <?php endif; ?>
        </form>
    </section>

    <section class="zona-peligro">
        <div class="zona-peligro-copy">
            <span class="zona-peligro-icono">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
            </span>
            <div>
                <h3>Eliminar este negocio</h3>
                <p>Se borrarán también sus servicios, categorías y fotos. Esta acción no se puede deshacer.</p>
            </div>
        </div>
        <button type="button" class="btn-eliminar-negocio" id="abrir-eliminar-negocio">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
            Eliminar negocio
        </button>
    </section>
    <?php endif; ?>
</div>

<!-- Modal: confirmar eliminación del negocio -->
<div class="modal-confirmacion" id="modal-eliminar-negocio" role="dialog" aria-modal="true" aria-labelledby="modal-eliminar-titulo" aria-hidden="true">
    <div class="modal-confirmacion-backdrop" id="cerrar-eliminar-fondo"></div>
    <div class="modal-confirmacion-caja">
        <span class="modal-confirmacion-icono">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
        </span>
        <h3 id="modal-eliminar-titulo">¿Eliminar este negocio?</h3>
        <p>Se borrarán definitivamente el negocio <strong><?= htmlspecialchars($negocio['nombre'] ?? '') ?></strong> junto con todos sus servicios, categorías y fotos.</p>
        <div class="modal-confirmacion-acciones">
            <button type="button" class="btn-cancelar-eliminar" id="cerrar-eliminar">Cancelar</button>
            <form action="controller/NegocioController.php" method="post" id="form-eliminar-negocio">
                <input type="hidden" name="action" value="eliminar_negocio">
                <input type="hidden" name="negocio_id" value="<?= (int) $negocio['id'] ?>">
                <button type="submit" class="btn-confirmar-eliminar" id="confirmar-eliminar">Sí, eliminar</button>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const preview = document.getElementById('avatar-preview');
    const inputLogo = document.getElementById('input-logo');
    const nombreInput = document.getElementById('perfil-nombre');
    const categoriaInput = document.getElementById('perfil-categoria');
    const bannerNombre = document.getElementById('banner-nombre');
    const contador = document.getElementById('contador-caracteres');
    const descripcion = document.getElementById('perfil-descripcion');

    inputLogo.addEventListener('change', function () {
        const archivo = this.files[0];
        if (!archivo) return;
        if (!archivo.type.startsWith('image/')) {
            mostrarToast('El archivo debe ser una imagen', 'error');
            return;
        }
        const lector = new FileReader();
        lector.onload = function (e) { preview.src = e.target.result; };
        lector.readAsDataURL(archivo);
        mostrarToast('Logo seleccionado. Guarda para aplicar.', 'success');
    });

    nombreInput.addEventListener('input', function () {
        bannerNombre.textContent = this.value.trim() || 'Tu negocio';
    });

    const MAXIMO = 240;
    const actualizarContador = function () {
        const len = descripcion.value.length;
        contador.textContent = len + ' / ' + MAXIMO;
        contador.classList.toggle('maximo', len > MAXIMO);
        contador.classList.toggle('cerca', len > MAXIMO * 0.85 && len <= MAXIMO);
    };
    descripcion.addEventListener('input', actualizarContador);
    actualizarContador();

    document.querySelector('.btn-descartar').addEventListener('click', function () {
        window.location.reload();
    });

    document.getElementById('perfil-form').addEventListener('submit', function () {
        const btn = this.querySelector('.btn-guardar');
        btn.disabled = true;
        btn.classList.add('cargando');
        btn.textContent = 'Guardando...';
    });

    const toastContenedor = document.getElementById('toast-contenedor');
    function mostrarToast(mensaje, tipo) {
        const toast = document.createElement('div');
        toast.className = 'toast toast-' + tipo;
        toast.textContent = mensaje;
        toastContenedor.appendChild(toast);
        setTimeout(() => toast.classList.add('visible'), 10);
        setTimeout(() => {
            toast.classList.remove('visible');
            setTimeout(() => toast.remove(), 300);
        }, 2800);
    }

    // Preview de las fotos seleccionadas del negocio
    const inputFotos = document.getElementById('input-fotos');
    const contenedorPreviews = document.getElementById('galeria-previews');
    if (inputFotos && contenedorPreviews) {
        inputFotos.addEventListener('change', function () {
            contenedorPreviews.innerHTML = '';
            const archivos = Array.from(this.files || []).slice(0, 8);
            if (archivos.length === 0) return;
            archivos.forEach((archivo) => {
                if (!archivo.type.startsWith('image/')) return;
                const lector = new FileReader();
                const mini = document.createElement('div');
                mini.className = 'galeria-preview';
                lector.onload = function (e) {
                    mini.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
                };
                lector.readAsDataURL(archivo);
                contenedorPreviews.appendChild(mini);
            });
            mostrarToast(archivos.length === 1 ? '1 foto lista' : archivos.length + ' fotos listas', 'success');
        });
    }

    const mensajeExito = document.getElementById('mensaje-exito');
    if (mensajeExito) {
        setTimeout(() => mensajeExito.classList.add('desvanecer'), 3000);
    }

    // Modal de confirmación para eliminar el negocio
    const modalEliminar = document.getElementById('modal-eliminar-negocio');
    const abrirEliminar = document.getElementById('abrir-eliminar-negocio');
    const cerrarEliminar = document.getElementById('cerrar-eliminar');
    const cerrarFondo = document.getElementById('cerrar-eliminar-fondo');
    const formEliminar = document.getElementById('form-eliminar-negocio');

    function abrirModalEliminar() {
        modalEliminar.classList.add('abierto');
        modalEliminar.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function cerrarModalEliminar() {
        modalEliminar.classList.remove('abierto');
        modalEliminar.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    if (abrirEliminar) {
        abrirEliminar.addEventListener('click', abrirModalEliminar);
    }

    if (cerrarEliminar) {
        cerrarEliminar.addEventListener('click', cerrarModalEliminar);
    }

    if (cerrarFondo) {
        cerrarFondo.addEventListener('click', cerrarModalEliminar);
    }

    document.addEventListener('keydown', function (evento) {
        if (evento.key === 'Escape' && modalEliminar.classList.contains('abierto')) {
            cerrarModalEliminar();
        }
    });

    if (formEliminar) {
        formEliminar.addEventListener('submit', function () {
            const btn = formEliminar.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.classList.add('cargando');
            btn.textContent = 'Eliminando...';
        });
    }

    // Horario de atención: toggle de días y armado del JSON
    const listaHorario = document.getElementById('horario-lista');
    const inputHorario = document.getElementById('perfil-horario');
    if (listaHorario && inputHorario) {
        listaHorario.querySelectorAll('.horario-fila').forEach(function (fila) {
            const check = fila.querySelector('.horario-dia-check');
            const abre = fila.querySelector('.horario-abre');
            const cierra = fila.querySelector('.horario-cierra');
            const sincronizar = function () {
                const activo = check.checked;
                fila.classList.toggle('activo', activo);
                abre.disabled = !activo;
                cierra.disabled = !activo;
            };
            check.addEventListener('change', sincronizar);
            sincronizar();
        });

        const perfilForm = document.getElementById('perfil-form');
        perfilForm.addEventListener('submit', function () {
            const horario = {};
            listaHorario.querySelectorAll('.horario-fila').forEach(function (fila) {
                const check = fila.querySelector('.horario-dia-check');
                const dia = check.dataset.dia;
                horario[dia] = {
                    abierto: check.checked,
                    abre: fila.querySelector('.horario-abre').value || '09:00',
                    cierra: fila.querySelector('.horario-cierra').value || '18:00'
                };
            });
            inputHorario.value = JSON.stringify(horario);
        });
    }

    // Compartir la página pública: copiar enlace con aviso
    const botonCompartir = document.getElementById('boton-compartir');
    if (botonCompartir) {
        botonCompartir.addEventListener('click', function () {
            const enlace = botonCompartir.dataset.enlace || '';
            navigator.clipboard.writeText(enlace).then(function () {
                mostrarToast('Enlace copiado: pégalo en WhatsApp o donde quieras', 'ok');
            }).catch(function () {
                const areaTemporal = document.createElement('textarea');
                areaTemporal.value = enlace;
                document.body.appendChild(areaTemporal);
                areaTemporal.select();
                try {
                    document.execCommand('copy');
                    mostrarToast('Enlace copiado: pégalo en WhatsApp o donde quieras', 'ok');
                } catch (e) {
                    mostrarToast('No se pudo copiar el enlace', 'error');
                }
                document.body.removeChild(areaTemporal);
            });
        });
    }
})();
</script>