<?php
    require_once '../../includes/auth.php'; // Funciones de autenticación
    require_once '../../includes/db.php'; // Conexión a la base de datos

    // Verificar si el administrador está autenticado
    if (!isLoggedIn()) {
        header('Location: login.php'); // Redirigir al inicio de sesión
        exit;
    }

    // Obtener el ID de la planilla desde la URL
    if (!isset($_GET['id'])) {
        header('Location: planillas.php'); // Redirigir si no hay ID
        exit;
    }

    $id_planilla = $_GET['id'];

    // Obtener los datos de la planilla desde la base de datos
    $stmt = $pdo->prepare("SELECT * FROM planillas WHERE id = ?");
    $stmt->execute([$id_planilla]);
    $planilla = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no se encuentra la planilla, redirigir
    if (!$planilla) {
        header('Location: planillas.php');
        exit;
    }

    // Procesar el formulario de edición (si se envió)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los datos del formulario
        $nombre = $_POST['nombre'];
        $slogan = $_POST['slogan'];
        $grado = $_POST['grado'];
        $jornada =$_POST['jornada'];
        $nivel_educativo = $_POST['nivel_educativo'];
        $presidente = $_POST['presidente'];

        // Procesar la carga de archivos (logo y foto del candidato)
        $logo = $planilla['logo']; // Mantener el logo actual si no se sube uno nuevo
        $foto_candidato = $planilla['foto_candidato']; // Mantener la foto actual si no se sube una nueva

        if ($_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logo = basename($_FILES['logo']['name']);
            move_uploaded_file($_FILES['logo']['tmp_name'], "../../uploads/logos/$logo");
        }

        if ($_FILES['foto_candidato']['error'] === UPLOAD_ERR_OK) {
            $foto_candidato = basename($_FILES['foto_candidato']['name']);
            move_uploaded_file($_FILES['foto_candidato']['tmp_name'], "../../uploads/candidatos/$foto_candidato");
        }

        // Actualizar la planilla en la base de datos
        $stmt = $pdo->prepare("
            UPDATE planillas
            SET nombre = ?, slogan = ?, grado = ?, jornada = ?, nivel_educativo = ?, presidente = ?, logo = ?, foto_candidato = ?
            WHERE id = ?
        ");
        $stmt->execute([$nombre, $slogan, $grado, $jornada, $nivel_educativo, $presidente, $logo, $foto_candidato, $id_planilla]);

        // Redirigir para evitar reenvío del formulario
        header('Location: planillas.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Editar Planilla</title>
        <link rel="stylesheet" href="styleEditar_Planilla.css">
    </head>
    <body>
        <div class="container">
            <h1>Editar Planilla</h1>

            <!-- Formulario de edición -->
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($planilla['nombre'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="slogan">Slogan:</label>
                    <textarea id="slogan" name="slogan" rows="3" required><?= htmlspecialchars($planilla['slogan'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="grado">Grado:</label>
                    <input type="text" id="grado" name="grado" value="<?= htmlspecialchars($planilla['grado'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="presidente">Presidente:</label>
                    <input type="text" id="presidente" name="presidente" value="<?= htmlspecialchars($planilla['presidente'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="jornada">Jornada:</label>
                    <select id="jornada" name="jornada" required>
                        <option value="mañana" <?= ($planilla['jornada'] ?? '') === 'mañana' ? 'selected' : '' ?>>Mañana</option>
                        <option value="tarde" <?= ($planilla['jornada'] ?? '') === 'tarde' ? 'selected' : '' ?>>Tarde</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nivel_educativo">Nivel Educativo:</label>
                    <select id="nivel_educativo" name="nivel_educativo" required>
                        <option value="escuela" <?= ($planilla['nivel_educativo'] ?? '') === 'escuela' ? 'selected' : '' ?>>Escuela</option>
                        <option value="colegio" <?= ($planilla['nivel_educativo'] ?? '') === 'colegio' ? 'selected' : '' ?>>Colegio</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="logo">Logo de la Planilla:</label>
                    <input type="file" id="logo" name="logo" class="file-input" accept="image/*">
                    <?php if (!empty($planilla['logo'])): ?>
                        <p>Archivo actual: <?= htmlspecialchars($planilla['logo']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="foto_candidato">Foto del Candidato:</label>
                    <input type="file" id="foto_candidato" name="foto_candidato" class="file-input" accept="image/*">
                    <?php if (!empty($planilla['foto_candidato'])): ?>
                        <p>Archivo actual: <?= htmlspecialchars($planilla['foto_candidato']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <button href="planillas.php" class="btn btn-secondary">Volver a Gestión</button>
                </div>
            </form>
        </div>
    </body>
</html>