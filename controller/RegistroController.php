<?php
// controller/RegistroController.php

session_start();
require_once __DIR__ . '/../model/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../view/login/registro.php');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$password = $_POST['password'] ?? '';
$confirmacion = $_POST['password2'] ?? '';

$volver = '../view/login/registro.php?error=';

// Validaciones
if ($nombre === '' || $correo === '') {
    header('Location: ' . $volver . 'campos');
    exit;
}
if (filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
    header('Location: ' . $volver . 'correo');
    exit;
}
if (strlen($password) < 6) {
    header('Location: ' . $volver . 'clave');
    exit;
}
if ($password !== $confirmacion) {
    header('Location: ' . $volver . 'confirmar');
    exit;
}

$usuarioModel = new Usuario();

if ($usuarioModel->correoExiste($correo)) {
    header('Location: ' . $volver . 'existente');
    exit;
}

$nuevoId = $usuarioModel->registrar($nombre, $correo, $password);

if ($nuevoId === false) {
    header('Location: ' . $volver . 'bd');
    exit;
}

// Inicia sesión automáticamente
$_SESSION['usuario'] = true;
$_SESSION['id_usuario'] = $nuevoId;
$_SESSION['usuario_nombre'] = $nombre;
$_SESSION['usuario_email'] = $correo;

header('Location: ../index.php?page=home&nuevo=1');
exit;