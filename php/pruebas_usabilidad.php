<?php
session_start();

include '../cronometro.php';

$servername = "localhost";
$username = "DBUSER2025";
$password = "DBPSWD2025";
$database = "UO300798_DB";

$crono = new Cronometro();
if (isset($_SESSION['inicio_crono'])) {
    $crono->setInicio($_SESSION['inicio_crono']);
}

$step = 1; 


if (isset($_POST['iniciar_prueba'])) {
    $crono->arrancar();
    $_SESSION['inicio_crono'] = $crono->getInicio();
    
    $_SESSION['usuario_datos'] = [
        'edad' => $_POST['edad'],
        'genero' => $_POST['genero'],
        'pericia' => $_POST['pericia'],
        'profesion' => $_POST['profesion'],
        'dispositivo' => $_POST['dispositivo']
    ];
    $step = 2;

} elseif (isset($_POST['terminar_prueba'])) {
    $crono->parar();
    $tiempoFinal = $crono->getTiempo();
    
    $_SESSION['tiempo_final'] = $tiempoFinal;
    $_SESSION['respuestas'] = $_POST; 
    unset($_SESSION['inicio_crono']); 
    
    $step = 3;

} elseif (isset($_POST['guardar_valoracion'])) {
    if (isset($_SESSION['respuestas'])) {
        $_SESSION['respuestas'] = array_merge($_SESSION['respuestas'], $_POST);
    } else {
        $_SESSION['respuestas'] = $_POST;
    }
    $step = 4;

} elseif (isset($_POST['guardar_observaciones'])) {
    $db = new mysqli($servername, $username, $password, $database);
    if ($db->connect_error) {
        echo "Conexión fallida: " . $db->connect_error;
    }   

    $datosUser = $_SESSION['usuario_datos'];
    $sqlUser = "INSERT INTO usuarios (profesion, edad, id_genero, pericia_informatica) 
                VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sqlUser);
    $stmt->bind_param("siii", $datosUser['profesion'], $datosUser['edad'], $datosUser['genero'], $datosUser['pericia']);
    $stmt->execute();
    $idUsuario = $db->insert_id;
    $stmt->close();

    $r = $_SESSION['respuestas']; 
    $tiempoEntero = (int)$_SESSION['tiempo_final'];
    $idDispositivo = $datosUser['dispositivo'];
    
    $completado = 1;
    for ($i = 1; $i <= 10; $i++) {
        $key = 'p' . $i;
        if (isset($r[$key]) && $r[$key] === '999') {
            $completado = 0;
            $r[$key] = 'No respondio';
        }
    }

    $sqlResult = "INSERT INTO resultados (
                    id_usuario, id_dispositivo, tiempo, completado, 
                    comentarios, propuestas_mejora, valoracion
                  ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt2 = $db->prepare($sqlResult);
    
    $stmt2->bind_param("iiiissi", 
        $idUsuario, 
        $idDispositivo, 
        $tiempoEntero, 
        $completado,
        $r['comentarios_usuario'], 
        $r['propuestas_mejora'], 
        $r['valoracion']
    );
    $stmt2->execute();
    $idResultado = $db->insert_id;
    $stmt2->close();

    $sqlResp = "INSERT INTO respuestas (id_resultado, numero_pregunta, respuesta) VALUES (?, ?, ?)";
    $stmtResp = $db->prepare($sqlResp);
    for ($i = 1; $i <= 10; $i++) {
        $stmtResp->bind_param("iis", $idResultado, $i, $r['p' . $i]);
        $stmtResp->execute();
    }
    $stmtResp->close();

    $comentariosObservador = $_POST['comentarios_observador'];
    if (!empty($comentariosObservador)) {
        $sqlObs = "INSERT INTO observaciones (id_usuario, comentarios) VALUES (?, ?)";
        $stmt3 = $db->prepare($sqlObs);
        $stmt3->bind_param("is", $idUsuario, $comentariosObservador);
        $stmt3->execute();
        $stmt3->close();
    }

    $db->close();
    session_destroy(); 
    $step = 5;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>    
    <meta charset="UTF-8" />
    <title>MotoGP-Pruebas Usabilidad</title>
    <meta name="author" content="Sergio Gonzalez Martinez" />
    <meta name="description" content="Prueba de usabilidad del proyecto MotoGP" />
    <meta name="keywords" content="MotoGP, Usabilidad" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>

