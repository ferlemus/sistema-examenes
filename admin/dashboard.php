<?php
require '../includes/auth.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Administracion</title>
	<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
	<div class="container">
		<div class="page-header">
			<div>
				<h1 class="page-title">Administracion</h1>
				<p class="page-subtitle">Panel de control para administrar el examen.</p>
			</div>
			<div class="actions">
				<a class="btn btn-secondary" href="../logout.php">Salir</a>
			</div>
		</div>
		<div class="card">
			<div class="actions">
				<a class="btn" href="usuarios.php">Usuarios</a>
				<a class="btn" href="preguntas.php">Preguntas</a>
				<a class="btn" href="examenes.php">Exámenes</a>
				<a class="btn" href="reportes.php">Reportes</a>
			</div>
		</div>
	</div>
</body>
</html>