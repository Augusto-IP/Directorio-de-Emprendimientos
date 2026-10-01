<?php
session_start();
require_once __DIR__ . '/config/base.php';
require_once __DIR__ . '/model/NegocioModel.php';
require_once __DIR__ . '/model/ServicioModel.php';

// Los visitantes ven el directorio público; los usuarios autenticados ven el panel.
if (!isset($_SESSION['usuario'])) {
    $negocioModel = new NegocioModel();

    // Portada dedicada de un solo negocio
    if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
        $negocio = $negocioModel->obtenerPorId((int) $_GET['id']);
        $servicios = $negocio ? (new ServicioModel())->obtenerPorNegocio((int) $negocio['id']) : [];
        require __DIR__ . '/view/detalle-negocio.php';
        exit;
    }

    $negocios = $negocioModel->obtenerTodos();
    require __DIR__ . '/view/index.php';
    exit;
}

$page = $_GET['page'] ?? 'home';

// Definición de rutas a las vistas
$routes = [
    'home'             => __DIR__ . '/view/dashboard/home.php',
    'perfil'           => __DIR__ . '/view/perfil-principal/perfil.php',
    'gestion_catalogo' => __DIR__ . '/view/gestion/gestion_catalogo.php',
    'cuenta'           => __DIR__ . '/view/cuenta/cuenta.php',
];

// Título dinámico por página
$titulos = [
    'home'             => 'Panel Principal',
    'perfil'           => 'Perfil del Negocio',
    'gestion_catalogo' => 'Gestión de Catálogo',
    'cuenta'           => 'Mi Cuenta',
];
$pageTitle = ($titulos[$page] ?? 'Panel') . ' · AgendaPro';
$vistaContenido = $routes[$page] ?? __DIR__ . '/view/dashboard/home.php';

// ---- Multi-negocio: negocios del usuario logueado + negocio seleccionado ----
$negocioModel = new NegocioModel();
$misNegocios = $negocioModel->obtenerPorUsuario((int) $_SESSION['id_usuario']);

$negocio = null;
$negocioId = ctype_digit($_GET['negocio_id'] ?? '') ? (int) $_GET['negocio_id'] : 0;
$pidioNegocio = array_key_exists('negocio_id', $_GET);
if ($negocioId > 0) {
    $negocio = $negocioModel->obtenerPorIdYUsuario($negocioId, (int) $_SESSION['id_usuario']);
}
if (!$negocio && !$pidioNegocio && !empty($misNegocios)) {
    $negocio = $misNegocios[0];
}

$servicios = [];
if ($negocio && ($page === 'home' || $page === 'gestion_catalogo')) {
    $servicios = (new ServicioModel())->obtenerPorNegocio((int) $negocio['id']);
}

define('APP_LAYOUT', true);

// Carga modular
require_once __DIR__ . '/view/layouts/header.php';
require_once __DIR__ . '/view/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <?php require_once $vistaContenido; ?>
</main>

<?php require_once __DIR__ . '/view/layouts/footer.php'; ?>