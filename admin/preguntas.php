<?php
require '../config/database.php';
require '../includes/auth.php';
require '../includes/csrf.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

$preg = $pdo->query("SELECT * FROM preguntas")->fetchAll();
$token = generarToken();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preguntas</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Preguntas</h1>
                <p class="page-subtitle">Gestiona el banco de preguntas.</p>
            </div>
            <div class="nav">
                <a href="dashboard.php">Panel</a>
                <a href="usuarios.php">Usuarios</a>
                <a href="reportes.php">Reportes</a>
                <a href="../logout.php">Salir</a>
            </div>
        </div>

        <div class="card">
            <form method="GET" class="actions" style="margin-bottom: 18px;">
                <input name="filtro_materia" placeholder="Materia" value="<?= htmlspecialchars($_GET['filtro_materia'] ?? '') ?>">
                <input name="filtro_id" placeholder="ID pregunta" value="<?= htmlspecialchars($_GET['filtro_id'] ?? '') ?>">
                <input name="filtro_texto" placeholder="Texto de la pregunta" value="<?= htmlspecialchars($_GET['filtro_texto'] ?? '') ?>">
                <button class="btn btn-secondary" type="submit">Filtrar</button>
                <a class="btn" href="preguntas.php">Limpiar</a>
            </form>
            <div class="card-header">
                <h2 class="title-small">Listado</h2>
                <a class="btn" href="preguntas_add.php">Agregar pregunta</a>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <tr><th>ID</th><th>Materia</th><th>Pregunta</th><th>Acciones</th></tr>
                    <?php
                    $pregFiltradas = $preg;
                    if (!empty($_GET['filtro_materia'])) {
                        $pregFiltradas = array_filter($pregFiltradas, function($p) {
                            return stripos($p['materia'], $_GET['filtro_materia']) !== false;
                        });
                    }
                    if (!empty($_GET['filtro_id'])) {
                        $pregFiltradas = array_filter($pregFiltradas, function($p) {
                            return $p['id_pregunta'] == $_GET['filtro_id'];
                        });
                    }
                    if (!empty($_GET['filtro_texto'])) {
                        $pregFiltradas = array_filter($pregFiltradas, function($p) {
                            return stripos($p['enunciado'], $_GET['filtro_texto']) !== false;
                        });
                    }
                    foreach ($pregFiltradas as $p): ?>
                    <tr>
                        <td><?= $p['id_pregunta'] ?></td>
                        <td><?= htmlspecialchars($p['materia']) ?></td>
                        <td><?= htmlspecialchars($p['enunciado']) ?></td>
                        <td>
                            <a class="btn btn-secondary" href="preguntas_edit.php?id=<?= $p['id_pregunta'] ?>">Editar</a>
                            <form method="POST" action="preguntas_delete.php" style="display:inline;" onsubmit="return confirm('¿Eliminar esta pregunta?');">
                                <input type="hidden" name="id_pregunta" value="<?= $p['id_pregunta'] ?>">
                                <button class="btn btn-secondary" type="submit">Eliminar</button>
                            </form>
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
    </div>
</body>
</html>