<?php

session_start();

$tempo_limite = 1800; // 30 * 60 = 1800 segundos = 30 minutos

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $tempo_limite)) {
    session_unset();
    session_destroy();
}

$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['tipo_usuario'])) {
    header('Location: userLogin.php');
    exit;
}

?>