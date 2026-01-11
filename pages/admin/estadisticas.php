<?php
    require_once '../../includes/auth.php'; // Funciones de autenticación
    require_once '../../includes/db.php'; // Conexión a la base de datos

    // Verificar si el administrador está autenticado
    if (!isLoggedIn()) {
        header('Location: login.php'); // Redirigir al inicio de sesión
        exit;
    }

    // Obtener el año lectivo desde la URL (si se proporciona)
    $anio_lectivo = $_GET['anio_lectivo'] ?? date('Y'); // Por defecto, el año actual

    // Obtener estadísticas de votaciones por jornada y nivel educativo
    $jornadas = ['Mañana', 'Tarde'];
    $niveles_educativos = ['Escuela', 'Colegio'];

    $estadisticas = [];
    foreach ($jornadas as $jornada) {
        foreach ($niveles_educativos as $nivel) {
            // Obtener votos por planilla
            $stmt = $pdo->prepare("
                SELECT p.nombre, COUNT(v.id) AS total_votos
                FROM planillas p
                LEFT JOIN votos v ON p.id = v.planilla_id
                WHERE p.jornada = ? AND p.nivel_educativo = ? AND YEAR(v.fecha_voto) = ?
                GROUP BY p.id
            ");
            $stmt->execute([$jornada, $nivel, $anio_lectivo]);
            $estadisticas[$jornada][$nivel] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener votos en blanco
            $stmt = $pdo->prepare("
                SELECT COUNT(id) AS votos_blanco
                FROM votos
                WHERE planilla_id IS NULL AND jornada = ? AND nivel_educativo = ? AND YEAR(fecha_voto) = ?
            ");
            $stmt->execute([$jornada, $nivel, $anio_lectivo]);
            $votos_blanco[$jornada][$nivel] = $stmt->fetch(PDO::FETCH_ASSOC)['votos_blanco'];
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas de Votaciones</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styleEstadisticas.css">
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-chart-bar"></i>
            Estadísticas de Votaciones
        </h1>

        <!-- Filtros -->
        <div class="filtros">
            <label for="anio_lectivo">Año Lectivo:</label>
            <select id="anio_lectivo" onchange="filtrarEstadisticas()">
                <option value="2025" <?= $anio_lectivo == 2025 ? 'selected' : '' ?>>2025</option>
            </select>
        </div>

        <!-- Cuadrícula de jornadas -->
        <div class="jornadas-grid">
            <?php foreach ($jornadas as $jornada): ?>
                <div class="jornada-container">
                    <div class="jornada-header">
                        <i class="fas <?= $jornada == 'Mañana' ? 'fa-sun' : 'fa-moon' ?>"></i>
                        <h2>Jornada <?= $jornada ?></h2>
                    </div>
                    
                    <div class="niveles-container">
                        <?php foreach ($niveles_educativos as $nivel): ?>
                            <div class="nivel-card">
                                <h3>
                                    <i class="fas <?= $nivel == 'Escuela' ? 'fa-school' : 'fa-graduation-cap' ?>"></i>
                                    <?= $nivel ?>
                                </h3>
                                
                                <div class="grafico-container">
                                    <canvas id="grafico-<?= strtolower($jornada) ?>-<?= strtolower($nivel) ?>"></canvas>
                                </div>
                                
                                <p class="votos-blanco">
                                    Votos en blanco: <strong><?= $votos_blanco[$jornada][$nivel] ?></strong>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Datos para los gráficos
        const datos = {
            <?php foreach ($jornadas as $jornada): ?>
                <?= strtolower($jornada) ?>: {
                    <?php foreach ($niveles_educativos as $nivel): ?>
                        <?= strtolower($nivel) ?>: {
                            labels: <?= json_encode(array_column($estadisticas[$jornada][$nivel], 'nombre')) ?>,
                            datos: <?= json_encode(array_column($estadisticas[$jornada][$nivel], 'total_votos')) ?>
                        },
                    <?php endforeach; ?>
                },
            <?php endforeach; ?>
        };

        // Colores para los gráficos
        const colores = {
            'mañana': {
                'escuela': ['#3B82F6', '#60A5FA', '#93C5FD'],
                'colegio': ['#10B981', '#34D399', '#6EE7B7']
            },
            'tarde': {
                'escuela': ['#F59E0B', '#FBBF24', '#FCD34D'],
                'colegio': ['#EC4899', '#F472B6', '#F9A8D4']
            }
        };

        // Crear gráficos con colores diferenciados
        function crearGrafico(id, labels, datos, jornada, nivel) {
            const ctx = document.getElementById(id).getContext('2d');
            const colorSet = colores[jornada][nivel];
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Votos',
                        data: datos,
                        backgroundColor: colorSet,
                        borderColor: colorSet.map(c => c.replace('0.6', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Inicializar gráficos
        <?php foreach ($jornadas as $jornada): ?>
            <?php foreach ($niveles_educativos as $nivel): ?>
                crearGrafico(
                    'grafico-<?= strtolower($jornada) ?>-<?= strtolower($nivel) ?>',
                    datos.<?= strtolower($jornada) ?>.<?= strtolower($nivel) ?>.labels,
                    datos.<?= strtolower($jornada) ?>.<?= strtolower($nivel) ?>.datos,
                    '<?= strtolower($jornada) ?>',
                    '<?= strtolower($nivel) ?>'
                );
            <?php endforeach; ?>
        <?php endforeach; ?>

        // Función para filtrar estadísticas por año
        function filtrarEstadisticas() {
            const anio = document.getElementById('anio_lectivo').value;
            window.location.href = `estadisticas.php?anio_lectivo=${anio}`;
        }
    </script>
</body>
</html>