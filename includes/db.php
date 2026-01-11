<?php
    // Configuración de la base de datos
    $host = 'localhost';          // Servidor de la base de datos
    $dbname = 'sistema_votacion'; // Nombre de la base de datos (corregido)
    $username = 'root';           // Usuario de la base de datos
    $password = '';               // Contraseña de la base de datos

    try {
        // Crear una instancia de PDO para la conexión
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

        // Configurar PDO para que lance excepciones en caso de errores
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Opcional: Configurar el manejo de caracteres especiales
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Mensaje de éxito (solo para desarrollo, eliminar en producción)
        // echo "Conexión a la base de datos establecida correctamente.";
    } catch (PDOException $e) {
        // Manejo de errores
        die("Error de conexión a la base de datos: " . $e->getMessage());
    }
?>