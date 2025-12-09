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
?>