<?php
require 'config/database.php';
require 'includes/sanitize.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email']);
    $password = clean_input($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo password_hash("Admin123", PASSWORD_DEFAULT);
    echo "<pre>";
    print_r($password);
    echo "</pre>";
    echo "<pre>";
    print_r($user['contraseña']);
    echo "</pre>";
    echo "<pre>VERIFY: ";
    var_dump(password_verify($password, $user['contraseña']));
    echo "</pre>";
    if ($user && password_verify($password, $user['contraseña'])) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => $user['id_usuario'],
            'nombre' => $user['nombre'],
            'rol' => $user['rol']
        ];

        header("Location: index.php");
        exit;
    }
    $error = "Credenciales incorrectas";
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingreso</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="card narrow">
            <h1 class="page-title">Ingreso</h1>
            <p class="page-subtitle">Accede con tus credenciales para continuar.</p>
            <form method="POST" class="stack form-gap">
                <input name="email" type="email" required placeholder="Email">
                <input name="password" type="password" required placeholder="Contraseña">
                <button type="submit" class="btn">Ingresar</button>
                <?php if (!empty($error)) echo "<p class=\"alert\">$error</p>"; ?>
            </form>
        </div>
    </div>
</body>
</html>