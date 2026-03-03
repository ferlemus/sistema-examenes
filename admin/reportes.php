<?php
require '../config/database.php';
require '../includes/auth.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

$stmt = $pdo->query("
        SELECT e.id_examen, u.nombre, e.total_preguntas, e.puntaje, e.fecha_inicio, e.fecha_fin,
            TIMESTAMPDIFF(SECOND, e.fecha_inicio, e.fecha_fin) AS tiempo_segundos
        FROM examenes e
        JOIN usuarios u ON u.id_usuario = e.id_usuario
        ORDER BY e.fecha_fin DESC
");
$datos = $stmt->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reportes</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Reportes de examenes</h1>
                <p class="page-subtitle">Consulta el historial de resultados.</p>
            </div>
            <div class="nav">
                <a href="dashboard.php">Panel</a>
                <a href="usuarios.php">Usuarios</a>
                <a href="preguntas.php">Preguntas</a>
                <a href="../logout.php">Salir</a>
            </div>
        </div>

        <?php if (!empty($_GET['msg'])): ?>
            <div class="alert" style="margin-bottom:18px;"> <?= htmlspecialchars($_GET['msg']) ?> </div>
        <?php endif; ?>
        <div class="card">
            <form method="GET" class="actions" style="margin-bottom: 18px;">
                <input name="filtro_alumno" placeholder="Alumno" value="<?= htmlspecialchars($_GET['filtro_alumno'] ?? '') ?>">
                <input name="filtro_fecha" placeholder="Fecha (YYYY-MM-DD)" value="<?= htmlspecialchars($_GET['filtro_fecha'] ?? '') ?>">
                <button class="btn btn-secondary" type="submit">Filtrar</button>
                <a class="btn" href="reportes.php">Limpiar</a>
            </form>
            <div class="table-wrap">
                <table class="table">
                    <tr><th>Alumno</th><th>Total</th><th>Puntaje</th><th>Fecha</th><th>Tiempo</th><th>Detalle</th><th>Eliminar</th></tr>
                    <?php
                    $datosFiltrados = $datos;
                    if (!empty($_GET['filtro_alumno'])) {
                        $datosFiltrados = array_filter($datosFiltrados, function($d) {
                            return stripos($d['nombre'], $_GET['filtro_alumno']) !== false;
                        });
                    }
                    if (!empty($_GET['filtro_fecha'])) {
                        $datosFiltrados = array_filter($datosFiltrados, function($d) {
                            return strpos($d['fecha_fin'], $_GET['filtro_fecha']) !== false;
                        });
                    }
                    foreach ($datosFiltrados as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nombre']) ?></td>
                        <td><?= $d['total_preguntas'] ?></td>
                        <td><?= $d['puntaje'] ?>%</td>
                        <td>
                            <?php
                            if (!empty($d['fecha_fin'])) {
                                echo htmlspecialchars($d['fecha_fin']);
                            } else {
                                echo '<span style="color:#b91c1c">No finalizado</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (!empty($d['fecha_fin'])) {
                                $seg = (int)$d['tiempo_segundos'];
                                if ($seg > 0) {
                                    $min = floor($seg/60);
                                    $s = $seg%60;
                                    echo $min.'m '.str_pad($s,2,'0',STR_PAD_LEFT).'s';
                                } else {
                                    echo '-';
                                }
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><a class="btn btn-secondary" href="reportes_detalle.php?id=<?= $d['id_examen'] ?>">Ver</a></td>
                        <td>
                            <?php if (empty($d['fecha_fin'])): ?>
                                <form method="POST" action="eliminar_examen.php" onsubmit="return confirm('¿Seguro que deseas eliminar este examen no finalizado?');" style="display:inline;">
                                    <input type="hidden" name="id_examen" value="<?= $d['id_examen'] ?>">
                                    <button class="btn btn-secondary" type="submit">Eliminar</button>
                                </form>
                            <?php else: ?>
                                -
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