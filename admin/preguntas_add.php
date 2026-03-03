<?php
require '../config/database.php';
require '../includes/auth.php';
require '../includes/csrf.php';
require '../includes/sanitize.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validarToken($_POST['csrf'])) {
    $m = clean_input($_POST['materia']);
    $e = clean_input($_POST['enunciado']);
    $a = clean_input($_POST['opcion_a']);
    $b = clean_input($_POST['opcion_b']);
    $c = clean_input($_POST['opcion_c']);
    $d = clean_input($_POST['opcion_d']);
    $rc = clean_input($_POST['respuesta_correcta']);
    $df = clean_input($_POST['dificultad']);

    $sql = "INSERT INTO preguntas VALUES (NULL,?,?,?,?,?,?,?,?)";
    $pdo->prepare($sql)->execute([$m,$e,$a,$b,$c,$d,$rc,$df]);
    header("Location: preguntas.php");
}

$token = generarToken();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agregar pregunta</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Nueva pregunta</h1>
                <p class="page-subtitle">Completa los campos para registrar una pregunta.</p>
            </div>
            <div class="nav">
                <a href="preguntas.php">Volver</a>
                <a href="dashboard.php">Panel</a>
            </div>
        </div>

        <div class="card">
            <form method="POST" class="stack">
                <input type="hidden" name="csrf" value="<?= $token ?>">
                <input name="materia" placeholder="Materia" required>
                <textarea name="enunciado" required placeholder="Pregunta"></textarea>
                <input name="opcion_a" required placeholder="Opción A">
                <input name="opcion_b" required placeholder="Opción B">
                <input name="opcion_c" required placeholder="Opción C">
                <input name="opcion_d" required placeholder="Opción D">
                <select name="respuesta_correcta">
                    <option>A</option><option>B</option><option>C</option><option>D</option>
                </select>
                <select name="dificultad">
                    <option value="facil">Fácil</option>
                    <option value="medio">Medio</option>
                    <option value="dificil">Difícil</option>
                </select>
                <div class="actions">
                    <button class="btn" type="submit">Guardar</button>
                    <a class="btn btn-secondary" href="preguntas.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>