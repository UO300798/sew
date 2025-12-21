<?php
class Configuracion {
    private $servername;
    private $username;
    private $password;
    private $database;
    private $db;

    public function __construct() {
        $this->servername = "localhost";
        $this->username = "DBUSER2025";
        $this->password = "DBPSWD2025";
        $this->database = "UO300798_DB";
    }

    private function conectarServidor() {
        $this->db = new mysqli($this->servername, $this->username, $this->password);
        if($this->db->connect_error) {
            exit("<p>ERROR de conexión: " . $this->db->connect_error . "</p>");
        }
        return true;
    }

    private function conectarBaseDatos() {
        $this->db = new mysqli($this->servername, $this->username, $this->password, $this->database);
        if($this->db->connect_error) {
            exit("<p>ERROR de conexión: " . $this->db->connect_error . "</p>");
        }
        return true;
    }

    private function cerrarConexion() {
        if($this->db) {
            $this->db->close();
        }
    }

    public function crearBaseDatos() {
        echo "<section><h2>Crear Base de Datos</h2>";

        $this->conectarServidor();

        $consultaDB = "CREATE DATABASE IF NOT EXISTS UO300798_DB COLLATE utf8_spanish_ci";
        if($this->db->query($consultaDB) === TRUE) {
            echo "<p>Base de datos 'UO300798_DB' creada con éxito</p>";
        } else {
            echo "<p>ERROR en la creación de la Base de Datos 'UO300798_DB'. Error: " . $this->db->error . "</p>";
            $this->cerrarConexion();
            return;
        }

        $this->db->select_db($this->database);

        $crearGeneros = "CREATE TABLE IF NOT EXISTS generos (
            id_genero INT AUTO_INCREMENT PRIMARY KEY,
            descripcion VARCHAR(50) NOT NULL
        )";
        if($this->db->query($crearGeneros) === TRUE) {
            echo "<p>Tabla 'generos' creada con éxito</p>";
        } else {
            echo "<p>ERROR en la creación de la tabla 'generos'. Error: " . $this->db->error . "</p>";
        }

        $insertGeneros = "INSERT INTO generos (descripcion) 
                         SELECT 'Hombre' WHERE NOT EXISTS (SELECT 1 FROM generos WHERE descripcion = 'Hombre')
                         UNION ALL
                         SELECT 'Mujer' WHERE NOT EXISTS (SELECT 1 FROM generos WHERE descripcion = 'Mujer')
                         UNION ALL
                         SELECT 'Otro/No binario' WHERE NOT EXISTS (SELECT 1 FROM generos WHERE descripcion = 'Otro/No binario')";
        $this->db->query($insertGeneros);

