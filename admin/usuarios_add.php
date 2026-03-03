<?php
require '../config/database.php';
require '../includes/auth.php';
require '../includes/csrf.php';
require '../includes/sanitize.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validarToken($_POST['csrf'])) {
    $nombre = clean_input($_POST['nombre']);
    $email = clean_input($_POST['email']);
    $pass = password_hash(clean_input($_POST['password']), PASSWORD_DEFAULT);
    $rol = clean_input($_POST['rol']);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $email, $pass, $rol]);

    header("Location: usuarios.php");
}

$token = generarToken();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agregar usuario</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Nuevo usuario</h1>
                <p class="page-subtitle">Crea una nueva cuenta de acceso.</p>
            </div>
            <div class="nav">
                <a href="usuarios.php">Volver</a>
                <a href="dashboard.php">Panel</a>
            </div>
        </div>

        <div class="card">
            <form method="POST" class="stack">
                <input type="hidden" name="csrf" value="<?= $token ?>">
                <input name="nombre" required placeholder="Nombre">
                <input name="email" type="email" required placeholder="Email">
                <input name="password" type="password" required placeholder="Contraseña">
                <select name="rol">
                    <option value="estudiante">Estudiante</option>
                    <option value="administrador">Administrador</option>
                </select>
                <div class="actions">
                    <button class="btn" type="submit">Crear</button>
                    <a class="btn btn-secondary" href="usuarios.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>