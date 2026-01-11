<?php
    



?>
                            <!-- Papeleta de voto en blanco -->
                            <div class="voto-blanco">
                                <h2>Voto en Blanco</h2>
                                <p>Selecciona esta opción si no deseas votar por ninguna planilla.</p>
                                <!-- Imagen predeterminada para el voto en blanco -->
                                <img class="img-candidato" src="../../uploads/candidatos/votoblanco.png" width="300px" alt="Voto en blanco">
                                <button type="submit" name="planilla_id" value="" class="btn-votar">Votar</button>
                            </div>

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
