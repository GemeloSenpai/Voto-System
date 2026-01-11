<?php
    require_once '../../includes/auth.php'; // Funciones de autenticación
    require_once '../../includes/db.php'; // Conexión a la base de datos

    // Verificar si el administrador está autenticado
    if (!isLoggedIn()) {
        header('Location: login.php'); // Redirigir al inicio de sesión
        exit;
    }

    // Obtener la jornada y el nivel educativo desde la URL (si se proporcionan)
    $jornada = $_GET['jornada'] ?? '';
    $nivel_educativo = $_GET['nivel_educativo'] ?? '';

    // Obtener los resultados de la votación filtrados por jornada y nivel educativo
    $resultados = [];
    $votos_en_blanco = 0;

    if ($jornada && $nivel_educativo) {
        // Obtener votos por planilla
        $stmt = $pdo->prepare("
            SELECT p.nombre, COUNT(v.id) AS total_votos
            FROM planillas p
            LEFT JOIN votos v ON p.id = v.planilla_id
            WHERE p.jornada = ? AND p.nivel_educativo = ?
            GROUP BY p.id
        ");
        $stmt->execute([$jornada, $nivel_educativo]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener votos en blanco
        $stmt = $pdo->prepare("
            SELECT COUNT(id) AS votos_en_blanco
            FROM votos
            WHERE planilla_id IS NULL AND jornada = ? AND nivel_educativo = ?
        ");
        $stmt->execute([$jornada, $nivel_educativo]);
        $votos_en_blanco = $stmt->fetch(PDO::FETCH_ASSOC)['votos_en_blanco'];
    }

    // Determinar quién ganó
    $ganador = null;
    $max_votos = 0;

    foreach ($resultados as $resultado) {
        if ($resultado['total_votos'] > $max_votos) {
            $max_votos = $resultado['total_votos'];
            $ganador = $resultado['nombre'];
        }
    }

    // Si hay empate, no hay ganador
    if (count(array_filter($resultados, fn($r) => $r['total_votos'] === $max_votos)) > 1) {
        $ganador = "Empate";
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Resultados de la Votación</title>
        <link rel="stylesheet" href="../../assets/css/styles.css">
        <link rel="stylesheet" href="styleResultados.css">
    </head>
    <body>
        <div class="resultados-containerY">
            <h1>Resultados de la Votación</h1>

            <!-- Filtros por jornada y nivel educativo -->
            <div class="filtros">
                <div class="filtro-group">
                    <label for="jornada">Jornada:</label>
                    <select id="jornada" name="jornada" required>
                        <option value="mañana" <?= $jornada === 'mañana' ? 'selected' : '' ?>>Mañana</option>
                        <option value="tarde" <?= $jornada === 'tarde' ? 'selected' : '' ?>>Tarde</option>
                    </select>
                </div>

                <div class="filtro-group">
                    <label for="nivel_educativo">Nivel Educativo:</label>
                    <select id="nivel_educativo" name="nivel_educativo" required>
                        <option value="colegio" <?= $nivel_educativo === 'colegio' ? 'selected' : '' ?>>Colegio</option>
                        <option value="escuela" <?= $nivel_educativo === 'escuela' ? 'selected' : '' ?>>Escuela</option>
                    </select>
                </div>

                <button type="button" onclick="filtrarResultados()">Filtrar</button>
            </div>

            <!-- Mostrar resultados si hay datos -->
            <?php if ($jornada && $nivel_educativo): ?>
                <!-- Tabla de resultados -->
                <table>
                    <thead>
                        <tr>
                            <th>Planilla</th>
                            <th>Votos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($resultados as $resultado): ?>
                            <tr>
                                <td><?= htmlspecialchars($resultado['nombre']) ?></td>
                                <td><?= $resultado['total_votos'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Resumen (ganador y votos en blanco) -->
                <div class="resumen">
                    <p>Ganador: <strong><?= $ganador ?? "No hay ganador" ?></strong></p>
                    <p>Votos en blanco: <strong><?= $votos_en_blanco ?></strong></p>
                </div>
            <?php endif; ?>

            <!-- Botón para volver al panel -->
            <a href="dashboard.php" class="btn-volverZ">Volver al Panel</a>
        </div>
        
        <script>
            // Función para filtrar resultados
            function filtrarResultados() {
                const jornada = document.getElementById('jornada').value;
                const nivelEducativo = document.getElementById('nivel_educativo').value;
                window.location.href = `resultados.php?jornada=${jornada}&nivel_educativo=${nivelEducativo}`;
            }
        </script>
    </body>
</html>