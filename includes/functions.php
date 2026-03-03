<?php
function calcularPuntaje($correctas, $total) {
    if ($total === 0) return 0;
    return round(($correctas / $total) * 100, 2);
}

function permisosAdmin() {
    return isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'administrador';
}

function obtenerPreguntasPorMateria(PDO $pdo, string $materia, int $limite, array $idsExcluir = []): array {
    $stmtConteo = $pdo->prepare('SELECT COUNT(*) FROM preguntas WHERE materia = ?');
    $stmtConteo->execute([$materia]);
    $totalMateria = (int) $stmtConteo->fetchColumn();

    if ($totalMateria < $limite) {
        throw new RuntimeException("No hay suficientes preguntas en {$materia}. Requeridas: {$limite}, disponibles: {$totalMateria}.");
    }

    $idsExcluir = array_values(array_unique(array_map('intval', $idsExcluir)));

    if ($totalMateria <= 5000) {
        $sql = 'SELECT id_pregunta, materia, enunciado, opcion_a, opcion_b, opcion_c, opcion_d FROM preguntas WHERE materia = :materia';
        $params = [':materia' => $materia];

        if (!empty($idsExcluir)) {
            $placeholdersExcluir = [];
            foreach ($idsExcluir as $index => $idExcluir) {
                $key = ":ex{$index}";
                $placeholdersExcluir[] = $key;
                $params[$key] = $idExcluir;
            }
            $sql .= ' AND id_pregunta NOT IN (' . implode(',', $placeholdersExcluir) . ')';
        }

        $sql .= ' ORDER BY RAND() LIMIT ' . (int) $limite;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $preguntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($preguntas) < $limite) {
            throw new RuntimeException("No fue posible completar {$limite} preguntas en {$materia}.");
        }

        return $preguntas;
    }

    $stmtRango = $pdo->prepare('SELECT MIN(id_pregunta) AS min_id, MAX(id_pregunta) AS max_id FROM preguntas WHERE materia = ?');
    $stmtRango->execute([$materia]);
    $rango = $stmtRango->fetch(PDO::FETCH_ASSOC);

    $minId = (int) ($rango['min_id'] ?? 0);
    $maxId = (int) ($rango['max_id'] ?? 0);
    if ($minId === 0 || $maxId === 0) {
        throw new RuntimeException("No se encontró rango de IDs para {$materia}.");
    }

    $seleccionados = [];
    $intentos = 0;
    $maxIntentos = $limite * 50;

    while (count($seleccionados) < $limite && $intentos < $maxIntentos) {
        $intentos++;
        $aleatorio = random_int($minId, $maxId);

        $idsBloqueados = array_values(array_unique(array_merge($idsExcluir, $seleccionados)));
        $sqlBase = 'SELECT id_pregunta FROM preguntas WHERE materia = :materia AND id_pregunta >= :aleatorio';
        $params = [
            ':materia' => $materia,
            ':aleatorio' => $aleatorio,
        ];

        if (!empty($idsBloqueados)) {
            $bloqueados = [];
            foreach ($idsBloqueados as $index => $idBloqueado) {
                $key = ":blq{$index}";
                $bloqueados[] = $key;
                $params[$key] = $idBloqueado;
            }
            $sqlBase .= ' AND id_pregunta NOT IN (' . implode(',', $bloqueados) . ')';
        }

        $sqlAsc = $sqlBase . ' ORDER BY id_pregunta ASC LIMIT 1';
        $stmtAsc = $pdo->prepare($sqlAsc);
        $stmtAsc->execute($params);
        $idEncontrado = $stmtAsc->fetchColumn();

        if (!$idEncontrado) {
            $sqlDesc = str_replace('>= :aleatorio', '<= :aleatorio', $sqlBase) . ' ORDER BY id_pregunta DESC LIMIT 1';
            $stmtDesc = $pdo->prepare($sqlDesc);
            $stmtDesc->execute($params);
            $idEncontrado = $stmtDesc->fetchColumn();
        }

        if ($idEncontrado) {
            $idEncontrado = (int) $idEncontrado;
            if (!in_array($idEncontrado, $seleccionados, true)) {
                $seleccionados[] = $idEncontrado;
            }
        }
    }

    if (count($seleccionados) < $limite) {
        $faltantes = $limite - count($seleccionados);
        $idsBloqueados = array_values(array_unique(array_merge($idsExcluir, $seleccionados)));

        $sqlRelleno = 'SELECT id_pregunta FROM preguntas WHERE materia = :materia';
        $paramsRelleno = [':materia' => $materia];

        if (!empty($idsBloqueados)) {
            $bloqueados = [];
            foreach ($idsBloqueados as $index => $idBloqueado) {
                $key = ":rb{$index}";
                $bloqueados[] = $key;
                $paramsRelleno[$key] = $idBloqueado;
            }
            $sqlRelleno .= ' AND id_pregunta NOT IN (' . implode(',', $bloqueados) . ')';
        }

        $sqlRelleno .= ' ORDER BY id_pregunta ASC LIMIT ' . (int) $faltantes;
        $stmtRelleno = $pdo->prepare($sqlRelleno);
        $stmtRelleno->execute($paramsRelleno);

        foreach ($stmtRelleno->fetchAll(PDO::FETCH_COLUMN) as $idRelleno) {
            $idRelleno = (int) $idRelleno;
            if (!in_array($idRelleno, $seleccionados, true)) {
                $seleccionados[] = $idRelleno;
            }
        }
    }

    if (count($seleccionados) < $limite) {
        throw new RuntimeException("No fue posible completar {$limite} preguntas en {$materia}.");
    }

    $seleccionados = array_slice($seleccionados, 0, $limite);
    $placeholders = implode(',', array_fill(0, count($seleccionados), '?'));
    $sqlFinal = "SELECT id_pregunta, materia, enunciado, opcion_a, opcion_b, opcion_c, opcion_d FROM preguntas WHERE id_pregunta IN ({$placeholders})";
    $stmtFinal = $pdo->prepare($sqlFinal);
    $stmtFinal->execute($seleccionados);
    $preguntas = $stmtFinal->fetchAll(PDO::FETCH_ASSOC);

    if (count($preguntas) < $limite) {
        throw new RuntimeException("No fue posible cargar el detalle completo de preguntas de {$materia}.");
    }

    shuffle($preguntas);
    return $preguntas;
}

