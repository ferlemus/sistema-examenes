<?php
require 'config/database.php';
require 'includes/sanitize.php';
require 'includes/csrf.php';

session_start();

$csrf_token = generarToken();

$max_attempts = 5;
$lockout_time = 300; // 5 minutos

if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
    if (time() - $_SESSION['last_login_attempt'] < $lockout_time) {
        $min_restantes = ceil(($lockout_time - (time() - $_SESSION['last_login_attempt'])) / 60);
        $error = "Demasiados intentos. Intente de nuevo en $min_restantes minutos.";
    }
    else {
        $_SESSION['login_attempts'] = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $token = $_POST['csrf_token'] ?? '';
    if (!validarToken($token)) {
        die("Error de validación CSRF");
    }

    $email = clean_input($_POST['email']);
    $password = clean_input($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

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

    // Login fallido
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    $_SESSION['last_login_attempt'] = time();
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
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token)?>">
                <input name="email" type="email" required placeholder="Email">
                <input name="password" type="password" required placeholder="Contraseña">
                <button type="submit" class="btn">Ingresar</button>
                <?php
if (!empty($error)) {
    echo "<p class=\"alert\">$error</p>";
}
?>
            </form>
        </div>
    </div>
</body>

</html>