<body>
    <header>
        <h1>Test de Usabilidad - MotoGP Desktop</h1>
    </header>

    <main>
        <?php if ($step == 1): ?>
            <section>
                <h2>Datos del Participante</h2>
                <form method="post" action="pruebas_usabilidad.php">
                    
                    <h3>Perfil de Usuario</h3>
                    
                    <p><label for="edad">Edad:</label></p>
                    <p><input type="number" id="edad" name="edad" required min="0" max="99"></p>

                    <p><label for="profesion">Profesión:</label></p>
                    <p><input type="text" id="profesion" name="profesion" required></p>

                    <p><label for="genero">Género:</label></p>
                    <p>
                        <select id="genero" name="genero" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            <option value="1">Hombre</option>
                            <option value="2">Mujer</option>
                            <option value="3">Otro</option>
                        </select>
                    </p>

                    <p><label for="pericia">Pericia Informática (0-10):</label></p>
                    <p><input type="number" id="pericia" name="pericia" min="0" max="10" required></p>

                    <h3>Configuración de la Prueba</h3>
                    
                    <p><label for="dispositivo">Dispositivo utilizado:</label></p>
                    <p>
                        <select id="dispositivo" name="dispositivo" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            <option value="1">Ordenador</option>
                            <option value="2">Tableta</option>
                            <option value="3">Teléfono</option>
                        </select>
                    </p>

                    <button type="submit" name="iniciar_prueba">Iniciar Prueba</button>
                </form>
            </section>

        <?php elseif ($step == 2): ?>
            <section>
                <h2>Cuestionario de Evaluación</h2>
                <p>Por favor, navegue por el sitio web MotoGP-Desktop en la otra pestaña y responda las siguientes preguntas.</p>
                
                <form method="post" action="pruebas_usabilidad.php">
                    
                    <p><label for="p1">1. ¿En qué país nació Brad Binder?</label></p>
                    <p><input type="text" id="p1" name="p1" required></p>

                    <p><label for="p2">2. ¿En que año debuto Brad Binder en Moto3?</label></p>
                    <p><input type="number" id="p2" name="p2" required></p>

                    <p><label for="p3">3. ¿En qué año nació Brad Binder?</label></p>
                    <p><input type="number" id="p3" name="p3" required></p>

                    <p><label for="p4">4. ¿Con qué equipo debutó Brad Binder en MotoGP?</label></p>
                    <p><input type="text" id="p4" name="p4" required></p>

                    <p><label for="p5">5. ¿Cuál es la ciudad sede del circuito?</label></p>
                    <p><input type="text" id="p5" name="p5" required></p>
                    
                    <p><label for="p6">6. ¿Cuantos habitantes tiene Barcelona?</label></p>
                    <p><input type="number" id="p6" name="p6" required></p>

                    <p><label for="p7">7. ¿Cuantas tarjetas hay en el juego de memoria?</label></p>
                    <p><input type="number" id="p7" name="p7" required></p>

                    <p><label for="p8">8. ¿Qué significa el término "ECU"?</label></p>
                    <p><input type="text" id="p8" name="p8" required></p>

                    <p><label for="p9">9. ¿Cuántos puntos obtuvo Brad Binder el año pasado?</label></p>
                    <p><input type="number" id="p9" name="p9" required></p>

                    <p><label for="p10">10. ¿Quién iba primero en el mundial despues del GP de Montmelo?</label></p>
                    <p><input type="text" id="p10" name="p10" required></p>

                    <button type="submit" name="terminar_prueba">Terminar Prueba</button>
                </form>
            </section>

        <?php elseif ($step == 3): ?>
            <section>
                <h2>Evaluación y Comentarios</h2>
                <p>Por favor, complete la siguiente información obligatoria.</p>
                <form method="post" action="pruebas_usabilidad.php">
                    <p><label for="valoracion">Valoración global de la web (0-10):</label></p>
                    <p><input type="number" id="valoracion" name="valoracion" min="0" max="10" required></p>

                    <p><label for="comentarios_usuario">Comentarios generales sobre la prueba:</label></p>
                    <textarea id="comentarios_usuario" name="comentarios_usuario" required></textarea>

                    <p><label for="propuestas_mejora">Propuestas de mejora para la aplicación:</label></p>
                    <textarea id="propuestas_mejora" name="propuestas_mejora" required></textarea>

                    <button type="submit" name="guardar_valoracion">Siguiente</button>
                </form>
            </section>

        <?php elseif ($step == 4): ?>
            <section>
                <h2>Panel del Observador</h2>
                <p>¡Prueba finalizada por el usuario!</p>
                <p>Como observador, introduzca sus notas sobre la sesión.</p>
                
                <form method="post" action="pruebas_usabilidad.php">
                    <p><label for="comentarios_observador">Comentarios / Incidencias detectadas durante la prueba:</label></p>
                    <p><textarea id="comentarios_observador" name="comentarios_observador" rows="6" required></textarea></p>
                    
                    <button type="submit" name="guardar_observaciones">Guardar Todo y Finalizar</button>
                </form>
            </section>

        <?php elseif ($step == 5): ?>
            <section>
                <h2>¡Datos Guardados!</h2>
                <p>La prueba de usabilidad ha sido registrada correctamente en la base de datos.</p>
            </section>
        <?php endif; ?>

    </main>
</body>
</html>