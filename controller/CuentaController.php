<?php
// controller/CuentaController.php
// Actualiza los datos de la cuenta (nombre, correo) y la contraseña

require_once '../model/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

session_start();
if (empty($_SESSION['id_usuario'])) {
    header('Location: ../index.php?page=home');
    exit;
}
$usuarioId = (int) $_SESSION['id_usuario'];
$usuarioModel = new Usuario();

$accion = $_POST['action'] ?? 'datos';
$resultado = false;

if ($accion === 'datos') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    // Validar correo
    if ($nombre !== '' && filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        // Si cambió el correo, comprobar que no esté en uso por otro usuario
        $usuarioActual = $usuarioModel->obtenerPorId($usuarioId);
        if ($usuarioActual && $usuarioActual['correo'] !== $correo && $usuarioModel->correoExiste($correo)) {
            header('Location: ../index.php?page=cuenta&error=correo_en_uso');
            exit;
        }
        $resultado = $usuarioModel->actualizarDatos($usuarioId, $nombre, $correo);
        if ($resultado) {
            $_SESSION['nombre_usuario'] = $nombre;
            $_SESSION['correo_usuario'] = $correo;
        }
    }
    header('Location: ../index.php?page=cuenta&cuenta=' . ($resultado ? 'ok' : 'error'));
    exit;
}

if ($accion === 'password') {
    $actual = $_POST['password_actual'] ?? '';
    $nueva = $_POST['password_nueva'] ?? '';
    $confirma = $_POST['password_confirma'] ?? '';

    $usuario = $usuarioModel->obtenerPorId($usuarioId);
    $actualOk = $usuario !== null
        && ($actual === ($usuario['password'] ?? '') || ($actual !== '' && password_verify($actual, $usuario['password'] ?? '')));
    $coinciden = $nueva !== '' && $nueva === $confirma;

    if ($actualOk && $coinciden) {
        $resultado = $usuarioModel->cambiarPassword($usuarioId, $nueva);
    }
    header('Location: ../index.php?page=cuenta&clave=' . ($resultado ? 'ok' : 'error'));
    exit;
}

header('Location: ../index.php?page=cuenta');
exit;