    <?php
    session_start(); // Iniciar la sesión

    // Función para iniciar sesión
    function login($usuario, $password) {
        global $pdo; // Usar la conexión a la base de datos

        // Consulta para obtener el administrador por su nombre de usuario
        $stmt = $pdo->prepare("SELECT * FROM administradores WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el administrador existe y si la contraseña es correcta
        if ($admin && $password === $admin['password']) {
            // Guardar el ID, el nombre de usuario y el rol en la sesión
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['usuario'] = $admin['usuario'];
            $_SESSION['rol'] = 'admin'; // Asignar el rol de administrador
            return true; // Inicio de sesión exitoso
        }

        // Si no es un administrador, verificar si es un votante
        if ($usuario === 'votante' && $password === 'votar') {
            // Guardar el nombre de usuario y el rol en la sesión
            $_SESSION['usuario'] = $usuario;
            $_SESSION['rol'] = 'votante'; // Asignar el rol de votante
            return true; // Inicio de sesión exitoso
        }

        return false; // Inicio de sesión fallido
    }

    // Función para verificar si el usuario está autenticado
    function isLoggedIn() {
        return isset($_SESSION['admin_id']) || isset($_SESSION['usuario']);
    }

    // Función para verificar el rol del usuario
    function getRol() {
        return $_SESSION['rol'] ?? null; // Retorna el rol del usuario o null si no está definido
    }

    // Función para cerrar sesión
    function logout() {
        // Destruir la sesión
        session_destroy();

        // Redirigir al inicio de sesión
        header('Location: login.php');
        exit;
    }


function puedeVotar() {
    return isLoggedIn() && ($_SESSION['rol'] === 'votante');
}

function esAdmin() {
    return isLoggedIn() && ($_SESSION['rol'] === 'admin');
}
?>