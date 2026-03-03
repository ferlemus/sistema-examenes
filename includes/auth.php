<?php
session_start();

function obtenerRutaLogin() {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($script, '/estudiante/') !== false || strpos($script, '/admin/') !== false) {
        return '../login.php';
    }
    return 'login.php';
}

function verificarLogin() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: " . obtenerRutaLogin());
        exit;
    }
}

function esAdmin() {
    return $_SESSION['usuario']['rol'] === 'administrador';
}

function esEstudiante() {
    return isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'estudiante';
}

function verificarEstudiante() {
    verificarLogin();
    if (!esEstudiante()) {
        http_response_code(403);
        exit('Acceso denegado');
    }
}