class Cronometro {
    
    constructor() {
        this.tiempo = 0;
        this.inicio = null; 
        this.corriendo = null;
        this.tiempoAcumulado=0;
    }

    arrancar() {
        if (this.corriendo) {
            return; 
        }
        try {
            this.inicio = Temporal.Now.instant();
        } catch (error) {
            this.inicio = new Date();
        } 
        this.corriendo = setInterval(this.actualizar.bind(this), 100);
    }

    actualizar() {
        var milisegundosSegmento;
        
        try {
            var ahora = Temporal.Now.instant();
            var duracion = ahora.since(this.inicio);
            
            milisegundosSegmento = duracion.total({ unit: 'milliseconds' });
            
        } catch (error) {
            var ahora = new Date();
            
            milisegundosSegmento = ahora - this.inicio;
        }

        this.tiempo = this.tiempoAcumulado + milisegundosSegmento;
        this.mostrar();
    }

    mostrar() {
        const totalMilisegundos = this.tiempo;
        const totalSegundos = parseInt(totalMilisegundos / 1000, 10);
        const totalMinutos = parseInt(totalSegundos / 60, 10);

        const decimas = parseInt((totalMilisegundos % 1000) / 100, 10);
        const segundos = totalSegundos % 60;
        const minutos = totalMinutos % 60;

        let stringMinutos;
        if (minutos < 10) {
            stringMinutos = '0' + minutos;
        } else {
            stringMinutos = minutos;
        }

        let stringSegundos;
        if (segundos < 10) {
            stringSegundos = '0' + segundos;
        } else {
            stringSegundos = segundos;
        }
        
        const formatoTiempo = stringMinutos + ":" + stringSegundos + "." + decimas;
        
        const pantalla = document.querySelector('main p');
        if (pantalla) {
            pantalla.textContent = formatoTiempo;
        } else {
            console.error("No se encontró 'main p' para mostrar el cronómetro.");
        }
    }

    parar() {
        clearInterval(this.corriendo);
        this.corriendo = null;
        this.tiempoAcumulado=this.tiempo;
    }

    reiniciar() {
        this.parar();
        
        this.tiempo = 0;
        this.tiempoAcumulado=0;
        this.mostrar();
    }
}