<?php
// controller/ResenaController.php
// Guarda una reseÃ±a pÃºblica de un cliente en la portada del negocio

require_once '../model/ResenaModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$negocioId = (int) ($_POST['negocio_id'] ?? 0);
$nombreC = trim($_POST['cliente_nombre'] ?? '');
$rating = (int) ($_POST['rating'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

$valido = $negocioId > 0
    && $nombreC !== ''
    && $rating >= 1 && $rating <= 5
    && mb_strlen($comentario) >= 3;

if ($valido) {
    $ok = (new ResenaModel())->crear([
        'cliente_nombre' => $nombreC,
        'rating' => $rating,
        'comentario' => $comentario,
    ], $negocioId);
    header('Location: ../detalle.php?id=' . $negocioId . '&resena=' . ($ok ? 'ok' : 'error'));
    exit;
}

header('Location: ../detalle.php?id=' . $negocioId . '&resena=error');
exit;