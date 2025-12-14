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
                    respuesta1, respuesta2, respuesta3, respuesta4, respuesta5, 
                    respuesta6, respuesta7, respuesta8, respuesta9, respuesta10, 
                    comentarios, propuestas_mejora, valoracion
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt2 = $db->prepare($sqlResult);
    
    $stmt2->bind_param("iiiissssssssssssi", 
        $idUsuario, 
        $idDispositivo, 
        $tiempoEntero, 
        $completado,
        $r['p1'], $r['p2'], $r['p3'], $r['p4'], $r['p5'], 
        $r['p6'], $r['p7'], $r['p8'], $r['p9'], $r['p10'],
        $r['comentarios_usuario'], 
        $r['propuestas_mejora'], 
        $r['valoracion']
    );
    $stmt2->execute();
    $stmt2->close();

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
    $step = 4;
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
                    
                    <p>Edad:</p>
                    <p><input type="number" name="edad" required min="0" max="99"></p>

                    <p>Profesión:</p>
                    <p><input type="text" name="profesion" required></p>

                    <p>Género:</p>
                    <p>
                        <select name="genero" required>
                            <option value="" selected disabled>Seleccione una opción</option>
                            <option value="1">Hombre</option>
                            <option value="2">Mujer</option>
                            <option value="3">Otro</option>
                        </select>
                    </p>

                    <p>Pericia Informática (0-10):</p>
                    <p><input type="number" name="pericia" min="0" max="10" required></p>

                    <h3>Configuración de la Prueba</h3>
                    
                    <p>Dispositivo utilizado:</p>
                    <p>
                        <select name="dispositivo" required>
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
                    
                    <p>1. ¿En qué país nació Brad Binder?</p>
                    <p><input type="text" name="p1" required></p>

                    <p>2. ¿Cuantas secciones principales aparecen en el menú de navegación?</p>
                    <p><input type="number" name="p2" required></p>

                    <p>3. ¿En qué año nació Brad Binder?</p>
                    <p><input type="number" name="p3" required></p>

                    <p>4. ¿Con qué equipo debutó Brad Binder en MotoGP?</p>
                    <p><input type="text" name="p4" required></p>

                    <p>5. ¿Cuál es la ciudad sede del circuito?</p>
                    <p><input type="text" name="p5" required></p>
                    
                    <p>6. ¿Cuantos habitantes tiene Barcelona?</p>
                    <p><input type="number" name="p6" required></p>

                    <p>7. ¿Cuantas tarjetas hay en el juego de memoria?</p>
                    <p><input type="number" name="p7" required></p>

                    <p>8. ¿Qué significa el término "ECU"?</p>
                    <p><input type="text" name="p8" required></p>

                    <p>9. ¿Cuántos puntos obtuvo Brad Binder el año pasado?</p>
                    <p><input type="number" name="p9" required></p>

                    <p>10. ¿Quién iba primero en el mundial despues del GP de Montmelo?</p>
                    <p><input type="text" name="p10" required></p>

                    <p>11. Valoración global de la web (0-10):</p>
                    <p><input type="number" name="valoracion" min="0" max="10" required></p>

                    <p>Comentarios generales sobre la prueba: (Opcional)</p>
                    <textarea name="comentarios_usuario"></textarea>

                    <p>Propuestas de mejora para la aplicación: (Opcional)</p>
                    <textarea name="propuestas_mejora"></textarea>

                    <button type="submit" name="terminar_prueba">Terminar Prueba</button>
                </form>
            </section>

        <?php elseif ($step == 3): ?>
            <section>
                <h2>Panel del Observador</h2>
                <p><strong>¡Prueba finalizada por el usuario!</strong></p>
                <p>El tiempo ha sido registrado internamente. Ahora, como observador, introduzca sus notas sobre la sesión.</p>
                
                <form method="post" action="pruebas_usabilidad.php">
                    <p>Comentarios / Incidencias detectadas durante la prueba:</p>
                    <p><textarea name="comentarios_observador" rows="6" required></textarea></p>
                    
                    <button type="submit" name="guardar_observaciones">Guardar Todo y Finalizar</button>
                </form>
            </section>

        <?php elseif ($step == 4): ?>
            <section>
                <h2>¡Datos Guardados!</h2>
                <p>La prueba de usabilidad ha sido registrada correctamente en la base de datos.</p>
                <p><a href="pruebas_usabilidad.php">Realizar nueva prueba</a></p>
            </section>
        <?php endif; ?>

    </main>
</body>
</html>