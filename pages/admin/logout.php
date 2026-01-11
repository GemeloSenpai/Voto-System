<?php
    session_start(); // Iniciar la sesión

    // Destruir la sesión
    session_destroy();

    // Redirigir al inicio de sesión
    header('Location: login.php');
    exit;
?>