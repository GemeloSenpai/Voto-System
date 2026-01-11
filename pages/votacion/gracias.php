<?php
session_start(); // Iniciar la sesión

// Obtener los parámetros de la elección desde la URL
$jornada = $_GET['jornada'] ?? '';
$nivel_educativo = $_GET['nivel_educativo'] ?? '';

// Verificar si el votante ha votado (opcional)
if (!isset($_SESSION['ha_votado'])) {
    header('Location: index.php'); // Redirigir si no ha votado
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gracias por Votar</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
   <link rel="stylesheet" href="styleGracias.css">
</head>
<body>
    <div class="container">
        <h1>¡Gracias por tu voto!</h1>
        <p>Tu participación es muy importante para nosotros.</p>

        <!-- Botón "Regresar a Urnas" -->
        <button id="btn-urnas" class="btn-urnas" disabled>Regresar a Urnas</button>
    </div>

    <script>
        // Habilitar el botón después de 3 segundos
        setTimeout(() => {
            const btnUrnas = document.getElementById('btn-urnas');
            btnUrnas.disabled = false;
            btnUrnas.textContent = "Regresar a Urnas";
        }, 1000);

        // Redirigir al hacer clic en el botón
        document.getElementById('btn-urnas').addEventListener('click', () => {
            window.location.href = `../votacion/index.php?jornada=<?= urlencode($jornada) ?>&nivel_educativo=<?= urlencode($nivel_educativo) ?>`;
        });
    </script>
</body>
</html>