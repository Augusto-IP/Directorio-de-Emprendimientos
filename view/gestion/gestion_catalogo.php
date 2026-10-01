<?php
if (!defined('APP_LAYOUT')) {
    header('Location: ../../index.php?page=gestion_catalogo');
    exit;
}

$totalServicios = count($servicios ?? []);
$disponibles = count(array_filter($servicios ?? [], static fn ($servicio) => $servicio['estado'] === 'disponible'));
?>
<section class="catalogo-page">
    <div class="toast-contenedor" id="toast-contenedor"></div>

    <?php if (($_GET['status'] ?? '') === 'success'): ?>
        <p class="sistema-mensaje sistema-exito" id="sistema-mensaje">Servicio agregado correctamente.</p>
    <?php elseif (($_GET['status'] ?? '') === 'actualizado'): ?>
        <p class="sistema-mensaje sistema-exito" id="sistema-mensaje">Servicio actualizado correctamente.</p>
    <?php elseif (($_GET['status'] ?? '') === 'eliminado'): ?>
        <p class="sistema-mensaje sistema-exito" id="sistema-mensaje">Servicio eliminado correctamente.</p>
    <?php elseif (($_GET['status'] ?? '') === 'error'): ?>
        <p class="sistema-mensaje sistema-error">No se pudo guardar el servicio. Verifica los datos.</p>
    <?php endif; ?>

<header class="catalogo-header">
        <div>
            <p class="header-eyebrow">Oferta de servicios</p>
            <h1>Gestión de Catálogo</h1>
            <?php if (!empty($negocio)): ?>
                <p class="catalogo-negocio-nombre">para
                    <a href="<?= app_url('detalle.php?id=' . (int) $negocio['id']) ?>" target="_blank"><?= htmlspecialchars($negocio['nombre']) ?></a>
                </p>
            <?php endif; ?>
            <p>
                <span class="chip chip-violeta"><?= $totalServicios ?> servicios</span>
                <span class="chip chip-verde"><?= $disponibles ?> disponibles</span>
                <span class="chip chip-rojo"><?= $totalServicios - $disponibles ?> en pausa</span>
            </p>
        </div>
        <?php if (!empty($negocio)): ?>
            <button type="button" class="btn-nuevo" id="abrir-servicio">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Agregar Servicio</span>
            </button>
        <?php endif; ?>
    </header>

    <?php if (empty($negocio)): ?>
        <section class="tabla-vacia">
            <p>Aún no tienes ningún negocio registrado.</p>
            <a class="btn-nuevo" href="index.php?page=perfil&negocio_id=0">Registrar mi primer negocio</a>
        </section>
    <?php else: ?>
    <section class="negocio-resumen">
            <div class="resumen-logo">
                <?php if (!empty($negocio['logo'])): ?>
                    <img class="negocio-logo" src="<?= htmlspecialchars($negocio['logo']) ?>" alt="Logo de <?= htmlspecialchars($negocio['nombre']) ?>">
                <?php else: ?>
                    <span class="negocio-logo negocio-logo-texto"><?= strtoupper(substr($negocio['nombre'] ?? 'N', 0, 1)) ?></span>
                <?php endif; ?>
            </div>
            <div>
                <span class="resumen-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Negocio
                </span>
                <strong><?= htmlspecialchars($negocio['nombre'] ?? 'Sin nombre') ?></strong>
                <span><?= htmlspecialchars($negocio['categoria'] ?? 'Sin categoría') ?></span>
            </div>
            <div>
                <span class="resumen-label">Descripción</span>
                <span><?= htmlspecialchars($negocio['descripcion'] ?? 'Sin descripción registrada') ?></span>
            </div>
            <div>
                <span class="resumen-label">Contacto</span>
                <span><?= htmlspecialchars($negocio['whatsapp'] ?? 'Sin WhatsApp registrado') ?></span>
                <span><?= htmlspecialchars($negocio['correo'] ?? 'Sin correo registrado') ?></span>
            </div>
            <div>
                <span class="resumen-label">Ubicación</span>
                <span><?= htmlspecialchars($negocio['telefono'] ?? 'Sin teléfono registrado') ?></span>
                <span><?= htmlspecialchars($negocio['ubicacion'] ?? 'Sin dirección registrada') ?></span>
            </div>
        </section>

    <div class="catalogo-toolbar">
        <label class="buscador">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="search" id="buscar-servicios" placeholder="Buscar servicios..." aria-label="Buscar servicios">
        </label>
        <div class="filtros" role="group" aria-label="Filtrar servicios">
            <button type="button" class="filtro active" data-filter="todos">Todos</button>
            <button type="button" class="filtro" data-filter="barberia">Barbería</button>
            <button type="button" class="filtro" data-filter="unas">Uñas</button>
            <button type="button" class="filtro" data-filter="corte">Corte</button>
            <button type="button" class="filtro" data-filter="estetica">Estética</button>
        </div>
        <span class="resultado-count" id="resultado-count"></span>
    </div>

    <div class="servicios-grid" id="servicios-grid">
        <?php foreach (($servicios ?? []) as $servicio): ?>
            <article class="servicio-card" data-category="<?= htmlspecialchars(strtolower($servicio['categoria'] ?? '')) ?>" data-name="<?= htmlspecialchars(strtolower($servicio['nombre'])) ?>">
                <div class="servicio-imagen <?= empty($servicio['imagen']) ? 'imagen-generica' : '' ?>">
                    <?php if (!empty($servicio['imagen'])): ?>
                        <img class="servicio-imagen-img" src="<?= htmlspecialchars(app_url($servicio['imagen'])) ?>" alt="<?= htmlspecialchars($servicio['nombre']) ?>">
                    <?php else: ?>
                        <div class="servicio-decoracion"></div>
                    <?php endif; ?>
                    <span class="categoria categoria-azul"><?= htmlspecialchars($servicio['categoria'] ?? 'Servicio') ?></span>
                    <span class="estado <?= $servicio['estado'] === 'disponible' ? 'disponible' : 'no-disponible' ?>">
                        <span class="estado-punto"></span>
                        <?= $servicio['estado'] === 'disponible' ? 'Disponible' : 'No disponible' ?>
                    </span>
                    <div class="servicio-duracion">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?= (int) $servicio['duracion_minutos'] ?> min
                    </div>
                </div>
                <div class="servicio-contenido">
                    <h2><?= htmlspecialchars($servicio['nombre']) ?></h2>
                    <p><?= htmlspecialchars($servicio['descripcion'] ?? '') ?></p>
                    <div class="servicio-datos">
                        <span>Precio<strong>$<?= number_format((float) $servicio['precio'], 2) ?></strong></span>
                        <span class="precio-final">Desde<strong><?= htmlspecialchars($servicio['categoria'] ?? '—') ?></strong></span>
                    </div>
                </div>
                <div class="servicio-acciones">
                    <button type="button" class="servicio-accion servicio-accion--editar" data-editar
                            data-id="<?= (int) $servicio['id'] ?>"
                            data-nombre="<?= htmlspecialchars($servicio['nombre']) ?>"
                            data-categoria="<?= htmlspecialchars($servicio['categoria'] ?? '') ?>"
                            data-descripcion="<?= htmlspecialchars($servicio['descripcion'] ?? '') ?>"
                            data-precio="<?= htmlspecialchars($servicio['precio']) ?>"
                            data-duracion="<?= (int) $servicio['duracion_minutos'] ?>"
                            data-estado="<?= htmlspecialchars($servicio['estado']) ?>"
                            data-imagen="<?= htmlspecialchars(app_url($servicio['imagen'] ?? '')) ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                        Editar
                    </button>
                    <button type="button" class="servicio-accion servicio-accion--eliminar" data-eliminar
                            data-id="<?= (int) $servicio['id'] ?>"
                            data-nombre="<?= htmlspecialchars($servicio['nombre']) ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                        Eliminar
                    </button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (empty($servicios)): ?>
        <section class="tabla-vacia">
            <p>Aún no tienes servicios. ¡Agrega el primero!</p>
        </section>
    <?php endif; ?>
