<?php
require 'includes/auth.php';
verificarLogin();

if (esAdmin()) {
    header("Location: admin/dashboard.php");
} else {
    header("Location: estudiante/inicio.php");
}