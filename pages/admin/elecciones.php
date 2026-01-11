<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Procesar creación y cambio de estado de urnas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear nueva urna
    if (isset($_POST['jornada'], $_POST['nivel_educativo'], $_POST['fecha_inicio'], $_POST['fecha_fin'])) {
        $stmt = $pdo->prepare("INSERT INTO configuracion_votacion 
                             (jornada, nivel_educativo, estado, fecha_inicio, fecha_fin) 
                             VALUES (?, ?, 'activo', ?, ?)");
        $stmt->execute([
            $_POST['jornada'],
            $_POST['nivel_educativo'],
            $_POST['fecha_inicio'],
            $_POST['fecha_fin']
        ]);
    }
    // Cambiar estado de urna
    elseif (isset($_POST['cambiar_estado'], $_POST['urna_id'])) {
        $stmt = $pdo->prepare("UPDATE configuracion_votacion 
                              SET estado = IF(estado = 'activo', 'inactivo', 'activo') 
                              WHERE id = ?");
        $stmt->execute([$_POST['urna_id']]);
    }
    
    header('Location: elecciones.php');
    exit;
}

// Obtener todas las urnas (activas e inactivas)
$stmt = $pdo->query("SELECT * FROM configuracion_votacion ORDER BY estado DESC");
$todas_elecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener solo urnas activas
$stmt = $pdo->query("SELECT * FROM configuracion_votacion WHERE estado = 'activo'");
$elecciones_activas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Elecciones Disponibles</title>
    <link rel="stylesheet" href="styleElecciones.css">
</head>
<body>
    <div class="containerA">
        <h1>Elecciones Disponibles</h1>

        <!-- Botón para mostrar lista completa -->
        <div class="button-row">
            <button id="btnMostrarLista" class="btn-lista">Mostrar Lista Completa de Urnas</button>
            <a href="../admin/dashboard.php" class="btn-panel">Volver al Panel</a>
        </div>

        <!-- Formulario para crear nuevas urnas -->
        <div class="form-crear-urna">
            <h3>Crear Nueva Urna</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Jornada:</label>
                    <select name="jornada" required>
                        <option value="">Seleccione jornada</option>
                        <option value="mañana">Mañana</option>
                        <option value="tarde">Tarde</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Nivel Educativo:</label>
                    <select name="nivel_educativo" required>
                        <option value="">Seleccione nivel</option>
                        <option value="escuela">Escuela</option>
                        <option value="colegio">Colegio</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Fecha de inicio:</label>
                    <input type="datetime-local" name="fecha_inicio" required>
                </div>
                
                <div class="form-group">
                    <label>Fecha de fin:</label>
                    <input type="datetime-local" name="fecha_fin" required>
                </div>
                
                <button type="submit">Crear Urna</button>
            </form>
        </div>

        <!-- Lista de elecciones activas -->
        <div class="eleccionesY">
            <?php foreach ($elecciones_activas as $eleccion): ?>
                <div class="eleccion">
                    <h2><?= ucfirst(htmlspecialchars($eleccion['jornada'])) ?> - <?= ucfirst(htmlspecialchars($eleccion['nivel_educativo'])) ?></h2>
                    <p>Fecha de inicio: <?= htmlspecialchars($eleccion['fecha_inicio']) ?></p>
                    <p>Fecha de fin: <?= htmlspecialchars($eleccion['fecha_fin']) ?></p>
                    <a href="../votacion/index.php?jornada=<?= urlencode($eleccion['jornada']) ?>&nivel_educativo=<?= urlencode($eleccion['nivel_educativo']) ?>" class="btn-panel">Ir a Urnas</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal para lista completa de urnas -->
    <div id="modalLista" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Lista Completa de Urnas</h2>
                <span class="close">&times;</span>
            </div>
            <div class="urna-list-container">
                <?php foreach ($todas_elecciones as $eleccion): ?>
                    <div class="urna-item <?= $eleccion['estado'] === 'inactivo' ? 'urna-inactiva' : '' ?>">
                        <div class="urna-info">
                            <strong><?= ucfirst(htmlspecialchars($eleccion['jornada'])) ?> - <?= ucfirst(htmlspecialchars($eleccion['nivel_educativo'])) ?></strong>
                            <small>Estado: <?= htmlspecialchars($eleccion['estado']) ?> | 
                                   Inicio: <?= htmlspecialchars($eleccion['fecha_inicio']) ?> | 
                                   Fin: <?= htmlspecialchars($eleccion['fecha_fin']) ?></small>
                        </div>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="urna_id" value="<?= $eleccion['id'] ?>">
                            <input type="hidden" name="cambiar_estado" value="1">
                            <button type="submit" class="btn-estado">
                                <?= $eleccion['estado'] === 'activo' ? 'Deshabilitar' : 'Habilitar' ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // Mostrar modal de lista
        document.getElementById('btnMostrarLista').onclick = function() {
            document.getElementById('modalLista').style.display = 'block';
        }
        
        // Cerrar modal
        document.querySelector('.close').onclick = function() {
            document.getElementById('modalLista').style.display = 'none';
        }
        
        // Cerrar al hacer clic fuera
        window.onclick = function(event) {
            if (event.target == document.getElementById('modalLista')) {
                document.getElementById('modalLista').style.display = 'none';
            }
        }
    </script>
</body>
</html>