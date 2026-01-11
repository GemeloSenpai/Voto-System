<?php
    require_once '../../includes/auth.php'; // Funciones de autenticación
    require_once '../../includes/db.php'; // Conexión a la base de datos

    /* Verificar si el administrador está autenticado
    if (!isLoggedIn()) {
        header('Location: login.php'); // Redirigir al inicio de sesión
        exit;
    }
    */

    // Procesar el formulario de agregar planilla (si se envió)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los datos del formulario
        $nombre = $_POST['nombre'];
        $slogan = $_POST['slogan'];
        $grado = $_POST['grado'];
        $jornada = $_POST['jornada'];
        $nivel_educativo = $_POST['nivel_educativo'];
        $presidente = $_POST['presidente'];

        // Procesar la carga de archivos (logo y foto del candidato)
        $logo = '';
        $foto_candidato = '';

        if ($_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logo = basename($_FILES['logo']['name']);
            move_uploaded_file($_FILES['logo']['tmp_name'], "../../uploads/logos/$logo");
        }

        if ($_FILES['foto_candidato']['error'] === UPLOAD_ERR_OK) {
            $foto_candidato = basename($_FILES['foto_candidato']['name']);
            move_uploaded_file($_FILES['foto_candidato']['tmp_name'], "../../uploads/candidatos/$foto_candidato");
        }

        // Insertar la nueva planilla en la base de datos
        $stmt = $pdo->prepare("INSERT INTO planillas (nombre, slogan, grado, jornada, nivel_educativo, presidente, logo, foto_candidato) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $slogan, $grado, $jornada, $nivel_educativo, $presidente, $logo, $foto_candidato]);

        // Redirigir para evitar reenvío del formulario
        header('Location: planillas.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Agregar Planilla</title>
        <link rel="stylesheet" href="styleAgregar_Planilla.css">
    </head>
    <body>
        <div class="container">
            <h1>Agregar Planilla</h1>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" >
                </div>
                <div class="form-group">
                    <label for="slogan">Slogan:</label>
                    <textarea id="slogan" name="slogan" rows="2" ></textarea>
                </div>
                <div class="form-group">
                    <label for="grado">Grado:</label>
                    <input type="text" id="grado" name="grado" >
                </div>
                <div class="form-group">
                    <label for="jornada">Jornada:</label>
                    <select id="jornada" name="jornada" >
                        <option value="Mañana">Mañana</option>
                        <option value="Tarde">Tarde</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nivel_educativo">Nivel Educativo:</label>
                    <select id="nivel_educativo" name="nivel_educativo" >
                        <option value="Colegio">Colegio</option>
                        <option value="Escuela">Escuela</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="presidente">Nombre de Presidente:</label>
                    <input type="text" id="presidente" name="presidente" >
                </div>
                <div class="form-group">
                    <label for="logo">Logo de la Planilla:</label>
                    <input type="file" id="logo" name="logo" class="file-input" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="foto_candidato">Foto del Candidato:</label>
                    <input type="file" id="foto_candidato" name="foto_candidato" class="file-input" accept="image/*">
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn btn-primary">Guardar Planilla</button>
                    <button href="planillas.php" class="btn btn-secondary">Cancelar</button>
                </div>
            </form>
        </div>
    </body>
</html>