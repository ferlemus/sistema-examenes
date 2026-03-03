<?php
require '../config/database.php';
require '../includes/auth.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_pregunta'])) {
    $id = intval($_POST['id_pregunta']);
    // Eliminar respuestas asociadas
    $pdo->prepare("DELETE FROM respuestas_estudiantes WHERE id_pregunta = ?")->execute([$id]);
    // Eliminar pregunta
    $pdo->prepare("DELETE FROM preguntas WHERE id_pregunta = ?")->execute([$id]);
}
header('Location: preguntas.php');
exit;
