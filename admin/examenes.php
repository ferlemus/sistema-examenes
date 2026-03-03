<?php
require '../config/database.php';
require '../includes/auth.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

$stmt = $pdo->query("SELECT e.*, u.nombre FROM examenes e JOIN usuarios u ON u.id_usuario = e.id_usuario ORDER BY e.id_examen DESC");
$examenes = $stmt->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD Exámenes</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Exámenes</h1>
                <p class="page-subtitle">Administrar todos los exámenes.</p>
            </div>
            <div class="nav">
                <a href="dashboard.php">Panel</a>
                <a href="reportes.php">Reportes</a>
                <a href="../logout.php">Salir</a>
            </div>
        </div>
        <div class="card">
            <div class="table-wrap">
                <table class="table">
                    <tr>
                        <th>ID</th><th>Alumno</th><th>Inicio</th><th>Fin</th><th>Puntaje</th><th>Total</th><th>Acciones</th>
                    </tr>
                    <?php foreach ($examenes as $e): ?>
                    <tr>
                        <td><?= $e['id_examen'] ?></td>
                        <td><?= htmlspecialchars($e['nombre']) ?></td>
                        <td><?= htmlspecialchars($e['fecha_inicio']) ?></td>
                        <td>
                            <?php if (empty($e['fecha_fin'])): ?>
                                <span style="color:#e11d48;font-weight:600">No terminado</span>
                            <?php else: ?>
                                <?= htmlspecialchars($e['fecha_fin']) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= $e['puntaje'] ?>%</td>
                        <td><?= $e['total_preguntas'] ?></td>
                        <td>
                            <a class="btn btn-secondary" href="reportes_detalle.php?id=<?= $e['id_examen'] ?>">Ver</a>
                            <?php if (empty($e['fecha_fin'])): ?>
                                <form method="POST" action="eliminar_examen.php" style="display:inline;">
                                    <input type="hidden" name="id_examen" value="<?= $e['id_examen'] ?>">
                                    <button class="btn btn-secondary" type="submit" onclick="return confirm('¿Eliminar este examen?')">Eliminar</button>
                                </form>
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
