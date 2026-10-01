<?php $currentPage = $_GET['page'] ?? 'home';
      $misNegocios = $misNegocios ?? [];
      $negocioSeleccionado = ctype_digit($_GET['negocio_id'] ?? '') ? (int) $_GET['negocio_id'] : 0; ?>
<button type="button" class="sidebar-toggle" id="sidebar-toggle" aria-controls="sidebar" aria-expanded="true" aria-label="Ocultar menú">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>
<div class="sidebar-overlay" id="sidebar-overlay"></div>
<aside class="sidebar" id="sidebar">
    <a href="index.php?page=home" class="brand">
        <span class="brand-mark" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
        </span>
        <span class="brand-copy">
            <strong>CatalogoPro</strong>
            <small>Panel de Administración</small>
        </span>
    </a>
    <div class="account-summary">
        <span class="account-avatar"><?= strtoupper(substr($_SESSION['usuario_nombre'] ?? 'SA', 0, 2)) ?></span>
        <span class="account-copy">
            <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Studio Aura Beauty') ?></strong>
            <small>Administrador</small>
        </span>
        <span class="online-dot" title="En línea"></span>
    </div>
    <hr>
    <nav class="menu-navegacion">
        <h3>NAVEGACIÓN</h3>
        <ul>
            <li>
                <a href="index.php?page=home" class="btn-nav <?= $currentPage === 'home' ? 'active' : '' ?>">
                    <svg class="nav-icono" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Panel Principal
                </a>
            </li>
            <li>
                <a href="index.php?page=perfil" class="btn-nav <?= $currentPage === 'perfil' ? 'active' : '' ?>">
                    <svg class="nav-icono" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Perfil del Negocio
                </a>
            </li>
            <li>
                <a href="index.php?page=gestion_catalogo" class="btn-nav <?= $currentPage === 'gestion_catalogo' ? 'active' : '' ?>">
                    <svg class="nav-icono" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    Gestión de Catálogo
                </a>
            </li>
            <li>
                <a href="index.php?page=cuenta" class="btn-nav <?= $currentPage === 'cuenta' ? 'active' : '' ?>">
                    <svg class="nav-icono" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Mi Cuenta
                </a>
            </li>
            <li>
                <a href="<?= app_url('view/index.php') ?>" target="_blank" class="btn-nav">
                    <svg class="nav-icono" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    Vista del Cliente
                </a>
            </li>
        </ul>
    </nav>
    <?php if (!empty($misNegocios)): ?>
        <div class="menu-negocios">
            <h3>MIS NEGOCIOS</h3>
            <ul>
                <?php foreach ($misNegocios as $miNegocio): ?>
                    <li>
                        <a href="index.php?page=perfil&negocio_id=<?= (int) $miNegocio['id'] ?>"
                           class="btn-negocio <?= $negocioSeleccionado === (int) $miNegocio['id'] ? 'active' : '' ?>"
                           title="<?= htmlspecialchars($miNegocio['nombre'] ?? '') ?>">
                            <span class="negocio-avatar">
                                <?php if (!empty($miNegocio['logo'])): ?>
                                    <img src="<?= htmlspecialchars(app_url($miNegocio['logo'])) ?>" alt="">
                                <?php else: ?>
                                    <?= strtoupper(substr($miNegocio['nombre'] ?? 'N', 0, 1)) ?>
                                <?php endif; ?>
                            </span>
                            <span class="negocio-copy">
                                <strong><?= htmlspecialchars($miNegocio['nombre'] ?? 'Negocio') ?></strong>
                                <small><?= htmlspecialchars($miNegocio['categoria'] ?? 'Sin categoría') ?></small>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
                <li>
                    <a href="index.php?page=perfil&negocio_id=0" class="btn-negocio btn-negocio--nuevo">
                        <span class="negocio-avatar negocio-avatar--nuevo">+</span>
                        <span class="negocio-copy">
                            <strong>Nuevo negocio</strong>
                            <small>Registrar otro emprendimiento</small>
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    <?php endif; ?>
    <hr>
    <div class="menu-secundario">
        <span class="menu-secundario__titulo">SESIÓN</span>
        <a href="view/logout.php" class="btn-logout">
            <svg class="nav-icono" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Cerrar sesión
        </a>
    </div>
</aside>