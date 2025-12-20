<?php
    class Clasificacion {
        
        protected $documento;

        public function __construct() {
            $this->documento = "xml/circuitoEsquema.xml";
        }
        
        public function getDocumento() {
            return $this->documento;
        }

        public function consultar() {
            
            $datos = file_get_contents($this->documento);
            if($datos==null) {
                return;
            }
            $datos = preg_replace("/>\s*</", ">\n<", $datos);

            $xml = new SimpleXMLElement($datos);


            echo "<h3>Ganador de la carrera</h3>";
            $vencedor = $xml->resultado->vencedor;
            $nombreGanador = (string)$vencedor;
            $tiempoGanadorISO = (string)$vencedor['tiempo'];

            preg_match('/PT(\d+)M([\d.]+)S/', $tiempoGanadorISO, $matches);
            if ($matches) {
                $minutos = $matches[1];
                $segundos = $matches[2];
                $tiempoGanador = sprintf("%02d:%06.3f", $minutos, $segundos);
            } else {
                $tiempoGanador = $tiempoGanadorISO; 
            }

            echo "<p>Nombre: $nombreGanador</p>";
            echo "<p>Tiempo: $tiempoGanador</p>";

            echo "<h3>Clasificación del mundial tras la carrera</h3>";
            echo "<ol>";
            foreach ($xml->resultado->clasificacion_mundial->puesto as $puesto) {
                $piloto = (string)$puesto;
                echo "<li>$piloto</li>";
            }
            echo "</ol>";
        }

    }
    
    $clasificacion = new Clasificacion();
?>
<!DOCTYPE HTML>

<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>MotoGP-Clasificaciones</title>
    <meta name="author" content="Sergio Gonzalez Martinez" />
    <meta name="description" content="Información de clasificaciones del proyecto MotoGP-Desktop" />
    <meta name="keywords" content="MotoGP, Clasificaciones" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
    <link rel="icon" href="multimedia/favicon.ico" type="image/x-icon" />
</head>

<body>
    <header>
        <h1><a href="index.html">MotoGP Desktop</a></h1>
        <nav>
            <a href="index.html" title="Información del inicio">Inicio</a>
            <a href="piloto.html" title="Información del piloto">Piloto</a>
            <a href="circuito.html" title="Información del circuito">Circuito</a>
            <a href="meteorologia.html" title="Información de la meteorologia">Meteorologia</a>
            
            <a href="clasificaciones.php" title="Información de las clasificaciones" class="active">Clasificaciones</a>
            
            <a href="juegos.html" title="Información de los juegos">Juegos</a>
            <a href="ayuda.html" title="Información de la ayuda">Ayuda</a>
        </nav>
    </header>
    
    <p>Estás en: <a href="index.html">Inicio</a> -> <strong>Clasificaciones</strong></p>

    <main>
        <h2>Clasificaciones de MotoGP-Desktop</h2>

        <?php 
            $clasificacion->consultar(); 
        ?>
        
    </main>
</body>
</html>