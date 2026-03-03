<?php
require '../config/database.php';
require '../includes/auth.php';
require '../includes/csrf.php';
require '../includes/sanitize.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

$id = intval($_GET['id']);
$p = $pdo->prepare("SELECT * FROM preguntas WHERE id_pregunta = ?");
$p->execute([$id]);
$preg = $p->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validarToken($_POST['csrf'])) {
    $m = clean_input($_POST['materia']);
    $e = clean_input($_POST['enunciado']);
    $a = clean_input($_POST['opcion_a']);
    $b = clean_input($_POST['opcion_b']);
    $c = clean_input($_POST['opcion_c']);
    $d = clean_input($_POST['opcion_d']);
    $rc = clean_input($_POST['respuesta_correcta']);
    $df = clean_input($_POST['dificultad']);

    $sql = "UPDATE preguntas SET materia=?, enunciado=?, opcion_a=?, opcion_b=?, opcion_c=?, opcion_d=?, respuesta_correcta=?, dificultad=? WHERE id_pregunta=?";
    $pdo->prepare($sql)->execute([$m,$e,$a,$b,$c,$d,$rc,$df,$id]);
    header("Location: preguntas.php");
}

$token = generarToken();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar pregunta</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Editar pregunta</h1>
                <p class="page-subtitle">Actualiza los detalles de la pregunta.</p>
            </div>
            <div class="nav">
                <a href="preguntas.php">Volver</a>
                <a href="dashboard.php">Panel</a>
            </div>
        </div>

        <div class="card">
            <form method="POST" class="stack">
                <input type="hidden" name="csrf" value="<?= $token ?>">
                <input name="materia" value="<?= htmlspecialchars($preg['materia']) ?>">
                <textarea name="enunciado"><?= htmlspecialchars($preg['enunciado']) ?></textarea>
                <input name="opcion_a" value="<?= htmlspecialchars($preg['opcion_a']) ?>">
                <input name="opcion_b" value="<?= htmlspecialchars($preg['opcion_b']) ?>">
                <input name="opcion_c" value="<?= htmlspecialchars($preg['opcion_c']) ?>">
                <input name="opcion_d" value="<?= htmlspecialchars($preg['opcion_d']) ?>">
                <select name="respuesta_correcta">
                    <option <?= $preg['respuesta_correcta']=='A'?'selected':'' ?>>A</option>
                    <option <?= $preg['respuesta_correcta']=='B'?'selected':'' ?>>B</option>
                    <option <?= $preg['respuesta_correcta']=='C'?'selected':'' ?>>C</option>
                    <option <?= $preg['respuesta_correcta']=='D'?'selected':'' ?>>D</option>
                </select>
                <select name="dificultad">
                    <option value="facil" <?= $preg['dificultad']=='facil'?'selected':'' ?>>Fácil</option>
                    <option value="medio" <?= $preg['dificultad']=='medio'?'selected':'' ?>>Medio</option>
                    <option value="dificil" <?= $preg['dificultad']=='dificil'?'selected':'' ?>>Difícil</option>
                </select>
                <div class="actions">
                    <button class="btn" type="submit">Actualizar</button>
                    <a class="btn btn-secondary" href="preguntas.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>