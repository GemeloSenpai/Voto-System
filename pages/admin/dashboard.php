<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

$pagina_actual = $_GET['pagina'] ?? 'inicio';
$paginas_permitidas = ['inicio', 'planillas', 'resultados', 'estadisticas', 'elecciones'];
if (!in_array($pagina_actual, $paginas_permitidas)) {
    $pagina_actual = 'inicio';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styleDashboard.css">
    <?php if ($pagina_actual === 'estadisticas'): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
</head>
<body>
    <!-- Barra de navegación superior -->
    <div class="navbar">
        <h2>Panel de Control</h2>
        <ul>
            <li><a href="?pagina=inicio" class="<?= $pagina_actual === 'inicio' ? 'active' : '' ?>">Inicio</a></li>
            <li><a href="?pagina=estadisticas" class="<?= $pagina_actual === 'estadisticas' ? 'active' : '' ?>">Estadísticas</a></li>
            <li><a href="?pagina=planillas" class="<?= $pagina_actual === 'planillas' ? 'active' : '' ?>">Planillas</a></li>
            <li><a href="?pagina=resultados" class="<?= $pagina_actual === 'resultados' ? 'active' : '' ?>">Resultados</a></li>
            <li><a href="?pagina=elecciones" class="<?= $pagina_actual === 'elecciones' ? 'active' : '' ?>">Urnas</a></li>
            <li><a href="logout.php" class="btn-logout">Cerrar Sesión</a></li>
        </ul>
    </div>

    <!-- Área de contenido -->
    <div class="main-container">
        <?php
            switch ($pagina_actual) {
                case 'inicio':
                    require 'inicio.php';
                    break;
                    
                case 'planillas':
                    require 'planillas.php';
                    break;

                case 'resultados':
                    require 'resultados.php';
                    break;

                case 'estadisticas':
                    require 'estadisticas.php';
                    break;

                case 'elecciones':
                    require 'elecciones.php';
                    break;
                    
                default:
                    require 'inicio.php';
                    break;
            }
        ?>
    </div>
</body>
</html>