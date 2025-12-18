
class Ciudad {

    #nombre;
    #pais;
    #gentilicio;
    #poblacion;

    #meteo;
    #meteoEntrenos;
    #coordenadas;
    
    constructor(nombre, pais, gentilicio) {
        this.#nombre = nombre;
        this.#pais = pais;
        this.#gentilicio = gentilicio;
    }

    rellenarAtributosSecundarios(poblacion, latitud, longitud) {
        this.#poblacion = poblacion;
        this.#coordenadas = {
            latitud: latitud,
            longitud: longitud
        };
    }

    getNombreCiudad() {
        $('main').append("<p>Ciudad: " + this.#nombre + "</p>");
    }

    getNombrePais() {
        $('main').append("<p>País: " + this.#pais + "</p>");
    }

    getInformacionSecundariaHTML() {            
         $('main').append("<ul>" +
               "<li>Gentilicio: " + this.#gentilicio + "</li>" +
               "<li>Población: " +  this.#poblacion + " habitantes</li>" +
               "</ul>");
    }

    getCoordenadasHTML() {
     
        const lat = this.#coordenadas.latitud;
        const lon = this.#coordenadas.longitud;
        
        const htmlContent = `
            
                <h4>Coordenadas de ${this.#nombre}</h4>
                <p>Latitud: ${lat}</p>
                <p>Longitud: ${lon}</p>
            
        `;
        
        $('main').append(htmlContent);
    }

    getMeteorologiaCarrera() {
        const fechaCarrera = "2025-09-07"; 
        
        const openMeteoURL = "https://archive-api.open-meteo.com/v1/archive";

        const paramsHorarios = "temperature_2m,apparent_temperature,rain,relative_humidity_2m,wind_speed_10m,wind_direction_10m";
        const paramsDiarios = "sunrise,sunset";

        $.ajax({
            dataType: 'json',
            url: openMeteoURL,
            method: 'GET',
            data: {
                latitude: this.#coordenadas.latitud,
                longitude: this.#coordenadas.longitud,
                start_date: fechaCarrera,
                end_date: fechaCarrera,
                hourly: paramsHorarios,
                daily: paramsDiarios,
                timezone: "Europe/Madrid"
            },
            success: (data) => {
                this.#meteo = data;
                this.#procesarJSONCarrera();
                this.getMeteorologiaEntrenos();
            }
        });
    }

    #procesarJSONCarrera() {
        const datosDiarios = this.#meteo.daily;
        const salidaSol = datosDiarios.sunrise[0].split("T")[1];;
        const puestaSol = datosDiarios.sunset[0].split("T")[1];

        const datosHorarios = this.#meteo.hourly;
        
        const horaAUX = 14;

        const hora = datosHorarios.time[horaAUX].split("T")[1];
        const temperatura = datosHorarios.temperature_2m[horaAUX];
        const sensacionTermica = datosHorarios.apparent_temperature[horaAUX];
        const lluvia = datosHorarios.rain[horaAUX];
        const humedad = datosHorarios.relative_humidity_2m[horaAUX];
        const vientoVelocidad = datosHorarios.wind_speed_10m[horaAUX];
        const vientoDireccion = datosHorarios.wind_direction_10m[horaAUX];

        const htmlMeteo = `
            <h3>Meteorología del Día de la Carrera</h3>
            
            <h4>Datos Totales del Día:</h4>
            <p>Salida del Sol: ${salidaSol}</p>
            <p>Puesta del Sol: ${puestaSol}</p>
            
            <h4>Datos de la Franja Horaria (14:00):</h4>
            <p>Temperatura a 2 metros del suelo: ${temperatura}°C</p>
            <p>Sensación térmica ${sensacionTermica}°C</p>
            <p>Lluvia: ${lluvia} mm</p>
            <p>Humedad relativa a 2 metros del suelo: ${humedad} %</p>
            <p>Velocidad del viento a 10 metros del suelo: ${vientoVelocidad} km/h</p>
            <p>Dirección del viento a 10 metros del suelo: ${vientoDireccion}°</p>
            
        `;
        $('main').append(htmlMeteo);
    }


    getMeteorologiaEntrenos() {
    const fechaInicio = "2025-07-05"; 
    const fechaFin = "2025-07-06";

    const openMeteoURL = "https://archive-api.open-meteo.com/v1/archive";
    
    const paramsHorarios = "temperature_2m,rain,wind_speed_10m,relative_humidity_2m";

    $.ajax({
        dataType: 'json',
        url: openMeteoURL,
        method: 'GET',
        data: {
            latitude: this.#coordenadas.latitud,
            longitude: this.#coordenadas.longitud,
            start_date: fechaInicio,
            end_date: fechaFin,
            hourly: paramsHorarios,
            timezone: "Europe/Madrid"
        },
        success: (data) => {
            this.#meteoEntrenos = data;
            this.#procesarJSONEntrenos();
        }
    });
}

#procesarJSONEntrenos() {
    if (!this.#meteoEntrenos) {
        console.error("No hay datos de entrenamientos para procesar.");
        return;
    }

    const datosHorarios = this.#meteoEntrenos.hourly;

    let sumaTempViernes = 0;
    let sumaLluviaViernes = 0;
    let sumaVientoViernes = 0;
    let sumaHumedadViernes = 0;
    let contadorViernes = 0;

    let sumaTempSabado = 0;
    let sumaLluviaSabado = 0;
    let sumaVientoSabado = 0;
    let sumaHumedadSabado = 0;
    let contadorSabado = 0;

    for (let i = 0; i < datosHorarios.time.length; i++) {
        const fecha = datosHorarios.time[i].split("T")[0];
        
        if (fecha === "2025-07-05") {
            sumaTempViernes += datosHorarios.temperature_2m[i];
            sumaLluviaViernes += datosHorarios.rain[i];
            sumaVientoViernes += datosHorarios.wind_speed_10m[i];
            sumaHumedadViernes += datosHorarios.relative_humidity_2m[i];
            contadorViernes++;
        } else if (fecha === "2025-07-06") {
            sumaTempSabado += datosHorarios.temperature_2m[i];
            sumaLluviaSabado += datosHorarios.rain[i];
            sumaVientoSabado += datosHorarios.wind_speed_10m[i];
            sumaHumedadSabado += datosHorarios.relative_humidity_2m[i];
            contadorSabado++;
        }
    }

    const tempMediaViernes = (sumaTempViernes / contadorViernes).toFixed(1);
    const lluviaMediaViernes = (sumaLluviaViernes / contadorViernes).toFixed(1);
    const vientoMedioViernes = (sumaVientoViernes / contadorViernes).toFixed(1);
    const humedadMediaViernes = (sumaHumedadViernes / contadorViernes).toFixed(1);

    const tempMediaSabado = (sumaTempSabado / contadorSabado).toFixed(1);
    const lluviaMediaSabado = (sumaLluviaSabado / contadorSabado).toFixed(1);
    const vientoMedioSabado = (sumaVientoSabado / contadorSabado).toFixed(1);
    const humedadMediaSabado = (sumaHumedadSabado / contadorSabado).toFixed(1);

    let htmlEntrenos = `
        <h3>Meteorología de Entrenamientos</h3>
        
        <h4>Viernes 05/07/2025 - Datos Medios del Día Completo:</h4>
        <p>Temperatura media a 2 metros del suelo: ${tempMediaViernes}°C</p>
        <p>Lluvia media: ${lluviaMediaViernes} mm</p>
        <p>Humedad relativa media a 2 metros del suelo: ${humedadMediaViernes}%</p>
        <p>Velocidad media del viento a 10 metros del suelo: ${vientoMedioViernes} km/h</p>
        
        <h4>Sábado 06/07/2025 - Datos Medios del Día Completo:</h4>
        <p>Temperatura media a 2 metros del suelo: ${tempMediaSabado}°C</p>
        <p>Lluvia media: ${lluviaMediaSabado} mm</p>
        <p>Humedad relativa media a 2 metros del suelo: ${humedadMediaSabado}%</p>
        <p>Velocidad media del viento a 10 metros del suelo: ${vientoMedioSabado} km/h</p>
    `;

    $('main').append(htmlEntrenos);
}


}


    let ciudadBarcelona = new Ciudad('Barcelona', 'España', 'barcelonés/barcelonesa');
    ciudadBarcelona.rellenarAtributosSecundarios(1702547, 41.389558, 2.168194);

    $("body").append("<main></main>");
    const $main = $("main");

    $main.append('<h3>Información de la Sede del Circuito</h3>');
    ciudadBarcelona.getNombreCiudad();
    ciudadBarcelona.getNombrePais();

    ciudadBarcelona.getInformacionSecundariaHTML();
    ciudadBarcelona.getCoordenadasHTML();

    ciudadBarcelona.getMeteorologiaCarrera();

