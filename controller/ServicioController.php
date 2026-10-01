<?php
require_once __DIR__ . '/../model/ServicioModel.php';
require_once __DIR__ . '/../model/NegocioModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=gestion_catalogo');
    exit;
}

session_start();
if (empty($_SESSION['id_usuario'])) {
    header('Location: ../index.php?page=login');
    exit;
}
$usuarioId = (int) $_SESSION['id_usuario'];

$negocioId = (int) ($_POST['negocio_id'] ?? 0);
if ($negocioId <= 0) {
    header('Location: ../index.php?page=gestion_catalogo&status=error');
    exit;
}

// Verifica que el negocio pertenezca al usuario logueado
$negocioModel = new NegocioModel();
if (!$negocioModel->obtenerPorIdYUsuario($negocioId, $usuarioId)) {
    header('Location: ../index.php?page=gestion_catalogo&status=error');
    exit;
}

$modelo = new ServicioModel();
$accion = $_POST['action'] ?? 'guardar';
$servicioId = (int) ($_POST['servicio_id'] ?? 0);

// --- ACCIÓN: eliminar un servicio ---
if ($accion === 'eliminar') {
    $servicioAEliminar = $servicioId > 0 ? $modelo->obtenerPorIdYNegocio($servicioId, $negocioId) : null;
    $ok = $servicioAEliminar && $modelo->eliminar($servicioId, $negocioId);

    if ($ok && !empty($servicioAEliminar['imagen'])) {
        $ruta = dirname(__DIR__) . '/' . $servicioAEliminar['imagen'];
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }

    header('Location: ../index.php?page=gestion_catalogo&negocio_id=' . $negocioId . '&status=' . ($ok ? 'eliminado' : 'error'));
    exit;
}

// --- ACCIÓN: guardar o actualizar un servicio ---
$nombre = trim($_POST['nombre'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$precio = filter_var($_POST['precio'] ?? '', FILTER_VALIDATE_FLOAT);
$duracion = filter_var($_POST['duracion'] ?? '', FILTER_VALIDATE_INT);
$estado = ($_POST['estado'] ?? 'disponible') === 'no_disponible' ? 'no_disponible' : 'disponible';

if ($nombre === '' || $categoria === '' || $precio === false || $duracion === false) {
    header('Location: ../index.php?page=gestion_catalogo&negocio_id=' . $negocioId . '&status=error');
    exit;
}

// Manejo de la imagen del servicio
$imagen = null;
if ($accion === 'actualizar') {
    $servicioActual = $modelo->obtenerPorIdYNegocio($servicioId, $negocioId);
    if ($servicioActual) {
        $imagen = $servicioActual['imagen'];
    }
}
if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['imagen'];
    $tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $ext = $tiposPermitidos[$archivo['type']] ?? null;
    if ($ext === null || $archivo['size'] > 4 * 1024 * 1024) {
        header('Location: ../index.php?page=gestion_catalogo&negocio_id=' . $negocioId . '&status=error');
        exit;
    }
    $carpeta = dirname(__DIR__) . '/uploads/servicios';
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }
    $nombreArchivo = 'servicio_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($archivo['tmp_name'], $carpeta . '/' . $nombreArchivo)) {
        header('Location: ../index.php?page=gestion_catalogo&negocio_id=' . $negocioId . '&status=error');
        exit;
    }
    $imagen = 'uploads/servicios/' . $nombreArchivo;

    // Si se reemplazó, elimina la imagen anterior
    if (isset($servicioActual['imagen']) && $servicioActual['imagen'] && $servicioActual['imagen'] !== $imagen) {
        $rutaAnterior = dirname(__DIR__) . '/' . $servicioActual['imagen'];
        if (is_file($rutaAnterior)) {
            @unlink($rutaAnterior);
        }
    }
}

$datos = [
    'nombre' => $nombre,
    'categoria' => $categoria,
    'descripcion' => $descripcion,
    'precio' => $precio,
    'duracion' => $duracion,
    'imagen' => $imagen,
    'estado' => $estado,
];

if ($accion === 'actualizar' && $servicioId > 0) {
    $guardado = $modelo->actualizar($datos, $servicioId, $negocioId);
    $statusMsg = $guardado ? 'actualizado' : 'error';
} else {
    $guardado = $modelo->guardar($datos, $negocioId);
    $statusMsg = $guardado ? 'success' : 'error';
}

header('Location: ../index.php?page=gestion_catalogo&negocio_id=' . $negocioId . '&status=' . $statusMsg);
exit;