<?php
require '../config/database.php';
require '../includes/auth.php';
require '../includes/csrf.php';
require '../includes/sanitize.php';
verificarLogin();
if (!esAdmin()) die("Acceso denegado");

$id = intval($_GET['id']);
$u = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$u->execute([$id]);
$user = $u->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && validarToken($_POST['csrf'])) {
    $nombre = clean_input($_POST['nombre']);
    $email = clean_input($_POST['email']);
    $rol = clean_input($_POST['rol']);
    $sql = "UPDATE usuarios SET nombre=?, email=?, rol=? WHERE id_usuario=?";
    $pdo->prepare($sql)->execute([$nombre, $email, $rol, $id]);
    // Cambio de contraseña si se proporciona
    if (!empty($_POST['password'])) {
        $pass = password_hash(clean_input($_POST['password']), PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE usuarios SET contraseña=? WHERE id_usuario=?")->execute([$pass, $id]);
    }
    header("Location: usuarios.php");
}

$token = generarToken();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar usuario</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Editar usuario</h1>
                <p class="page-subtitle">Actualiza los datos de la cuenta.</p>
            </div>
            <div class="nav">
                <a href="usuarios.php">Volver</a>
                <a href="dashboard.php">Panel</a>
            </div>
        </div>

        <div class="card">
            <form method="POST" class="stack">
                <input type="hidden" name="csrf" value="<?= $token ?>">
                <input name="nombre" value="<?= htmlspecialchars($user['nombre']) ?>" required>
                <input name="email" type="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                <select name="rol">
                    <option value="estudiante" <?= ($user['rol']=='estudiante')?'selected':'' ?>>Estudiante</option>
                    <option value="administrador" <?= ($user['rol']=='administrador')?'selected':'' ?>>Administrador</option>
                </select>
                <input name="password" type="password" placeholder="Nueva contraseña (opcional)">
                <div class="actions">
                    <button class="btn" type="submit">Actualizar</button>
                    <a class="btn btn-secondary" href="usuarios.php">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>