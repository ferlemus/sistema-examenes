<?php
require '../config/database.php';
require '../includes/auth.php';
require '../includes/csrf.php';
require '../includes/functions.php';

verificarEstudiante();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

if (!validarToken($_POST['csrf'] ?? '')) {
    http_response_code(400);
    exit('Solicitud inválida');
}

$idExamen = (int) ($_POST['id_examen'] ?? 0);
if ($idExamen <= 0 || !isset($_SESSION['simulador'][$idExamen])) {
    http_response_code(400);
    exit('Intento de examen inválido');
}

$intento = $_SESSION['simulador'][$idExamen];
$idUsuario = (int) $_SESSION['usuario']['id'];

if ((int) ($intento['id_usuario'] ?? 0) !== $idUsuario) {
    http_response_code(403);
    exit('Acceso denegado');
}

$preguntasIntento = $intento['preguntas'] ?? [];
$respuestasIntento = $intento['respuestas'] ?? [];
$inicioTs = (int) ($intento['inicio_ts'] ?? time());
$finTs = time();
$tiempoTotal = max(0, $finTs - $inicioTs);

if (empty($preguntasIntento)) {
    http_response_code(400);
    exit('No hay preguntas asociadas al intento');
}

try {
    $pdo->beginTransaction();

    $stmtPropietario = $pdo->prepare('SELECT id_usuario FROM examenes WHERE id_examen = ? LIMIT 1');
    $stmtPropietario->execute([$idExamen]);
    $idUsuarioExamen = (int) $stmtPropietario->fetchColumn();

    if ($idUsuarioExamen !== $idUsuario) {
        throw new RuntimeException('El examen no pertenece al usuario autenticado.');
    }

    $idsPreguntas = array_map(static fn(array $pregunta): int => (int) $pregunta['id_pregunta'], $preguntasIntento);
    $idsPreguntas = array_values(array_unique($idsPreguntas));

    $placeholders = implode(',', array_fill(0, count($idsPreguntas), '?'));
    $stmtCorrectas = $pdo->prepare("SELECT id_pregunta, respuesta_correcta FROM preguntas WHERE id_pregunta IN ({$placeholders})");
    $stmtCorrectas->execute($idsPreguntas);

    $correctasPorPregunta = [];
    foreach ($stmtCorrectas->fetchAll(PDO::FETCH_ASSOC) as $fila) {
        $correctasPorPregunta[(int) $fila['id_pregunta']] = (string) $fila['respuesta_correcta'];
    }

    $stmtLimpiar = $pdo->prepare('DELETE FROM respuestas_estudiantes WHERE id_examen = ?');
    $stmtLimpiar->execute([$idExamen]);

    $stmtInsertar = $pdo->prepare(
        'INSERT INTO respuestas_estudiantes (id_examen, id_pregunta, respuesta_seleccionada, es_correcta, tiempo_respuesta)
         VALUES (?, ?, ?, ?, ?)'
    );

    foreach ($idsPreguntas as $idPregunta) {
        if (!isset($correctasPorPregunta[$idPregunta])) {
            throw new RuntimeException('Se detectó una pregunta no válida en el intento.');
        }

        $respuestaSeleccionada = $respuestasIntento[$idPregunta] ?? null;
        if (!in_array($respuestaSeleccionada, ['A', 'B', 'C', 'D'], true)) {
            $respuestaSeleccionada = null;
        }

        $esCorrecta = ($respuestaSeleccionada !== null && $respuestaSeleccionada === $correctasPorPregunta[$idPregunta]) ? 1 : 0;
        $stmtInsertar->execute([$idExamen, $idPregunta, $respuestaSeleccionada, $esCorrecta, 0]);
    }

    $resultados = calcularResultados($pdo, $idExamen);
    $puntaje = (float) $resultados['puntaje'];

    try {
        $stmtUpdate = $pdo->prepare(
            'UPDATE examenes
             SET fecha_fin = NOW(), puntaje = ?, tiempo_total = ?
             WHERE id_examen = ?'
        );
        $stmtUpdate->execute([$puntaje, $tiempoTotal, $idExamen]);
    } catch (PDOException $e) {
        $stmtUpdate = $pdo->prepare(
            'UPDATE examenes
             SET fecha_fin = NOW(), puntaje = ?
             WHERE id_examen = ?'
        );
        $stmtUpdate->execute([$puntaje, $idExamen]);
    }

    $pdo->commit();
    unset($_SESSION['simulador'][$idExamen]);

    header('Location: resultado.php?id=' . $idExamen);
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    exit('No fue posible guardar el examen: ' . htmlspecialchars($e->getMessage()));
}