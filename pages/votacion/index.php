<?php
    require_once '../../includes/db.php';
    require_once '../../includes/auth.php';

    // Limpiar la sesión "ha_votado" al acceder a la página de votación
    unset($_SESSION['ha_votado']);

    // Verificar si el usuario es administrador
    $es_admin = isset($_SESSION['es_admin']) && $_SESSION['es_admin'] === true;

    // Obtener parámetros de la URL
    $jornada = $_GET['jornada'] ?? '';
    $nivel_educativo = $_GET['nivel_educativo'] ?? '';
    $grado = $_GET['grado'] ?? '';

    // Obtener las planillas disponibles
    $stmt = $pdo->prepare(
        "SELECT id, nombre, slogan, grado, presidente, logo, foto_candidato 
        FROM planillas 
        WHERE jornada = ? 
        AND nivel_educativo = ?"
    );
    $stmt->execute([$jornada, $nivel_educativo]);
    $planillas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar el voto
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['planilla_id'])) {
            // Procesar voto normal
            $planilla_id = $_POST['planilla_id'];
            $grado = $_POST['grado'] ?? '';
            
            if (empty($planilla_id)) {
                $planilla_id = null;
            }

            $stmt = $pdo->prepare(
                "INSERT INTO votos (planilla_id, jornada, nivel_educativo, grado) 
                VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$planilla_id, $jornada, $nivel_educativo, $grado]);
            
            $_SESSION['ha_votado'] = true;
            header('Location: gracias.php?jornada=' . urlencode($jornada) . '&nivel_educativo=' . urlencode($nivel_educativo));
            exit;
        } elseif (isset($_POST['password'])) {
            // Procesar autenticación de administrador
            $password = $_POST['password'];
            
            // Verificar la contraseña (deberías usar password_hash() en producción)
            if ($password === 'contraseña_admin') { // Cambia esto por tu lógica de autenticación
                $_SESSION['es_admin'] = true;
                header('Location: ../admin/dashboard.php');
                exit;
            } else {
                $error = "Contraseña incorrecta";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Papeleta de Votación</title>
        <link rel="stylesheet" href="../../assets/css/styles.css">
        <link rel="stylesheet" href="styleIndex.css">
    </head>
    <body>
        <div class="container">
            <h1>Papeleta de Votación</h1>

            <select name="grado" id="selectGrado" required>
                <!-- <option value="">Seleccione el grado</option> -->
                <!-- kindergarten -->
                <optgroup label="Kindergarten">
                    <option value="Kindergarten">Kindergarten</option>
                </optgroup>

                <!-- Primaria Bilingüe -->
                <optgroup label="Bilingual Elementary">
                    <option value="1st Grade">1st Grade</option>
                    <option value="2nd Grade">2nd Grade</option>
                    <option value="3rd Grade">3rd Grade</option>
                    <option value="4th Grade">4th Grade</option>
                    <option value="5th Grade">5th Grade</option>
                    <option value="6th Grade">6th Grade</option>
                </optgroup>
                
                <!-- Primaria Español -->
                <optgroup label="Primaria Español">
                    <option value="1er Grado">1er Grado</option>
                    <option value="2do Grado">2do Grado</option>
                    <option value="3er Grado">3er Grado</option>
                    <option value="4to Grado">4to Grado</option>
                    <option value="5to Grado">5to Grado</option>
                    <option value="6to Grado">6to Grado</option>
                </optgroup>
                
                <!-- Ciclo Básico -->
                <optgroup label="Middle School">
                    <option value="7th Grade">7th Grade</option>
                    <option value="8th Grade">8th Grade</option>
                    <option value="9th Grade">9th Grade</option>
                </optgroup>

                <optgroup label="Ciclo Básico">
                    <option value="Séptimo Grado">Séptimo Grado</option>
                    <option value="Octavo Grado">Octavo Grado</option>
                    <option value="Noveno Grado">Noveno Grado</option>
                </optgroup>
                
                <!-- Media -->
                <optgroup label="Media / High School">
                    <option value="Decimo BTP Bilingual">Decimo BTP Bilingual</option>
                    <option value="Decimo BTP">Decimo BTP</option>
                    <option value="Undécimo BTPA">Undécimo BTPA</option>
                    <option value="Undécimo BCH">Undécimo BCH</option>
                    <option value="Undécimo BTPI">Undécimo BTPI</option>
                    <option value="Duodécimo BTPI">Duodécimo BTPI</option>
                </optgroup>
            </select>

            <!-- Formulario de votación -->
            <form method="POST" action="index.php?jornada=<?= urlencode($jornada) ?>&nivel_educativo=<?= urlencode($nivel_educativo) ?>">

            <input type="hidden" name="grado" id="inputGrado" value="">

            <div class="contenedor-papeletas">    
                <div class="papeletas-container">
                        <!-- Planilla 1 -->
                        <?php if (isset($planillas[0])): ?>
                            <div class="papeleta">
                            <h2><?= htmlspecialchars($planillas[0]['nombre']) ?></h2>
                            <?php if ($planillas[0]['logo']): ?>
                                <img src="../../uploads/logos/<?= htmlspecialchars($planillas[0]['logo']) ?>" alt="Logo de la planilla">
                            <?php endif; ?>
                            <p><strong>Slogan:</strong> <?= htmlspecialchars($planillas[0]['slogan']) ?></p>
                            <?php if ($planillas[0]['foto_candidato']): ?>
                                <img class="img-candidato" src="../../uploads/candidatos/<?= htmlspecialchars($planillas[0]['foto_candidato']) ?>" alt="Foto del presidente">
                            <?php endif; ?>
                            <p><strong>Presidente:</strong> <?= htmlspecialchars($planillas[0]['presidente']) ?></p>
                            <p><strong>Grado:</strong> <?= htmlspecialchars($planillas[0]['grado']) ?></p>
                            
                            <button type="submit" name="planilla_id" value="<?= $planillas[0]['id'] ?>" class="btn-votar">Votar</button>
                        </div>
                        <?php endif; ?>

                        <!-- Papeleta de voto en blanco -->
                        

                        <!--Este codigo pertenece a la Planilla 2 -->
                        <?php if (isset($planillas[1])): ?>
                            <!-- Segunda papeleta -->
                            <div class="papeleta">
                                <h2><?= htmlspecialchars($planillas[1]['nombre']) ?></h2>
                                <?php if ($planillas[1]['logo']): ?>
                                    <img src="../../uploads/logos/<?= htmlspecialchars($planillas[1]['logo']) ?>" alt="Logo de la planilla">
                                <?php endif; ?>
                                <p><strong>Slogan:</strong> <?= htmlspecialchars($planillas[1]['slogan']) ?></p>
                                <?php if ($planillas[1]['foto_candidato']): ?>
                                    <img class="img-candidato" src="../../uploads/candidatos/<?= htmlspecialchars($planillas[1]['foto_candidato']) ?>" alt="Foto del presidente">
                                <?php endif; ?>
                                <p><strong>Presidente:</strong> <?= htmlspecialchars($planillas[1]['presidente']) ?></p>
                                <p><strong>Grado:</strong> <?= htmlspecialchars($planillas[1]['grado']) ?></p>
                                <button type="submit" name="planilla_id" value="<?= $planillas[1]['id'] ?>" class="btn-votar">Votar</button>
                            </div>
                    <?php endif; ?>
                </div>
            </form>
            <!-- Botón de panel de administración (siempre visible) -->
            <button class="btn-panel" onclick="openModal()">Acceso Admin</button>

        </div>

        <!-- Modal para ingresar la contraseña -->
        <!-- Modal para ingresar la contraseña -->
        <div id="modal" class="modal">
                            <div class="modal-content">
                                <button class="btn-cerrar" onclick="closeModal()">&times;</button>
                                <h2>Ingresar Contraseña</h2>
                                
                                <form method="POST" action="../admin/dashboard.php">
                                    <input type="password" name="password" placeholder="Contraseña" required>
                                    <button type="submit">Ingresar</button>
                                </form>
                            </div>
                        </div>

        <script>
            // Funciones para abrir y cerrar el modal
            function openModal() {
                document.getElementById('modal').style.display = 'flex';
            }

            function closeModal() {
                document.getElementById('modal').style.display = 'none';
            }

            // Evitar que el usuario regrese a la página de votación
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                history.go(1);
            };

           document.addEventListener('DOMContentLoaded', function() {
                const selectGrado = document.getElementById('selectGrado');
                const inputGrado = document.getElementById('inputGrado');
                
                // Recuperar y establecer el valor guardado
                const gradoGuardado = localStorage.getItem('gradoSeleccionado');
                if(gradoGuardado) {
                    selectGrado.value = gradoGuardado;
                    inputGrado.value = gradoGuardado;
                }
                
                // Actualizar el input hidden cuando cambia el select
                selectGrado.addEventListener('change', function() {
                    localStorage.setItem('gradoSeleccionado', this.value);
                    inputGrado.value = this.value;
                });
                
                // Asegurarse de enviar el grado al hacer clic en cualquier botón de votar
                document.querySelectorAll('.btn-votar').forEach(btn => {
                    btn.addEventListener('click', function() {
                        inputGrado.value = selectGrado.value;
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                // Recuperar selección guardada
                const gradoGuardado = localStorage.getItem('gradoSeleccionado');
                if(gradoGuardado) {
                    const radio = document.querySelector(`input[name="grado"][value="${gradoGuardado}"]`);
                    if(radio) radio.checked = true;
                }

                // Guardar selección al cambiar
                document.querySelectorAll('input[name="grado"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        localStorage.setItem('gradoSeleccionado', this.value);
                    });
                });
            });
        </script>
    </body>
</html>