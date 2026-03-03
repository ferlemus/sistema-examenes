<?php
require '../config/database.php';
require '../includes/auth.php';
require '../includes/csrf.php';
verificarLogin();

if (!esAdmin()) die("Acceso denegado");

$token = generarToken();

$users = $pdo->query("SELECT * FROM usuarios")->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuarios</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Usuarios</h1>
                <p class="page-subtitle">Administra cuentas de estudiantes y administradores.</p>
            </div>
            <div class="nav">
                <a href="dashboard.php">Panel</a>
                <a href="preguntas.php">Preguntas</a>
                <a href="reportes.php">Reportes</a>
                <a href="../logout.php">Salir</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="title-small">Listado</h2>
                <a class="btn" href="usuarios_add.php">Agregar usuario</a>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th></tr>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($u['nombre']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['rol']) ?></td>
                        <td>
                            <a class="btn btn-secondary" href="usuarios_edit.php?id=<?= $u['id_usuario'] ?>">Editar</a>
                            <form method="POST" action="usuarios_delete.php" style="display:inline;" onsubmit="return confirm('¿Eliminar este usuario?');">
                                <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                <button class="btn btn-secondary" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>