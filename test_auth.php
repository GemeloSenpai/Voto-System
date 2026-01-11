<?php
    require_once 'includes/db.php';
    require_once 'includes/auth.php';

    // Simular un inicio de sesión
    if (login('admin', 'password')) { // Usuario: admin, Contraseña: password
        echo "Inicio de sesión exitoso!";
    } else {
        echo "Credenciales incorrectas.";
    }
?>