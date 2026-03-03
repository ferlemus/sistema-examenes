<?php
require '../includes/auth.php';
verificarLogin();
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Inicio</title>
	<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
	<div class="container">
		<div class="page-header">
			<div>
				<h1 class="page-title">Bienvenido <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></h1>
				<p class="page-subtitle">Listo para comenzar tu examen.</p>
			</div>
			<div class="actions">
				<a class="btn btn-secondary" href="../logout.php">Cerrar sesion</a>
			</div>
		</div>

		<div class="card">
			<div class="actions">
				<a class="btn" href="simulador.php?nuevo=1">Iniciar simulador</a>
			</div>
		</div>
	</div>
</body>
</html>