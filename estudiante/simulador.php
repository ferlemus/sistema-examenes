<?php
require '../config/app.php';
require '../config/database.php';
require '../includes/auth.php';
require '../includes/csrf.php';
require '../includes/functions.php';

verificarEstudiante();

const DURACION_SIMULADOR_SEGUNDOS = 10800;

$distribucionMaterias = [
    'Español' => 12,
    'Matemáticas' => 12,
    'Biología' => 12,
    'Física' => 12,
    'Química' => 12,
    'Historia' => 12,
    'Geografía' => 12,
    'Formación Cívica y Ética' => 12,
    'Habilidad Verbal' => 16,
    'Habilidad Matemática' => 16,
];

if (!isset($_SESSION['simulador'])) {
    $_SESSION['simulador'] = [];
}

function iniciarSimulador(PDO $pdo, int $idUsuario, array $distribucionMaterias): int {
    $totalPreguntas = array_sum($distribucionMaterias);

    $stmt = $pdo->prepare('INSERT INTO examenes (id_usuario, fecha_inicio, total_preguntas) VALUES (?, NOW(), ?)');
    $stmt->execute([$idUsuario, $totalPreguntas]);
    $idExamen = (int) $pdo->lastInsertId();

    $preguntasSeleccionadas = [];
    $idsYaTomados = [];

    foreach ($distribucionMaterias as $materia => $limite) {
        $preguntasMateria = obtenerPreguntasPorMateria($pdo, $materia, $limite, $idsYaTomados);
        foreach ($preguntasMateria as $pregunta) {
            $idPregunta = (int) $pregunta['id_pregunta'];
            if (!in_array($idPregunta, $idsYaTomados, true)) {
                $idsYaTomados[] = $idPregunta;
                $preguntasSeleccionadas[] = [
                    'id_pregunta' => $idPregunta,
                    'materia' => $pregunta['materia'],
                ];
            }
        }
    }

    if (count($preguntasSeleccionadas) !== $totalPreguntas) {
        throw new RuntimeException('No fue posible construir el simulador completo de 128 preguntas.');
    }

    shuffle($preguntasSeleccionadas);

    $_SESSION['simulador'][$idExamen] = [
        'id_usuario' => $idUsuario,
        'preguntas' => $preguntasSeleccionadas,
        'respuestas' => [],
        'inicio_ts' => time(),
        'fin_ts' => time() + DURACION_SIMULADOR_SEGUNDOS,
    ];

    return $idExamen;
}

function renderAutoFinalizacion(int $idExamen, string $token): void {
    ?>
    <!doctype html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Finalizando simulador</title>
    </head>
    <body>
        <form id="autoFin" method="POST" action="guardar_examen.php">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="id_examen" value="<?= (int) $idExamen ?>">
            <input type="hidden" name="finalizar" value="1">
        </form>
        <script>document.getElementById('autoFin').submit();</script>
    </body>
    </html>
    <?php
}

