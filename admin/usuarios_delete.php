<?php
require '../config/database.php';
require '../includes/auth.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_usuario'])) {
    $id = intval($_POST['id_usuario']);
    // Eliminar exámenes y respuestas asociadas
    $stmt = $pdo->prepare("SELECT id_examen FROM examenes WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $examenes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($examenes as $ex) {
        $pdo->prepare("DELETE FROM respuestas_estudiantes WHERE id_examen = ?")->execute([$ex]);
        $pdo->prepare("DELETE FROM examenes WHERE id_examen = ?")->execute([$ex]);
    }
    // Eliminar usuario
    $pdo->prepare("DELETE FROM usuarios WHERE id_usuario = ?")->execute([$id]);
}
header('Location: usuarios.php');
exit;