        $crearDispositivos = "CREATE TABLE IF NOT EXISTS dispositivos (
            id_dispositivo INT AUTO_INCREMENT PRIMARY KEY,
            descripcion VARCHAR(50) NOT NULL
        )";
        if($this->db->query($crearDispositivos) === TRUE) {
            echo "<p>Tabla 'dispositivos' creada con éxito</p>";
        } else {
            echo "<p>ERROR en la creación de la tabla 'dispositivos'. Error: " . $this->db->error . "</p>";
        }

        $insertDispositivos = "INSERT INTO dispositivos (descripcion) 
                              SELECT 'Ordenador' WHERE NOT EXISTS (SELECT 1 FROM dispositivos WHERE descripcion = 'Ordenador')
                              UNION ALL
                              SELECT 'Tableta' WHERE NOT EXISTS (SELECT 1 FROM dispositivos WHERE descripcion = 'Tableta')
                              UNION ALL
                              SELECT 'Teléfono' WHERE NOT EXISTS (SELECT 1 FROM dispositivos WHERE descripcion = 'Teléfono')";
        $this->db->query($insertDispositivos);

        $crearUsuarios = "CREATE TABLE IF NOT EXISTS usuarios (
            id_usuario INT AUTO_INCREMENT PRIMARY KEY,
            profesion VARCHAR(100) NOT NULL,
            edad INT NOT NULL,
            id_genero INT NOT NULL,
            pericia_informatica TINYINT NOT NULL,
            FOREIGN KEY (id_genero) REFERENCES generos(id_genero)
        )";
        if($this->db->query($crearUsuarios) === TRUE) {
            echo "<p>Tabla 'usuarios' creada con éxito</p>";
        } else {
            echo "<p>ERROR en la creación de la tabla 'usuarios'. Error: " . $this->db->error . "</p>";
        }

        $crearResultados = "CREATE TABLE IF NOT EXISTS resultados (
            id_resultado INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            id_dispositivo INT NOT NULL,
            tiempo INT NOT NULL,
            completado BOOLEAN NOT NULL,
            comentarios TEXT,
            propuestas_mejora TEXT,
            valoracion TINYINT NOT NULL CHECK (valoracion BETWEEN 0 AND 10),
            FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
            FOREIGN KEY (id_dispositivo) REFERENCES dispositivos(id_dispositivo)
        )";
        
        if($this->db->query($crearResultados) === TRUE) {
            echo "<p>Tabla 'resultados' creada con éxito</p>";
        } else {
            echo "<p>ERROR en la creación de la tabla 'resultados'. Error: " . $this->db->error . "</p>";
        }

        $crearRespuestas = "CREATE TABLE IF NOT EXISTS respuestas (
            id_respuesta INT AUTO_INCREMENT PRIMARY KEY,
            id_resultado INT NOT NULL,
            numero_pregunta INT NOT NULL,
            respuesta TEXT,
            FOREIGN KEY (id_resultado) REFERENCES resultados(id_resultado) ON DELETE CASCADE
        )";
        if($this->db->query($crearRespuestas) === TRUE) {
            echo "<p>Tabla 'respuestas' creada con éxito</p>";
        } else {
            echo "<p>ERROR en la creación de la tabla 'respuestas'. Error: " . $this->db->error . "</p>";
        }

        $crearObservaciones = "CREATE TABLE IF NOT EXISTS observaciones (
            id_observacion INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            comentarios TEXT NOT NULL,
            FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
        )";
        if($this->db->query($crearObservaciones) === TRUE) {
            echo "<p>Tabla 'observaciones' creada con éxito</p>";
        } else {
            echo "<p>ERROR en la creación de la tabla 'observaciones'. Error: " . $this->db->error . "</p>";
        }

        $this->cerrarConexion();
        echo "</section>";
    }

    public function reiniciarBaseDatos() {
        echo "<section><h2>Reiniciar Base de Datos</h2>";

        $this->db = @new mysqli($this->servername, $this->username, $this->password, $this->database);

        if ($this->db->connect_error) {
            echo "<p>La base de datos no existe. Creándola automáticamente...</p>";
            echo "</section>";
            $this->crearBaseDatos();
            return;
        }

        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");

        $this->db->query("TRUNCATE TABLE observaciones");
        $this->db->query("TRUNCATE TABLE respuestas");
        $this->db->query("TRUNCATE TABLE resultados");
        $this->db->query("TRUNCATE TABLE usuarios");
        $this->db->query("TRUNCATE TABLE dispositivos");
        $this->db->query("TRUNCATE TABLE generos");

        $this->db->query("SET FOREIGN_KEY_CHECKS = 1");

        $insertGeneros = "INSERT INTO generos (descripcion) VALUES ('Hombre'), ('Mujer'), ('Otro/No binario')";
        $this->db->query($insertGeneros);

        $insertDispositivos = "INSERT INTO dispositivos (descripcion) VALUES ('Ordenador'), ('Tableta'), ('Teléfono')";
        $this->db->query($insertDispositivos);

        echo "<p>Base de datos reiniciada correctamente</p>";

        $this->cerrarConexion();
        echo "</section>";
    }

    public function eliminarBaseDatos() {
        echo "<section><h2>Eliminar Base de Datos</h2>";

        $this->conectarServidor();

        $consulta = "DROP DATABASE UO300798_DB";
        if($this->db->query($consulta) === TRUE) {
            echo "<p>Eliminada la base de datos 'UO300798_DB'</p>";
        } else {
            echo "<p>No se ha podido eliminar la base de datos 'UO300798_DB'. Error: " . $this->db->error . "</p>";
        }

        $this->cerrarConexion();
        echo "</section>";
    }

    public function exportarDatos() {
        $this->conectarBaseDatos();

        $nombreArchivo = "exportacion_" . date('Y-m-d_H-i-s') . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');

        $salida = fopen('php://output', 'w');
        fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF));

        $encabezados = [
            'id_resultado', 'id_usuario', 'edad', 'id_genero', 'genero', 'pericia', 'profesion', 
            'id_dispositivo', 'dispositivo', 
            'tiempo', 'completado', 
            'respuesta1', 'respuesta2', 'respuesta3', 'respuesta4', 'respuesta5', 
            'respuesta6', 'respuesta7', 'respuesta8', 'respuesta9', 'respuesta10', 
            'valoracion', 'comentarios_usuario', 'propuestas_mejora', 
            'id_observacion', 'comentarios_observador'
        ];
        fputcsv($salida, $encabezados, ";");

        $sql = "SELECT 
                    r.id_resultado, r.id_usuario, u.edad, u.id_genero, g.descripcion as genero, 
                    u.pericia_informatica, u.profesion,
                    r.id_dispositivo, d.descripcion as dispositivo,
                    r.tiempo, r.completado,
                    (SELECT respuesta FROM respuestas WHERE id_resultado = r.id_resultado AND numero_pregunta = 1) as respuesta1,
                    (SELECT respuesta FROM respuestas WHERE id_resultado = r.id_resultado AND numero_pregunta = 2) as respuesta2,
                    (SELECT respuesta FROM respuestas WHERE id_resultado = r.id_resultado AND numero_pregunta = 3) as respuesta3,
                    (SELECT respuesta FROM respuestas WHERE id_resultado = r.id_resultado AND numero_pregunta = 4) as respuesta4,
                    (SELECT respuesta FROM respuestas WHERE id_resultado = r.id_resultado AND numero_pregunta = 5) as respuesta5,
                    (SELECT respuesta FROM respuestas WHERE id_resultado = r.id_resultado AND numero_pregunta = 6) as respuesta6,
                    (SELECT respuesta FROM respuestas WHERE id_resultado = r.id_resultado AND numero_pregunta = 7) as respuesta7,
                    (SELECT respuesta FROM respuestas WHERE id_resultado = r.id_resultado AND numero_pregunta = 8) as respuesta8,
                    (SELECT respuesta FROM respuestas WHERE id_resultado = r.id_resultado AND numero_pregunta = 9) as respuesta9,
                    (SELECT respuesta FROM respuestas WHERE id_resultado = r.id_resultado AND numero_pregunta = 10) as respuesta10,
                    r.valoracion, r.comentarios as comentarios_usuario, r.propuestas_mejora,
                    o.id_observacion, o.comentarios as comentarios_observador
                FROM resultados r
                JOIN usuarios u ON r.id_usuario = u.id_usuario
                JOIN generos g ON u.id_genero = g.id_genero
                JOIN dispositivos d ON r.id_dispositivo = d.id_dispositivo
                LEFT JOIN observaciones o ON u.id_usuario = o.id_usuario
                ORDER BY u.id_usuario ASC";

        $resultado = $this->db->query($sql);

        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $fila['completado'] = ($fila['completado'] == 1) ? "Completado" : "No completado";

                $filaLimpia = array_map(function($dato) {
                    return str_replace(["\r\n", "\n", "\r"], " ", (string)$dato);
                }, $fila);
                
                fputcsv($salida, $filaLimpia, ";");
            }
        }

        fclose($salida);
        $this->cerrarConexion();

        exit();
    }
}

$config = new Configuracion();
if (isset($_POST['exportar'])) {
    $config->exportarDatos();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Configuración Base de Datos - Pruebas de Usabilidad MotoGP</title>
    <meta name="author" content="Sergio Gonzalez Martinez" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
    <link rel="icon" href="../multimedia/favicon.ico" type="image/x-icon" />
</head>   
<body>
    <header>
        <h1>Configuración - Pruebas de Usabilidad MotoGP</h1>
    </header>
    
    <main>
        <section>
            <h2>Operaciones de Configuración</h2>
            <form method="post" action="configuracion.php">
                <button type="submit" name="crear">Crear Base de Datos</button>
                <button type="submit" name="reiniciar">Reiniciar Base de Datos</button>
                <button type="submit" name="eliminar">Eliminar base datos, sus tablas y los datos asociados</button>
                <button type="submit" name="exportar">Exportar Datos en formato .csv</button>
            </form>
        </section>

        <?php
        if(isset($_POST['crear'])) {
            $config->crearBaseDatos();
        } elseif(isset($_POST['reiniciar'])) {
            $config->reiniciarBaseDatos();
        } elseif(isset($_POST['eliminar'])) {
            $config->eliminarBaseDatos();
        }
        ?>
    </main>
    
</body>
</html>