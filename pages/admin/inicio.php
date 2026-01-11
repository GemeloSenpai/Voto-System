
<?php
    // Consultas a la base de datos (se mantienen igual)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM planillas");
    $total_planillas = $stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM votos");
    $total_votos = $stmt->fetch()['total'];

    // Reemplaza la consulta anterior con esta nueva
    $stmt = $pdo->query("SELECT id, jornada, nivel_educativo, fecha_inicio, fecha_fin 
    FROM configuracion_votacion 
    WHERE estado = 'activo' 
    ORDER BY fecha_inicio DESC");
    $urnas_activas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT nombre FROM planillas ORDER BY id DESC LIMIT 1");
    $ultima_planilla = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT grado, COUNT(*) as total FROM votos GROUP BY grado ORDER BY grado");
    $votos_por_grado = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="styleInicio.css"> -->
    <style>
        /* ===== VARIABLES Y ESTILOS BASE ===== */
:root {
    --primary-color: #2c3e50;
    --secondary-color: #34495e;
    --accent-color: #3498db;
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --danger-color: #e74c3c;
    --light-gray: #ecf0f1;
    --medium-gray: #bdc3c7;
    --dark-gray: #7f8c8d;
    --white: #ffffff;
    --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --border-radius: 8px;
    --transition: all 0.3s ease;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
    background-color: #f5f7fa;
    color: var(--primary-color);
    line-height: 1.6;
    margin: 0;
    padding: 0;
}

/* ===== ESTRUCTURA PRINCIPAL ===== */
.main-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 1.5rem;
}

/* ===== ENCABEZADO ===== */
.dashboard-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background-color: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
}

.dashboard-header h1 {
    color: var(--primary-color);
    font-size: 1.75rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.dashboard-subtitle {
    color: var(--dark-gray);
    font-size: 0.9rem;
    font-weight: 400;
}

/* ===== SECCIÓN DE ESTADÍSTICAS ===== */
.stats-section {
    margin-bottom: 2rem;
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--white);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
    flex-shrink: 0;
}

.stat-content {
    flex-grow: 1;
}

.stat-card h3 {
    font-size: 1.1rem;
    color: var(--secondary-color);
    margin-bottom: 0.75rem;
    font-weight: 600;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    margin: 0.5rem 0;
    color: var(--primary-color);
    line-height: 1.2;
}

.stat-detail {
    color: var(--dark-gray);
    font-size: 0.9rem;
    margin: 0;
}

.no-data-message {
    color: var(--dark-gray);
    font-style: italic;
    padding: 0.5rem 0;
}

/* Estilos específicos para cada tarjeta */
.planillas-card {
    border-top: 4px solid var(--accent-color);
}

.planillas-card .stat-icon {
    background-color: rgba(52, 152, 219, 0.1);
    color: var(--accent-color);
}

.votos-card {
    border-top: 4px solid var(--success-color);
}

.votos-card .stat-icon {
    background-color: rgba(39, 174, 96, 0.1);
    color: var(--success-color);
}

.urnas-card {
    border-top: 4px solid var(--warning-color);
}

.urnas-card .stat-icon {
    background-color: rgba(243, 156, 18, 0.1);
    color: var(--warning-color);
}

/* ===== CARRUSEL DE URNAS ===== */
.urnas-carousel {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding: 0.5rem 0;
    scrollbar-width: thin;
    margin-top: 1rem;
}

.urna-item {
    min-width: 250px;
    background-color: var(--light-gray);
    border-radius: calc(var(--border-radius) - 2px);
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    flex-shrink: 0;
    transition: var(--transition);
}

.urna-item:hover {
    background-color: #e0e5e9;
}

.urna-details p {
    margin: 0.5rem 0;
    font-size: 0.9rem;
    display: flex;
    justify-content: space-between;
}

.detail-label {
    font-weight: 600;
    color: var(--secondary-color);
    margin-right: 0.5rem;
}

/* ===== SECCIÓN SECUNDARIA ===== */
.secondary-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
}

.data-card {
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--light-gray);
}

.card-header h2 {
    font-size: 1.25rem;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-body {
    padding: 1.5rem;
}

/* ===== PARTICIPACIÓN POR GRADO ===== */
.grades-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

.grade-item {
    background: var(--light-gray);
    padding: 1rem;
    border-radius: calc(var(--border-radius) - 2px);
    border-left: 3px solid var(--accent-color);
}

.grade-name {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.progress-container {
    height: 6px;
    background-color: #dfe6e9;
    border-radius: 3px;
    margin: 0.5rem 0;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background-color: var(--accent-color);
    border-radius: 3px;
}

.grade-count {
    font-size: 0.8rem;
    color: var(--dark-gray);
    display: block;
    text-align: right;
}

/* ===== ACTIVIDAD RECIENTE ===== */
.activity-item {
    display: flex;
    gap: 1rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--light-gray);
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background-color: rgba(52, 152, 219, 0.1);
    color: var(--accent-color);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
}

.activity-content {
    flex-grow: 1;
}

.activity-content p {
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}

.activity-time {
    color: var(--dark-gray);
    font-size: 0.8rem;
    display: block;
}

/* ===== SCROLLBAR ===== */
::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}

