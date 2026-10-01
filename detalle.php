<?php
// detalle.php — Página pública de un negocio (independiente de la sesión).
// Carga los datos reales registrados en "Perfil del Negocio" y su catálogo.

require_once __DIR__ . '/config/base.php';
require_once __DIR__ . '/model/NegocioModel.php';
require_once __DIR__ . '/model/ServicioModel.php';

$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : 0;

$negocio = $id > 0 ? (new NegocioModel())->obtenerPorId($id) : null;
$servicios = $negocio ? (new ServicioModel())->obtenerPorNegocio((int) $negocio['id']) : [];
$fotos = $negocio ? (new NegocioModel())->obtenerFotos((int) $negocio['id']) : [];

require __DIR__ . '/view/detalle-negocio.php';