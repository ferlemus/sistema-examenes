<?php
require '../config/database.php';
require '../includes/auth.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

$id_examen = intval($_GET['id'] ?? 0);

$info = $pdo->prepare("
    SELECT u.nombre, e.fecha_inicio, e.fecha_fin, e.puntaje, e.total_preguntas
    FROM examenes e
    JOIN usuarios u ON u.id_usuario = e.id_usuario
    WHERE e.id_examen = ?
");
$info->execute([$id_examen]);
$examen = $info->fetch(PDO::FETCH_ASSOC);

$detalle = $pdo->prepare("
        SELECT p.id_pregunta, p.materia, p.enunciado, p.opcion_a, p.opcion_b, p.opcion_c, p.opcion_d,
            p.respuesta_correcta, r.respuesta_seleccionada, r.es_correcta
    FROM respuestas_estudiantes r
    JOIN preguntas p ON p.id_pregunta = r.id_pregunta
    WHERE r.id_examen = ?
    ORDER BY r.id_respuesta ASC
");
$detalle->execute([$id_examen]);
$preguntas = $detalle->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle del examen</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Detalle del examen</h1>
                <p class="page-subtitle">Respuestas del alumno y solucion correcta.</p>
            </div>
            <div class="nav">
                <a href="reportes.php">Volver</a>
                <a href="dashboard.php">Panel</a>
            </div>
        </div>

        <?php if (!$examen): ?>
            <div class="card">
                <p>Examen no encontrado.</p>
                <a class="btn btn-secondary" href="reportes.php">Regresar</a>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="stack">
                    <p><strong>Alumno:</strong> <?= htmlspecialchars($examen['nombre']) ?></p>
                    <p><strong>Fecha inicio:</strong> <?= htmlspecialchars($examen['fecha_inicio']) ?></p>
                    <p><strong>Fecha fin:</strong> <?= htmlspecialchars($examen['fecha_fin']) ?></p>
                    <p><strong>Total preguntas:</strong> <?= $examen['total_preguntas'] ?></p>
                    <p><strong>Puntaje:</strong> <?= $examen['puntaje'] ?>%</p>
                    <?php
                    $tiempo = '-';
                    if (!empty($examen['fecha_inicio']) && !empty($examen['fecha_fin'])) {
                        $ini = strtotime($examen['fecha_inicio']);
                        $fin = strtotime($examen['fecha_fin']);
                        if ($ini && $fin && $fin > $ini) {
                            $seg = $fin - $ini;
                            $min = floor($seg/60);
                            $s = $seg%60;
                            $tiempo = $min.'m '.str_pad($s,2,'0',STR_PAD_LEFT).'s';
                        }
                    }
                    ?>
                    <p><strong>Tiempo usado:</strong> <?= $tiempo ?></p>
                </div>
            </div>

            <div class="card">
                <form method="GET" class="actions" style="margin-bottom: 18px;">
                    <input name="filtro_id" placeholder="ID pregunta" value="<?= htmlspecialchars($_GET['filtro_id'] ?? '') ?>">
                    <input name="filtro_materia" placeholder="Materia" value="<?= htmlspecialchars($_GET['filtro_materia'] ?? '') ?>">
                    <button class="btn btn-secondary" type="submit">Filtrar</button>
                    <a class="btn" href="reportes_detalle.php?id=<?= $id_examen ?>">Limpiar</a>
                </form>
                <div class="table-wrap">
                    <table class="table">
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Materia</th>
                            <th>Pregunta</th>
                            <th>Respuesta del alumno</th>
                            <th>Respuesta correcta</th>
                            <th>¿Correcta?</th>
                            <th>Accion</th>
                        </tr>
                        <?php $i = 1; ?>
                        <?php
                        // Filtros
                        $pregFiltradas = $preguntas;
                        if (!empty($_GET['filtro_id'])) {
                            $pregFiltradas = array_filter($pregFiltradas, function($p) {
                                return $p['id_pregunta'] == $_GET['filtro_id'];
                            });
                        }
                        if (!empty($_GET['filtro_materia'])) {
                            $pregFiltradas = array_filter($pregFiltradas, function($p) {
                                return stripos($p['materia'], $_GET['filtro_materia']) !== false;
                            });
                        }
                        foreach ($pregFiltradas as $p):
                            $opciones = [
                                'A' => $p['opcion_a'],
                                'B' => $p['opcion_b'],
                                'C' => $p['opcion_c'],
                                'D' => $p['opcion_d'],
                            ];
                            $respAlumno = $p['respuesta_seleccionada'];
                            $respCorrecta = $p['respuesta_correcta'];
                            $textoAlumno = $respAlumno && isset($opciones[$respAlumno])
                                ? $respAlumno . ' - ' . $opciones[$respAlumno]
                                : ($respAlumno ?: 'Sin respuesta');
                            $textoCorrecta = $respCorrecta && isset($opciones[$respCorrecta])
                                ? $respCorrecta . ' - ' . $opciones[$respCorrecta]
                                : ($respCorrecta ?: 'Sin respuesta');
                            $clase = $p['es_correcta'] ? 'correcta' : 'incorrecta';
                            ?>
                            <tr class="<?= $clase ?>">
                                <td><?= $i++ ?></td>
                                <td><?= $p['id_pregunta'] ?></td>
                                <td><?= htmlspecialchars($p['materia']) ?></td>
                                <td><?= htmlspecialchars($p['enunciado']) ?></td>
                                <td><?= htmlspecialchars($textoAlumno) ?></td>
                                <td><?= htmlspecialchars($textoCorrecta) ?></td>
                                <td><?= $p['es_correcta'] ? 'Si' : 'No' ?></td>
                                <td>
                                    <a class="btn btn-secondary" href="preguntas_edit.php?id=<?= $p['id_pregunta'] ?>">Editar</a>
                                    <?php
                                    // Estadísticas de uso
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM respuestas_estudiantes WHERE id_pregunta = ?");
                                    $stmt->execute([$p['id_pregunta']]);
                                    $total = $stmt->fetchColumn();
                                    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM respuestas_estudiantes WHERE id_pregunta = ? AND es_correcta = 1");
                                    $stmt2->execute([$p['id_pregunta']]);
                                    $aciertos = $stmt2->fetchColumn();
                                    if ($total > 0) {
                                        $porc = round(($aciertos/$total)*100,1);
                                        echo "<span style='margin-left:8px;font-size:12px;color:#0f766e'>Usada $total veces, $porc% aciertos</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