try {
    $idUsuario = (int) $_SESSION['usuario']['id'];
    $idExamen = isset($_REQUEST['id_examen']) ? (int) $_REQUEST['id_examen'] : 0;

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['nuevo']) || $idExamen === 0)) {
        $idExamen = iniciarSimulador($pdo, $idUsuario, $distribucionMaterias);
        header('Location: simulador.php?id_examen=' . $idExamen . '&n=1');
        exit;
    }

    if ($idExamen <= 0 || !isset($_SESSION['simulador'][$idExamen])) {
        $idExamen = iniciarSimulador($pdo, $idUsuario, $distribucionMaterias);
        header('Location: simulador.php?id_examen=' . $idExamen . '&n=1');
        exit;
    }

    if ((int) $_SESSION['simulador'][$idExamen]['id_usuario'] !== $idUsuario) {
        http_response_code(403);
        exit('Examen no válido para este usuario.');
    }

    $intento = &$_SESSION['simulador'][$idExamen];
    $totalPreguntas = count($intento['preguntas']);
    $indice = isset($_REQUEST['n']) ? (int) $_REQUEST['n'] : 1;
    $indice = max(1, min($totalPreguntas, $indice));

    if (time() >= (int) $intento['fin_ts']) {
        $tokenAuto = generarToken();
        renderAutoFinalizacion($idExamen, $tokenAuto);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!validarToken($_POST['csrf'] ?? '')) {
            throw new RuntimeException('Token CSRF inválido.');
        }

        $accion = $_POST['accion'] ?? 'siguiente';
        $indiceActual = isset($_POST['n']) ? (int) $_POST['n'] : 1;
        $indiceActual = max(1, min($totalPreguntas, $indiceActual));

        $preguntaActual = $intento['preguntas'][$indiceActual - 1] ?? null;
        if (!$preguntaActual) {
            throw new RuntimeException('Pregunta inválida.');
        }

        $respuesta = $_POST['respuesta'] ?? null;
        if ($respuesta !== null && in_array($respuesta, ['A', 'B', 'C', 'D'], true)) {
            $intento['respuestas'][(int) $preguntaActual['id_pregunta']] = $respuesta;
        }

        if ($accion === 'finalizar') {
            $tokenAuto = generarToken();
            renderAutoFinalizacion($idExamen, $tokenAuto);
            exit;
        }

        if ($accion === 'anterior') {
            $indice = max(1, $indiceActual - 1);
        } else {
            $indice = min($totalPreguntas, $indiceActual + 1);
        }
    }

    $preguntaInfo = $intento['preguntas'][$indice - 1];
    $idPreguntaActual = (int) $preguntaInfo['id_pregunta'];

    $stmtPregunta = $pdo->prepare('SELECT id_pregunta, materia, enunciado, opcion_a, opcion_b, opcion_c, opcion_d FROM preguntas WHERE id_pregunta = ? LIMIT 1');
    $stmtPregunta->execute([$idPreguntaActual]);
    $pregunta = $stmtPregunta->fetch(PDO::FETCH_ASSOC);

    if (!$pregunta) {
        throw new RuntimeException('No se encontró la pregunta solicitada.');
    }

    $respuestaSeleccionada = $intento['respuestas'][$idPreguntaActual] ?? '';
    $segundosRestantes = max(0, (int) $intento['fin_ts'] - time());
    $token = generarToken();
} catch (Throwable $e) {
    http_response_code(500);
    exit('Error al cargar el simulador: ' . htmlspecialchars($e->getMessage()));
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Simulador COMIPEMS</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Simulador COMIPEMS</h1>
                <p class="page-subtitle">Pregunta <?= (int) $indice ?> de <?= (int) $totalPreguntas ?> · <?= htmlspecialchars($pregunta['materia']) ?></p>
            </div>
            <div class="actions">
                <a class="btn btn-secondary" href="inicio.php">Regresar</a>
            </div>
        </div>

        <div class="card" style="margin-bottom:16px;max-width:340px;position:sticky;top:16px;z-index:10;background:#fff;">
            <div style="font-size:18px;font-weight:600;color:#0f766e">Tiempo restante:</div>
            <div id="reloj" style="font-size:22px;font-weight:700;margin-top:4px">00:00:00</div>
        </div>

        <form method="POST" action="simulador.php" class="stack">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="id_examen" value="<?= (int) $idExamen ?>">
            <input type="hidden" name="n" value="<?= (int) $indice ?>">

            <div class="card question-card">
                <p class="question-title"><?= htmlspecialchars($pregunta['enunciado']) ?></p>

                <?php foreach (['A', 'B', 'C', 'D'] as $op):
                    $key = 'opcion_' . strtolower($op);
                    $checked = ($respuestaSeleccionada === $op) ? 'checked' : '';
                ?>
                    <label class="option">
                        <input type="radio" name="respuesta" value="<?= $op ?>" <?= $checked ?>>
                        <span><?= htmlspecialchars((string) $pregunta[$key]) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="actions">
                <?php if ($indice > 1): ?>
                    <button type="submit" class="btn btn-secondary" name="accion" value="anterior">Anterior</button>
                <?php endif; ?>

                <?php if ($indice < $totalPreguntas): ?>
                    <button type="submit" class="btn" name="accion" value="siguiente">Siguiente</button>
                <?php else: ?>
                    <button type="submit" class="btn" name="accion" value="finalizar">Finalizar</button>
                <?php endif; ?>
            </div>
        </form>

        <form id="form-fin" method="POST" action="guardar_examen.php" style="display:none;">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="id_examen" value="<?= (int) $idExamen ?>">
            <input type="hidden" name="finalizar" value="1">
        </form>
    </div>

    <script>
        const reloj = document.getElementById('reloj');
        const formFin = document.getElementById('form-fin');
        let segundos = <?= (int) $segundosRestantes ?>;

        function pad(numero) {
            return numero < 10 ? '0' + numero : String(numero);
        }

        function pintarReloj() {
            const h = Math.floor(segundos / 3600);
            const m = Math.floor((segundos % 3600) / 60);
            const s = segundos % 60;
            reloj.textContent = `${pad(h)}:${pad(m)}:${pad(s)}`;
        }

        pintarReloj();

        const intervalo = setInterval(() => {
            segundos--;
            if (segundos <= 0) {
                clearInterval(intervalo);
                reloj.textContent = '00:00:00';
                formFin.submit();
                return;
            }
            pintarReloj();
        }, 1000);
    </script>
</body>
</html>
