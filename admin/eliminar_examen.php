<?php
require '../config/database.php';
require '../includes/auth.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_examen'])) {
    $id = intval($_POST['id_examen']);
    // Solo eliminar si el examen no está finalizado
    $stmt = $pdo->prepare("SELECT fecha_fin FROM examenes WHERE id_examen = ?");
    $stmt->execute([$id]);
    $fin = $stmt->fetchColumn();
    if (empty($fin)) {
        // Eliminar respuestas asociadas primero
        $pdo->prepare("DELETE FROM respuestas_estudiantes WHERE id_examen = ?")->execute([$id]);
        // Eliminar el examen
        $pdo->prepare("DELETE FROM examenes WHERE id_examen = ?")->execute([$id]);
        $msg = 'Examen eliminado correctamente.';
    } else {
        $msg = 'No se puede eliminar un examen finalizado.';
    }
    header('Location: reportes.php?msg=' . urlencode($msg));
    exit;
}
header('Location: reportes.php');
exit;
