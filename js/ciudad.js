
class Ciudad {
    
    constructor(nombre, pais, gentilicio) {
        this.nombre = nombre;
        this.pais = pais;
        this.gentilicio = gentilicio;
        this.poblacion = null;
    }

    rellenarAtributosSecundarios(poblacion, latitud, longitud) {
        this.poblacion = poblacion;
        this.coordenadas = {
            latitud: latitud,
            longitud: longitud
        };
    }

    getNombreCiudad() {
        return this.nombre;
    }

    getNombrePais() {
        return this.pais;
    }

    getInformacionSecundariaHTML() {            
         return "<ul>" +
               "<li>Gentilicio: " + this.gentilicio + "</li>" +
               "<li>Población: " +  this.poblacion + " habitantes</li>" +
               "</ul>";
    }

    escribirCoordenadasEnHTML() {
     
        const lat = this.coordenadas.latitud;
        const lon = this.coordenadas.longitud;
        
        const htmlContent = `
            
                <h4>Coordenadas de ${this.nombre}</h4>
                <p>Latitud: ${lat}</p>
                <p>Longitud: ${lon}</p>
            
        `;
        
        document.write(htmlContent);
    }
}
