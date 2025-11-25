<?php
class Cronometro {
    protected $tiempo;   
    protected $inicio;  

    public function __construct() {
        $this->tiempo = 0;
        $this->inicio = null; 
    }

    public function arrancar() {
        $this->tiempo = 0;
        $this->inicio = microtime(true);
    }

    public function parar() {
        if ($this->inicio !== null) {
            $fin = microtime(true);
            $this->tiempo = $fin - $this->inicio;
            $this->inicio = null;
        }
    }
    public function mostrar() {
        $totalSegundos = $this->tiempo;
        $minutos = floor($totalSegundos / 60);
        $segundos = floor($totalSegundos - ($minutos * 60));
        $decimas = floor(($totalSegundos - floor($totalSegundos)) * 10);
        return sprintf("%02d:%02d.%d", $minutos, $segundos, $decimas);
    }
    
    public function getTiempo() {
        return $this->tiempo;
    }


    public function setTiempo($t) {
        $this->tiempo = $t;
    }

    public function getInicio() {
        return $this->inicio;
    }

    public function setInicio($i) {
        $this->inicio = $i;
    }
}

session_start();

if (!isset($_SESSION['tiempo'])) $_SESSION['tiempo'] = 0;
if (!isset($_SESSION['inicio'])) $_SESSION['inicio'] = null;

$mensaje = "";

$crono = new Cronometro();
$crono->setTiempo($_SESSION['tiempo']);
$crono->setInicio($_SESSION['inicio']);

if (count($_POST) > 0) {
    
    if (isset($_POST['arrancar'])) {
        $crono->arrancar();
        $_SESSION['inicio'] = $crono->getInicio();
        $mensaje = "<p><strong>Cronómetro en marcha</strong></p>";
    }

    if (isset($_POST['parar'])) {
        $crono->parar();
        $_SESSION['tiempo'] = $crono->getTiempo();
        $_SESSION['inicio'] = $crono->getInicio();
        $mensaje = "<p><strong>Cronómetro parado</strong></p>";
    }

    if (isset($_POST['mostrar'])) {
        $tiempoFormateado = $crono->mostrar();
        $mensaje = "<p>Tiempo transcurrido: <strong>$tiempoFormateado</strong></p>";
    }
}
?>


<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>MotoGP-Cronómetro</title>
    <meta name='author' content='Sergio Gonzalez Martinez' />
    <meta name='description' content='Cronómetro del proyecto MotoGP-Desktop' />
    <meta name='keywords' content="MotoGP, Cronómetro" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
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
            <a href="clasificaciones.php" title="Información de las clasificaciones">Clasificaciones</a>
            <a href="juegos.html" title="Información de los juegos">Juegos</a>
            <a href="ayuda.html" title="Información de la ayuda">Ayuda</a>
        </nav>
    </header>
    
    <p class="breadcrumbs">Estás en: <a href="index.html">Inicio</a> -> <a href="juegos.html">Juegos</a> -> <strong>Cronómetro</strong></p>

    <h2>Cronómetro de MotoGP-Desktop</h2>

    <main>
        <section>
            <h3>Control del Cronómetro</h3>
            
            <?php
            if (!empty($mensaje)) {
                echo $mensaje;
            }
            ?>

            <form action="#" method="post" name="controlCronometro">
                <input type="submit" class="button" name="arrancar" value="Arrancar"/>
                <input type="submit" class="button" name="parar" value="Parar"/>
                <input type="submit" class="button" name="mostrar" value="Mostrar Tiempo yea"/>
            </form>
            
            
        </section>
    </main>

</body>
</html>