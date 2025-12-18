class Circuito {
    
    #lector; 
    #seccion; 

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
            mensajeError.textContent = "¡¡¡ Este navegador NO soporta el API File !!!";
            document.body.appendChild(mensajeError); 
            return false;
        }
    }
    
    inicializarInterfaz() {
        this.#seccion = document.createElement('section');

        const h3 = document.createElement('h3');
        h3.textContent = 'Carga de Archivo InfoCircuito.html';

        const label = document.createElement('label');
        label.textContent = 'Seleccionar archivo HTML: ';
        label.setAttribute('for', 'archivoHTML');
        
        const input = document.createElement('input');
        input.type = 'file';
        input.id = 'archivoHTML';

        
        input.addEventListener('change', (evento) => {
            this.leerArchivoHTML(evento.target.files);
        });

        this.#seccion.appendChild(h3);
        this.#seccion.appendChild(label);
        this.#seccion.appendChild(input);

        const main = document.querySelector('main');
        main.appendChild(this.#seccion);
    }  

    leerArchivoHTML(files) {
        if (!files || files.length === 0) return;
        const archivo = files[0]; 
        if (archivo.name.endsWith('.html')) {
            this.#lector.onload = (e) => {
                const contenidoArchivo = e.target.result;
                this.#procesarContenidoHTML(contenidoArchivo); 
            };
            this.#lector.readAsText(archivo); 
        } else {
            const mensajeError = document.createElement('p');
            mensajeError.textContent = "Archivo no válido";
            this.#seccion.appendChild(mensajeError);
        }
    }

    #procesarContenidoHTML(contenidoHTML) {
        const parser = new DOMParser();
        const docExterno = parser.parseFromString(contenidoHTML, 'text/html');
        
        const $contenedorSeccion = $(this.#seccion);

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
            $contenedorSeccion.append(contenidoAInsertar.innerHTML); 
        }
    }
}


class CargadorSVG {

    constructor() {
        this.inicializarInterfaz();
    }

    inicializarInterfaz() {
        this.section = document.createElement('section');

        const h3 = document.createElement('h3');
        h3.textContent = 'Carga de Archivo SVG';

        const label = document.createElement('label');
        label.textContent = 'Seleccionar archivo SVG: ';
        label.setAttribute('for', 'archivoSVG');
        
        const input = document.createElement('input');
        input.type = 'file';
        input.id = 'archivoSVG';
        
        input.addEventListener('change', (evento) => {
            this.leerArchivoSVG(evento);
        });

        this.section.appendChild(h3);
        this.section.appendChild(label);
        this.section.appendChild(input);
        
        const main = document.querySelector('main');
        main.appendChild(this.section);
    }

    leerArchivoSVG(evento) {
        const archivo = evento.target.files[0];
        
        if (archivo && archivo.type === 'image/svg+xml') {
            const lector = new FileReader();
            lector.onload = (e) => this.insertarSVG(e.target.result);
            lector.readAsText(archivo);
        } else {
            const mensajeError = document.createElement('p');
            mensajeError.textContent = "Archivo no válido";
            this.section.appendChild(mensajeError);
        }
    }

    insertarSVG(contenidoTexto) {
        const parser = new DOMParser();
        const documentoSVG = parser.parseFromString(contenidoTexto, 'image/svg+xml');
        const elementoSVG = documentoSVG.documentElement;

        if (elementoSVG.hasAttribute('version')) {
            elementoSVG.setAttribute('version', '1.1');
        }

        if (!this.contenedor) {
            this.contenedor = document.createElement('article');
            this.section.appendChild(this.contenedor);
        }
        
        this.contenedor.innerHTML = '';
        const h4 = document.createElement('h4');
        h4.textContent = 'Altimetría del circuito';
        this.contenedor.appendChild(h4);
        this.contenedor.appendChild(elementoSVG);  
    }
}


class CargadorKML {

    constructor() {
        this.accessToken = "pk.eyJ1IjoidW8zMDA3OTgiLCJhIjoiY21pYW5jc3JiMGI4ajJrczZ0bm9pOGFjaiJ9.CN1I8R62F90z5pDjEfY2BQ"; 
        this.inicializarInterfaz();
    }

    inicializarInterfaz() {
        this.section = document.createElement('section');

        const h3 = document.createElement('h3');
        h3.textContent = 'Carga de Archivo KML';

        const label = document.createElement('label');
        label.textContent = 'Seleccionar archivo KML: ';
        label.setAttribute('for', 'archivoKML');
        
        const input = document.createElement('input');
        input.type = 'file';
        input.id = 'archivoKML';

        
        input.addEventListener('change', (evento) => {
            this.leerArchivoKML(evento);
        });

        this.section.appendChild(h3);
        this.section.appendChild(label);
        this.section.appendChild(input);
        
        const main = document.querySelector('main');
        main.appendChild(this.section);
    }

    leerArchivoKML(evento) {
        const archivo = evento.target.files[0];
        
        if (archivo && (archivo.name.endsWith('.kml') || archivo.name.endsWith('.xml'))) {
            if (!this.contenedor) {
                this.contenedor = document.createElement('div');
                this.section.appendChild(this.contenedor);
            }
            const lector = new FileReader();
            lector.onload = (e) => this.procesarKML(e.target.result);
            lector.readAsText(archivo);
        } else {
            const mensajeError = document.createElement('p');
            mensajeError.textContent = "Archivo no válido";
            this.section.appendChild(mensajeError);
        }
    }

    procesarKML(xmlString) {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(xmlString, "text/xml");

        const coordNode = xmlDoc.querySelector("coordinates");

        const coordenadasRaw = coordNode.textContent.trim();
        const puntos = [];

        const lineas = coordenadasRaw.split(/\s+/);
        
        lineas.forEach(linea => {
            const coords = linea.split(',');
            puntos.push([parseFloat(coords[0]), parseFloat(coords[1])]);

        });
        this.insertarCapaKML(puntos);
    }

    insertarCapaKML(puntos) {
        mapboxgl.accessToken = this.accessToken;
        
        const mapa = new mapboxgl.Map({
            container: this.contenedor,
            center: puntos[0],
            zoom: 14,
            style: 'mapbox://styles/mapbox/streets-v11'
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

new Circuito();
new CargadorSVG();
new CargadorKML();