class Circuito {
    
    #lector; 

    constructor() {
        this.#lector = new FileReader(); 
        
        if (this.comprobarApiFile()) {
            this.inicializarInterfaz();
        }
    }

    comprobarApiFile() {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            return true;
        } else {
            const mensajeError = document.createElement('p');
            mensajeError.textContent = "¡¡¡ Este navegador NO soporta el API File y este programa puede no funcionar correctamente !!!";
            document.body.appendChild(mensajeError); 
            return false;
        }
    }
    
    inicializarInterfaz() {
        const h3 = document.createElement('h3');
        h3.textContent = 'Carga de Archivo InfoCircuito.html';

        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.html';
        
        input.addEventListener('change', (evento) => {
            this.leerArchivoHTML(evento.target.files);
        });

        const main = document.querySelector('main');
        if (main) {
            main.appendChild(h3);
            main.appendChild(input);
        } else {
            document.body.appendChild(h3);
            document.body.appendChild(input);
        }
    }

    leerArchivoHTML(files) {
        if (!files || files.length === 0) return;

        const archivo = files[0]; 

        if (archivo) {
            this.#lector.onload = (e) => {
                const contenidoArchivo = e.target.result;
                this.#procesarContenidoHTML(contenidoArchivo); 
            };
            this.#lector.readAsText(archivo); 
        }
    }

    #procesarContenidoHTML(contenidoHTML) {
        const parser = new DOMParser();
        const docExterno = parser.parseFromString(contenidoHTML, 'text/html');
        
        const $main = $('main'); 
        
        if ($main.length === 0) return;

        const h1Externo = docExterno.querySelector('h1');
        if (h1Externo) {
            const nuevoH2 = $('<h2></h2>').text(h1Externo.textContent);
            $('h2:contains("Circuito de MotoGP-Desktop")').replaceWith(nuevoH2);
            h1Externo.remove(); 
        }

        const imagenes = docExterno.querySelectorAll('img');
        imagenes.forEach(img => {
            const src = img.getAttribute('src');
            if (src && src.startsWith('../')) {
                img.setAttribute('src', src.replace('../', ''));
            }
            if (img.parentNode.tagName !== 'P') {
                const nuevoParrafo = docExterno.createElement('p');
                img.parentNode.insertBefore(nuevoParrafo, img);
                nuevoParrafo.appendChild(img);
            }
        });

        const videos = docExterno.querySelectorAll('source');
        videos.forEach(source => {
            const src = source.getAttribute('src');
            if (src && src.startsWith('../')) {
                source.setAttribute('src', src.replace('../', ''));
            }
        });

        const contenidoAInsertar = docExterno.querySelector('body');
        
        if (contenidoAInsertar) {
            $main.append(contenidoAInsertar.innerHTML); 
        }
    }

}


class CargadorSVG {

    constructor() {
        this.contenedor = document.querySelector('section article');
        
        if (this.contenedor) {
            this.crearInput();
            this.inicializar();
        }
    }

    crearInput() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.svg';
        
        this.contenedor.parentNode.insertBefore(input, this.contenedor);
        this.entrada = input;
    }

    inicializar() {
        this.entrada.addEventListener('change', (evento) => this.leerArchivoSVG(evento));
    }

    leerArchivoSVG(evento) {
        const archivo = evento.target.files[0];
        
        if (archivo && archivo.type === 'image/svg+xml') {
            const lector = new FileReader();
            lector.onload = (e) => this.insertarSVG(e.target.result);
            lector.readAsText(archivo);
        } else {
            console.error('Selecciona un archivo SVG válido');
        }
    }

    insertarSVG(contenidoTexto) {
        const parser = new DOMParser();
        const documentoSVG = parser.parseFromString(contenidoTexto, 'image/svg+xml');
        const elementoSVG = documentoSVG.documentElement;
        
        this.contenedor.innerHTML = '';
        this.contenedor.appendChild(elementoSVG);  
    }
}


class CargadorKML {

    constructor() {
        this.accessToken = "pk.eyJ1IjoidW8zMDA3OTgiLCJhIjoiY21pYW5jc3JiMGI4ajJrczZ0bm9pOGFjaiJ9.CN1I8R62F90z5pDjEfY2BQ"; 
        this.contenedor = document.getElementById('mapa');
        
        if (this.contenedor) {
            this.crearInput();
            this.inicializar();
        }
    }

    crearInput() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.kml';

        this.contenedor.parentNode.insertBefore(input, this.contenedor);

        this.entrada = input;
        this.idMapa = 'mapa';
    }

    inicializar() {
        this.entrada.addEventListener('change', (evento) => this.leerArchivoKML(evento));
    }

    leerArchivoKML(evento) {
        const archivo = evento.target.files[0];
        
        if (archivo && (archivo.name.endsWith('.kml') || archivo.name.endsWith('.xml'))) {
            const lector = new FileReader();
            lector.onload = (e) => this.procesarKML(e.target.result);
            lector.readAsText(archivo);
        }
    }

    procesarKML(xmlString) {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(xmlString, "text/xml");

        const coordNode = xmlDoc.querySelector("coordinates");
        if (!coordNode) return;

        const coordenadasRaw = coordNode.textContent.trim();
        const puntos = [];

        const lineas = coordenadasRaw.split(/\s+/);
        
        lineas.forEach(linea => {
            const coords = linea.split(',');
            if (coords.length >= 2) {
                puntos.push([parseFloat(coords[0]), parseFloat(coords[1])]);
            }
        });
        this.insertarCapaKML(puntos);
    }

    insertarCapaKML(puntos) {
        mapboxgl.accessToken = this.accessToken;
        
        const mapa = new mapboxgl.Map({
            container: this.idMapa,
            style: 'mapbox://styles/mapbox/streets-v11',
            center: puntos[0],
            zoom: 14
        });

        mapa.on('load', () => {
            mapa.addSource('ruta', {
                type: 'geojson',
                data: {
                    type: 'Feature',
                    geometry: {
                        type: 'LineString',
                        coordinates: puntos
                    }
                }
            });

            mapa.addLayer({
                id: 'ruta',
                type: 'line',
                source: 'ruta',
                paint: {
                    'line-color': '#FF0000',
                    'line-width': 4
                }
            });

            new mapboxgl.Marker({ color: 'red' })
                .setLngLat(puntos[0])
                .addTo(mapa);
        });
    }
}
