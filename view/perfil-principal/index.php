<?php
// Redirige a la vista del perfil dentro del panel (index.php?page=perfil)
require_once __DIR__ . '/../../config/base.php';
header('Location: ' . app_url('index.php?page=perfil'));
exit;