</section>

<!-- Modal -->
<div class="modal-servicio" id="modal-servicio" hidden>
    <div class="modal-backdrop" id="cerrar-fondo-servicio"></div>
    <form class="form-servicio" action="controller/ServicioController.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" id="servicio-accion" value="guardar">
        <input type="hidden" name="servicio_id" id="servicio-id" value="0">
        <input type="hidden" name="negocio_id" value="<?= (int) $negocio['id'] ?>">
        <button type="button" class="cerrar-modal" id="cerrar-servicio" aria-label="Cerrar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="modal-titulo">
            <span class="modal-icono">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            </span>
            <div>
                <h2 id="servicio-modal-titulo">Agregar Servicio</h2>
                <p>Completa los datos del servicio</p>
            </div>
        </div>
        <label>Nombre<input name="nombre" id="servicio-nombre" required placeholder="Ej. Corte clásico"></label>
        <label>Categoría<input name="categoria" id="servicio-categoria" required placeholder="Ej. Barbería" list="categorias-sugeridas"></label>
        <datalist id="categorias-sugeridas">
            <option value="Barbería"></option>
            <option value="Uñas"></option>
            <option value="Corte"></option>
            <option value="Estética"></option>
            <option value="Estilismo"></option>
        </datalist>
        <label>Descripción<textarea name="descripcion" id="servicio-descripcion" rows="3" placeholder="Describe el servicio"></textarea></label>
        <div class="form-servicio-grid">
            <label>Precio (S/)<input name="precio" id="servicio-precio" type="number" min="0" step="0.01" required placeholder="180"></label>
            <label>Duración (min)<input name="duracion" id="servicio-duracion" type="number" min="1" required placeholder="30"></label>
        </div>
        <label>Imagen
            <input type="file" name="imagen" id="servicio-imagen-input" accept="image/*">
        </label>
        <img class="servicio-imagen-preview" id="servicio-imagen-preview" alt="Preview del servicio">
        <label>Estado
            <select name="estado" id="servicio-estado">
                <option value="disponible">Disponible</option>
                <option value="no_disponible">No disponible</option>
            </select>
        </label>
        <button class="btn-nuevo btn-guardar-servicio" type="submit">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="20 6 9 17 4 12"/></svg>
            Guardar Servicio
        </button>
    </form>
</div>
</section>
    <?php endif; ?>
<script src="<?= app_url('assertes/js/catalogo.js') ?>?v=2"></script>