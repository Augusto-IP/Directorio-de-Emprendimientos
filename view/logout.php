<?php
session_start();
session_unset();
session_destroy();

// Redirige de vuelta al login
header('Location: login/login.php');
exit();