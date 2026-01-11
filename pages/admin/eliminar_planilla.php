<?php
    
    require_once '../../includes/auth.php'; // Funciones de autenticación
    require_once '../../includes/db.php'; // Conexión a la base de datos

    // Verificar si el administrador está autenticado
    if (!isLoggedIn()) {
        header('Location: login.php'); // Redirigir al inicio de sesión
        exit;
    }

    // Obtener el ID de la planilla a eliminar
    if (!isset($_GET['id'])) {
        header('Location: planillas.php'); // Redirigir si no se proporciona un ID
        exit;
    }

    $id = $_GET['id'];

    // Eliminar la planilla de la base de datos
    $stmt = $pdo->prepare("DELETE FROM planillas WHERE id = ?");
    $stmt->execute([$id]);

    // Redirigir a la lista de planillas
    header('Location: planillas.php');
    exit;
?>