
class Memoria {
    constructor(){
        this.tablero_bloqueado = true; 
        this.primera_carta=null;
        this.segunda_carta=null;
        setTimeout(this.barajarCartas.bind(this), 0);

        this.cronometro = new Cronometro();
        this.cronometro.arrancar();
    }

    voltearCarta(carta) {
        const estado = carta.getAttribute('data-estado');

        if (this.tablero_bloqueado === true || estado === 'volteada' || carta === this.primera_carta) { 
            return;
        }

        carta.setAttribute('data-estado', 'volteada');

        

        if (this.primera_carta === null) {
            this.primera_carta = carta;
            
            return;
        } else {
            this.segunda_carta = carta;
            
            this.tablero_bloqueado = true; 
            
            this.comprobarPareja();
        }
    }

    barajarCartas() {  
        const main = document.querySelector('main');     
        const cartas = document.querySelectorAll('main article');
        
        
        let arrayCartas = Array.from(cartas);

        for (let i = arrayCartas.length - 1; i > 0; i--) {
            let j = Math.floor(Math.random() * (i + 1));

            let temp = arrayCartas[i];
            arrayCartas[i] = arrayCartas[j];
            arrayCartas[j] = temp;
        }

        for (const carta of arrayCartas) {
            main.appendChild(carta);
        }
        
        this.tablero_bloqueado = false;
    }

    reiniciarAtributos() {
        this.tablero_bloqueado = false;
        this.primera_carta = null;
        this.segunda_carta = null;
    }

    deshabilitarCartas() {
        if (this.primera_carta) {
            this.primera_carta.setAttribute('data-estado', 'volteada');
        }
        if (this.segunda_carta) {
            this.segunda_carta.setAttribute('data-estado', 'volteada');
        }
        this.comprobarJuego();


        this.reiniciarAtributos();
    }

    comprobarJuego() {
        const cartas = document.querySelectorAll('main article');

        let todasVolteadas = true; 

        for (let i = 0; i < cartas.length; i++) {
            if (cartas[i].getAttribute('data-estado') !== 'volteada') {
                todasVolteadas = false; 
                break; 
            }
        }

        if (todasVolteadas) {
            this.cronometro.parar();
        }
    }

    cubrirCartas() {
        this.tablero_bloqueado = true;

        setTimeout(() => {
            if (this.primera_carta) {
                this.primera_carta.removeAttribute('data-estado');
            }
            if (this.segunda_carta) {
                this.segunda_carta.removeAttribute('data-estado');
            }
            
            this.reiniciarAtributos();

        }, 1500); 
    }

    comprobarPareja() {
        const altPrimera = this.primera_carta.querySelector('img').getAttribute('alt');
        const altSegunda = this.segunda_carta.querySelector('img').getAttribute('alt');

        let sonPareja;

    if (altPrimera === altSegunda) {
        sonPareja = true;
    } else {
        sonPareja = false;
    }
        sonPareja ? this.deshabilitarCartas() : this.cubrirCartas();
    }
}