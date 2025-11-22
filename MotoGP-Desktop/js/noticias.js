class Noticias {

    #busqueda; 
    #url;
    #noticias;


    constructor() {

        this.#busqueda = "MotoGP"; 
        this.#url = "https://api.thenewsapi.com/v1/news/all";
        this.#noticias = [];
    }

    async buscar() {
        const urlCompleta = `${this.#url}?api_token=${"GjDwF9jKWLMg5eUHiCFQfdAFHRDR1LpAaKwxGzPH"}&search=${encodeURIComponent(this.#busqueda)}&language=es&limit=3`;

        try {
            const respuesta = await fetch(urlCompleta);
            
            if (!respuesta.ok) {
                throw new Error('Noticia no encontrada');
            }
            const datos = await respuesta.json();
            
            this.#procesarInformacion(datos);

        } catch (error) {
            $("article:has(img)").first().after("<h2>Error al cargar noticias</h2>");
        }
    }

    #procesarInformacion(data) {
        this.#noticias = data.data;
        this.#mostrarNoticias();
        
    }

    #mostrarNoticias() {
        const $newsSection = $("<section></section>");
        $newsSection.append("<h2>Últimas Noticias de MotoGP</h2>");
        
        const $listaNoticias = $("<ul></ul>");

        this.#noticias.forEach(noticia => {
            const $item = $("<li></li>");
            
            const $titleLink = $("<a></a>")
                .append($("<h3></h3>").text(noticia.title)); 
            
            $item.append($titleLink);

            const $description = $("<p></p>").text(noticia.description);
            $item.append($description);

            const $sourceText = $("<p></p>").text(`Fuente: ${noticia.source}`);
            $item.append($sourceText);
            
            const $link = $("<a></a>")
                .attr("href", noticia.url)
                .attr("target", "_blank")
                .text("Leer más...");
            $item.append($link);

            $listaNoticias.append($item);
        });

        $newsSection.append($listaNoticias);

        const $carruselArticle = $("article:has(img)").first();
        $carruselArticle.after($newsSection);
    }

}