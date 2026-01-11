<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM planillas");
$planillas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Planillas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="stylePlanillas.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-clipboard-list"></i> Gestión de Planillas</h1>
            <div>
                <button onclick="window.location.href='agregar_planilla.php'" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Insertar Planilla
                </button>
                <button onclick="window.location.href='dashboard.php'" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Regresar a Panel
                </button>
            </div>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Slogan</th>
                        <th>Grado</th>
                        <th>Jornada</th>
                        <th>Nivel</th>
                        <th>Presidente</th>
                        <th>Logo</th>
                        <th>Foto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planillas as $planilla): ?>
                        <tr>
                            <td><?= htmlspecialchars($planilla['nombre']) ?></td>
                            <td><?= htmlspecialchars($planilla['slogan']) ?></td>
                            <td><?= htmlspecialchars($planilla['grado']) ?></td>
                            <td><?= htmlspecialchars($planilla['jornada']) ?></td>
                            <td><?= htmlspecialchars($planilla['nivel_educativo']) ?></td>
                            <td><?= htmlspecialchars($planilla['presidente']) ?></td>
                            <td>
                                <?php if ($planilla['logo']): ?>
                                    <img src="../../uploads/logos/<?= htmlspecialchars($planilla['logo']) ?>" alt="Logo" class="table-img">
                                <?php else: ?>
                                    <i class="fas fa-times status-icon status-missing"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($planilla['foto_candidato']): ?>
                                    <img src="../../uploads/candidatos/<?= htmlspecialchars($planilla['foto_candidato']) ?>" alt="Foto" class="table-img">
                                <?php else: ?>
                                    <i class="fas fa-times status-icon status-missing"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="editar_planilla.php?id=<?= $planilla['id'] ?>" class="action-btn edit-btn">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="eliminar_planilla.php?id=<?= $planilla['id'] ?>" class="action-btn delete-btn" onclick="return confirm('¿Estás seguro de eliminar esta planilla?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>