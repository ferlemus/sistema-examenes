<?php
require '../config/database.php';
require '../includes/auth.php';
require '../includes/functions.php';

verificarEstudiante();

$idExamen = (int) ($_GET['id'] ?? 0);
if ($idExamen <= 0) {
	http_response_code(400);
	exit('Examen inválido');
}

$stmtExamen = $pdo->prepare('SELECT id_usuario, puntaje, fecha_inicio, fecha_fin FROM examenes WHERE id_examen = ? LIMIT 1');
$stmtExamen->execute([$idExamen]);
$examen = $stmtExamen->fetch(PDO::FETCH_ASSOC);

if (!$examen) {
	http_response_code(404);
	exit('Examen no encontrado');
}

if ((int) $examen['id_usuario'] !== (int) $_SESSION['usuario']['id']) {
	http_response_code(403);
	exit('Acceso denegado');
}

$resultados = calcularResultados($pdo, $idExamen);
$puntaje = isset($examen['puntaje']) ? (float) $examen['puntaje'] : (float) $resultados['puntaje'];
$fechaInicio = $examen['fecha_inicio'] ?? null;
$fechaFin = $examen['fecha_fin'] ?? null;

$tiempoTotalTexto = 'N/A';
if ($fechaInicio && $fechaFin) {
	$inicio = strtotime($fechaInicio);
	$fin = strtotime($fechaFin);
	if ($inicio !== false && $fin !== false && $fin >= $inicio) {
		$segundos = $fin - $inicio;
		$horas = floor($segundos / 3600);
		$minutos = floor(($segundos % 3600) / 60);
		$seg = $segundos % 60;
		$tiempoTotalTexto = sprintf('%02d:%02d:%02d', $horas, $minutos, $seg);
	}
}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Resultado</title>
	<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
	<div class="container">
		<div class="page-header">
			<div>
				<h1 class="page-title">Resultado</h1>
				<p class="page-subtitle">Resumen de tu examen.</p>
			</div>
			<div class="actions">
				<a class="btn btn-secondary" href="inicio.php">Regresar</a>
			</div>
		</div>

		<div class="card">
			<h2 class="title-medium">Tu puntaje: <?= htmlspecialchars((string) number_format($puntaje, 2)) ?>%</h2>
			<div style="font-size:18px;color:#0f766e;margin-top:8px;">Respuestas correctas: <b><?= (int) $resultados['correctas'] ?></b> de <b><?= (int) $resultados['total'] ?></b></div>
			<div style="font-size:16px;color:#5b5b5b;margin-top:6px;">Tiempo total: <b><?= htmlspecialchars($tiempoTotalTexto) ?></b></div>
		</div>

		<div class="card">
			<h3 class="title-small">Resumen por área</h3>
			<div class="table-wrap">
				<table class="table">
					<tr>
						<th>Materia</th>
						<th>Total</th>
						<th>Aciertos</th>
						<th>Porcentaje</th>
					</tr>
					<?php foreach ($resultados['materias'] as $materia): ?>
						<tr>
							<td><?= htmlspecialchars((string) $materia['materia']) ?></td>
							<td><?= (int) $materia['total'] ?></td>
							<td><?= (int) $materia['correctas'] ?></td>
							<td><?= htmlspecialchars((string) number_format((float) $materia['porcentaje'], 2)) ?>%</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>

		<div class="card">
			<h3 class="title-small">Detalle de preguntas</h3>
			<div class="table-wrap">
				<table class="table">
					<tr>
						<th>#</th>
						<th>Materia</th>
						<th>Pregunta</th>
						<th>Tu respuesta</th>
						<th>Respuesta correcta</th>
						<th>Resultado</th>
					</tr>
					<?php
					$stmt = $pdo->prepare('SELECT
							r.id_pregunta,
							r.respuesta_seleccionada,
							p.materia,
							p.enunciado,
							p.respuesta_correcta,
							p.opcion_a,
							p.opcion_b,
							p.opcion_c,
							p.opcion_d
						FROM respuestas_estudiantes r
						INNER JOIN preguntas p ON r.id_pregunta = p.id_pregunta
						WHERE r.id_examen = ?
						ORDER BY r.id_respuesta ASC');
					$stmt->execute([$idExamen]);
					$preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$letras = ['A' => 'opcion_a', 'B' => 'opcion_b', 'C' => 'opcion_c', 'D' => 'opcion_d'];
					$num = 1;
					foreach ($preguntas as $p):
						$respSel = $letras[$p['respuesta_seleccionada']] ?? '';
						$respCorr = $letras[$p['respuesta_correcta']] ?? '';
						$txtSel = $respSel !== '' ? ($p[$respSel] ?? 'Sin respuesta') : 'Sin respuesta';
						$txtCorr = $respCorr !== '' ? ($p[$respCorr] ?? '') : '';
						$esCorrecta = ($p['respuesta_seleccionada'] !== null && $p['respuesta_seleccionada'] === $p['respuesta_correcta']);
					?>
						<tr>
							<td><?= $num++ ?></td>
							<td><?= htmlspecialchars((string) $p['materia']) ?></td>
							<td><?= htmlspecialchars((string) $p['enunciado']) ?></td>
							<td><?= htmlspecialchars((string) $txtSel) ?> <span style="color:#0f766e;font-size:12px">(<?= htmlspecialchars((string) ($p['respuesta_seleccionada'] ?? '-')) ?>)</span></td>
							<td><?= htmlspecialchars((string) $txtCorr) ?> <span style="color:#0f766e;font-size:12px">(<?= htmlspecialchars((string) $p['respuesta_correcta']) ?>)</span></td>
							<td>
								<?php if ($esCorrecta): ?>
									<span style="color:green">✔ Correcta</span>
								<?php else: ?>
									<span style="color:red">✘ Incorrecta</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>