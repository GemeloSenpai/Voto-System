
<?php
    require_once 'includes/auth.php';

    // Redirigir a la página de votación si no está autenticado
    header('Location: pages/admin/login.php');
    exit;
?>