::-webkit-scrollbar-track {
    background: var(--light-gray);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: var(--medium-gray);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--dark-gray);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
    .secondary-section {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .stats-row {
        grid-template-columns: 1fr;
    }
    
    .dashboard-header {
        padding: 1rem;
    }
    
    .dashboard-header h1 {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .main-container {
        padding: 1rem;
    }
    
    .grades-grid {
        grid-template-columns: 1fr;
    }
}
    </style>
    <title>Inicio</title>
</head>
<body>
<div class="main-container">
    <!-- Encabezado del Dashboard -->
    <header class="dashboard-header">
        <h1><i class="fas fa-tachometer-alt"></i> Panel de Control Electoral</h1>
        <p class="dashboard-subtitle">Sistema de monitoreo en tiempo real</p>
    </header>

    <!-- Sección de Estadísticas Principales -->
    <section class="stats-section">
        <div class="stats-row">
            <!-- Tarjeta Planillas -->
            <article class="stat-card planillas-card">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <h3>Planillas Registradas</h3>
                    <p class="stat-value"><?= number_format($total_planillas) ?></p>
                    <p class="stat-detail">Total de candidaturas registradas</p>
                </div>
            </article>

            <!-- Tarjeta Votos -->
            <article class="stat-card votos-card">
                <div class="stat-icon">
                    <i class="fas fa-vote-yea"></i>
                </div>
                <div class="stat-content">
                    <h3>Votos Registrados</h3>
                    <p class="stat-value"><?= number_format($total_votos) ?></p>
                    <p class="stat-detail">Participación electoral total</p>
                </div>
            </article>

            <!-- Tarjeta Urnas Activas -->
            <article class="stat-card urnas-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>Urnas Activas</h3>
                    
                    <?php if (!empty($urnas_activas)): ?>
                        <div class="urnas-carousel">
                            <?php foreach ($urnas_activas as $urna): ?>
                                <div class="urna-item">
                                    <div class="urna-details">
                                        <p><span class="detail-label">Jornada:</span> <?= htmlspecialchars($urna['jornada']) ?></p>
                                        <p><span class="detail-label">Nivel:</span> <?= htmlspecialchars($urna['nivel_educativo']) ?></p>
                                        <p><span class="detail-label">Inicio:</span> <time><?= date('d/m/Y H:i', strtotime($urna['fecha_inicio'])) ?></time></p>
                                        <p><span class="detail-label">Fin:</span> <time><?= date('d/m/Y H:i', strtotime($urna['fecha_fin'])) ?></time></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-data-message">No hay urnas activas</p>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </section>

    <!-- Sección de Datos Secundarios -->
    <section class="secondary-section">
        <!-- Participación por grado -->
        <article class="data-card participation-card">
            <header class="card-header">
                <h2><i class="fas fa-users"></i> Participación por Grado</h2>
            </header>
            <div class="card-body">
                <?php if (empty($votos_por_grado)): ?>
                    <p class="no-data-message">No hay datos de participación disponibles</p>
                <?php else: ?>
                    <div class="grades-grid">
                        <?php foreach ($votos_por_grado as $grado): ?>
                            <div class="grade-item">
                                <span class="grade-name"><?= htmlspecialchars($grado['grado']) ?></span>
                                <div class="progress-container">
                                    <div class="progress-bar" style="width: <?= min(100, ($grado['total'] / max(1, $total_votos)) * 100) ?>%"></div>
                                </div>
                                <span class="grade-count"><?= number_format($grado['total']) ?> votos</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        
        <!-- Actividad reciente -->
        <article class="data-card activity-card">
            <header class="card-header">
                <h2><i class="fas fa-history"></i> Actividad Reciente</h2>
            </header>
            <div class="card-body">
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-content">
                        <p>Nueva planilla "<?= htmlspecialchars($ultima_planilla['nombre'] ?? 'N/A') ?>" registrada</p>
                        <time class="activity-time">Hoy, <?= date('H:i') ?></time>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="activity-content">
                        <p>Sesión iniciada por el administrador</p>
                        <time class="activity-time">Hoy, <?= date('H:i') ?></time>
                    </div>
                </div>
            </div>
        </article>
    </section>
</div>
</body>
</html>
