<?php
// controller/NegocioController.php

require_once '../model/NegocioModel.php';
require_once '../model/ServicioModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Requiere sesión iniciada
session_start();
if (empty($_SESSION['id_usuario'])) {
    header("Location: ../index.php?page=home");
    exit;
}
$usuarioId = (int) $_SESSION['id_usuario'];

$negocioModel = new NegocioModel();

// --- ACCIÓN: eliminar una foto de la galería ---
if (($_POST['action'] ?? '') === 'eliminar_foto') {
    $fotoId = (int) ($_POST['foto_id'] ?? 0);
    $negocioId = (int) ($_POST['negocio_id'] ?? 0);
    $ok = $fotoId > 0 && $negocioModel->eliminarFoto($fotoId, $usuarioId);
    header("Location: ../index.php?page=perfil&negocio_id=" . $negocioId . "&galeria=" . ($ok ? 'ok' : 'error'));
    exit;
}

// --- ACCIÓN: pausar (ocultar) o activar (mostrar) la visibilidad del negocio ---
if (($_POST['action'] ?? '') === 'pausar_negocio' || ($_POST['action'] ?? '') === 'activar_negocio') {
    $negocioId = (int) ($_POST['negocio_id'] ?? 0);
    $nuevoEstado = ($_POST['action'] ?? '') === 'pausar_negocio' ? 'inactivo' : 'activo';
    $ok = $negocioId > 0 && $negocioModel->cambiarEstado($negocioId, $usuarioId, $nuevoEstado);
    header("Location: ../index.php?page=perfil&negocio_id=" . $negocioId . "&visible=" . (($ok && $nuevoEstado === 'activo') ? 'activo' : ($ok ? 'pausa' : 'error')));
    exit;
}

// --- ACCIÓN: eliminar un negocio completo (con servicios, categorías y fotos) ---
if (($_POST['action'] ?? '') === 'eliminar_negocio') {
    $negocioId = (int) ($_POST['negocio_id'] ?? 0);

    // Busca los archivos antes de borrar para limpiarlos del disco
    $negocioAEliminar = $negocioId > 0 ? $negocioModel->obtenerPorIdYUsuario($negocioId, $usuarioId) : null;
    $fotosAEliminar = $negocioAEliminar ? $negocioModel->obtenerFotos($negocioId) : [];
    $serviciosDelNegocio = $negocioAEliminar ? (new ServicioModel())->obtenerPorNegocio($negocioId) : [];

    $ok = $negocioAEliminar !== null && $negocioModel->eliminar($negocioId, $usuarioId);

    if ($ok) {
        $raizProyecto = dirname(__DIR__);
        $archivos = array_merge(
            [$negocioAEliminar['logo'] ?? null],
            array_column($fotosAEliminar, 'imagen'),
            array_column($serviciosDelNegocio, 'imagen')
        );
        foreach ($archivos as $archivo) {
            if (is_string($archivo) && $archivo !== '' && strpos($archivo, 'uploads/') === 0) {
                $ruta = $raizProyecto . '/' . $archivo;
                if (is_file($ruta)) {
                    @unlink($ruta);
                }
            }
        }
    }

    header('Location: ../index.php?page=home&borrado=' . ($ok ? 'ok' : 'error'));
    exit;
}

// 1. Sanitizar y recibir datos del formulario
$negocioId = (int) ($_POST['negocio_id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$wsp = trim($_POST['wsp'] ?? '');
$correoIngresado = trim($_POST['correo'] ?? '');
$correo = $correoIngresado === '' ? null : filter_var($correoIngresado, FILTER_VALIDATE_EMAIL);
$telefono = trim($_POST['telefono'] ?? '');
$ubicacion = trim($_POST['ubicacion'] ?? '');
$horario = trim($_POST['horario'] ?? '');

// 2. Validaciones básicas
if (empty($nombre) || empty($categoria) || ($correoIngresado !== '' && $correo === false)) {
    header("Location: ../index.php?page=perfil&negocio_id=" . $negocioId . "&error=campos_requeridos");
    exit;
}

// 3. Manejo de la subida del Logo
$rutaLogo = null;
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $nombreArchivo = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['logo']['name']));
    $directorioDestino = '../uploads/logos/';

    if (!is_dir($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    $rutaFinal = $directorioDestino . $nombreArchivo;
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $rutaFinal)) {
        $rutaLogo = 'uploads/logos/' . $nombreArchivo;
    }
}

// 4. Instanciar el modelo y guardar (crear o actualizar según corresponda)
if ($negocioId > 0) {
    $miNegocio = $negocioModel->obtenerPorIdYUsuario($negocioId, $usuarioId);
    if ($miNegocio) {
        $resultado = $negocioModel->actualizar($negocioId, [
            'nombre' => $nombre,
            'categoria' => $categoria,
            'descripcion' => $descripcion,
            'wsp' => $wsp,
            'correo' => $correo,
            'telefono' => $telefono,
            'ubicacion' => $ubicacion,
            'horario' => $horario === '' ? null : $horario,
            'logo' => $rutaLogo
        ]);
    } else {
        $resultado = false;
    }
} else {
    $negocioId = $negocioModel->crear([
        'nombre' => $nombre,
        'categoria' => $categoria,
        'descripcion' => $descripcion,
        'wsp' => $wsp,
        'correo' => $correo,
        'telefono' => $telefono,
        'ubicacion' => $ubicacion,
        'horario' => $horario === '' ? null : $horario,
        'logo' => $rutaLogo
    ], $usuarioId);
    $resultado = $negocioId !== false;
}

// 5. Subir las fotos de la galería (si el negocio quedó guardado)
if ($resultado && $negocioId > 0) {
    $fotosSubidas = subirFotosGaleria();
    if (!empty($fotosSubidas)) {
        $negocioModel->guardarFotos($negocioId, $fotosSubidas);
    }
}

// 6. Responder / Redireccionar al negocio guardado
$destinoNegocio = $negocioId > 0 ? '&negocio_id=' . $negocioId : '';
if ($resultado) {
    header("Location: ../index.php?page=perfil&status=success" . $destinoNegocio);
} else {
    header("Location: ../index.php?page=perfil&status=error" . $destinoNegocio);
}
exit;

// Sube los archivos del input múltiple 'fotos[]' y devuelve sus rutas relativas
function subirFotosGaleria(): array {
    if (empty($_FILES['fotos'])) return [];
    $archivos = $_FILES['fotos'];

    // Normaliza a arreglo (tanto un único archivo como múltiples)
    $nombres = is_array($archivos['name']) ? $archivos['name'] : [$archivos['name']];
    $tmp = is_array($archivos['tmp_name']) ? $archivos['tmp_name'] : [$archivos['tmp_name']];
    $errores = is_array($archivos['error']) ? $archivos['error'] : [$archivos['error']];
    $tipos = is_array($archivos['type']) ? $archivos['type'] : [$archivos['type']];

    $directorio = '../uploads/galeria/';
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $rutas = [];
    $permitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $base = time();

    foreach ($nombres as $i => $nombre) {
        if (($errores[$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) continue;
        if (!in_array($tipos[$i] ?? '', $permitidos, true)) continue;

        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        $ext = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? $ext : 'jpg';
        $archivoFinal = $base . '_' . $i . '.' . $ext;

        if (move_uploaded_file($tmp[$i], $directorio . $archivoFinal)) {
            $rutas[] = 'uploads/galeria/' . $archivoFinal;
        }
    }

    return $rutas;
}