function calcularResultados(PDO $pdo, int $idExamen): array {
    $stmtTotales = $pdo->prepare(
        'SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN r.es_correcta = 1 THEN 1 ELSE 0 END) AS correctas
         FROM respuestas_estudiantes r
         WHERE r.id_examen = ?'
    );
    $stmtTotales->execute([$idExamen]);
    $totales = $stmtTotales->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'correctas' => 0];

    $stmtMaterias = $pdo->prepare(
        'SELECT
            p.materia,
            COUNT(*) AS total,
            SUM(CASE WHEN r.es_correcta = 1 THEN 1 ELSE 0 END) AS correctas
         FROM respuestas_estudiantes r
         INNER JOIN preguntas p ON p.id_pregunta = r.id_pregunta
         WHERE r.id_examen = ?
         GROUP BY p.materia
         ORDER BY p.materia ASC'
    );
    $stmtMaterias->execute([$idExamen]);
    $materias = $stmtMaterias->fetchAll(PDO::FETCH_ASSOC);

    foreach ($materias as &$materia) {
        $totalMateria = (int) $materia['total'];
        $correctasMateria = (int) $materia['correctas'];
        $materia['porcentaje'] = $totalMateria > 0 ? round(($correctasMateria / $totalMateria) * 100, 2) : 0;
    }
    unset($materia);

    $total = (int) $totales['total'];
    $correctas = (int) $totales['correctas'];

    return [
        'total' => $total,
        'correctas' => $correctas,
        'puntaje' => $total > 0 ? round(($correctas / $total) * 100, 2) : 0,
        'materias' => $materias,
    ];
}