<?php
if (!isset($_SESSION)) session_start();

function generarToken() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function validarToken($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}