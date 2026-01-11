<?php

    require_once '../../includes/db.php'; // Conexión a la base de datos
    require_once '../../includes/auth.php'; // Funciones de autenticación

    // Procesar el formulario de inicio de sesión
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];

        // Intentar iniciar sesión
        if (login($usuario, $password)) {
            // Redirigir según el tipo de usuario
            if ($usuario === 'votante' && $password === 'votar') {
                // Verificar si hay una urna activa para el votante
                $stmt = $pdo->prepare("SELECT jornada, nivel_educativo FROM configuracion_votacion WHERE estado = 'activo' LIMIT 1");
                $stmt->execute();
                $urna_activa = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($urna_activa) {
                    // Redirigir a la página de votación con los parámetros de la urna activa
                    $jornada = $urna_activa['jornada'];
                    $nivel_educativo = $urna_activa['nivel_educativo'];
                    header("Location: /sistema_votacion/pages/votacion/index.php?jornada=" . urlencode($jornada) . "&nivel_educativo=" . urlencode($nivel_educativo));
                    exit;
                } else {
                    // Mostrar mensaje de que no hay urnas activas
                    $no_urna_activa = true;
                }
            } elseif ($usuario === 'admin' && $password === 'admin') {
                header('Location: dashboard.php'); // Redirigir al panel de control
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Votación - Login</title>
    <link rel="stylesheet" href="styleLogin.css">
</head>
<body>
    <!-- Efecto de partículas -->
    <div id="particles-js"></div>
    
    <div class="login-container">
        <!-- Texto descriptivo -->
        <div class="login-description">
            <h2>Sistema de Votación JASAMA</h2>
            <p>Participa en las decisiones importantes de tu institución educativa. Tu voz es importante para nosotros.</p>
        </div>
        
        <h1><i class="fas fa-vote-yea"></i> Iniciar Sesión</h1>

        <!-- Mostrar mensaje de error si las credenciales son incorrectas -->
        <?php if (isset($error)): ?>
            <p class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></p>
        <?php endif; ?>

        <!-- Mostrar mensaje si no hay urnas activas -->
        <?php if (isset($no_urna_activa) && $no_urna_activa): ?>
            <p class="no-urna-activa"><i class="fas fa-info-circle"></i> No hay votaciones activas en este momento.</p>
            <button class="btn-logout" onclick="window.location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button>

            <!-- Script para evitar la actualización de la página -->
            <script>
                // Deshabilitar la tecla F5 y el menú contextual
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                        e.preventDefault();
                    }
                });

                document.addEventListener('contextmenu', function (e) {
                    e.preventDefault();
                });
            </script>
        <?php else: ?>
            <!-- Formulario de inicio de sesión -->
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="usuario"><i class="fas fa-user"></i> Usuario:</label>
                    <input type="text" id="usuario" name="usuario" required placeholder="Ingrese su usuario">
                </div>
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Contraseña:</label>
                    <input type="password" id="password" name="password" required placeholder="Ingrese su contraseña">
                </div>
                <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Script para partículas -->
    <script>
        // Crear partículas decorativas
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.getElementById('particles-js');
            const particleCount = 30;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Tamaño aleatorio entre 2px y 6px
                const size = Math.random() * 4 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Posición aleatoria
                particle.style.left = `${Math.random() * 100}vw`;
                particle.style.top = `${Math.random() * 100}vh`;
                
                // Duración de animación aleatoria
                const duration = Math.random() * 20 + 10;
                particle.style.animationDuration = `${duration}s`;
                
                // Retraso aleatorio
                particle.style.animationDelay = `${Math.random() * 5}s`;
                
                particlesContainer.appendChild(particle);
            }
        });
    </script>
</body>
</html>