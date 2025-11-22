class Carrusel {

    #busqueda;
    #actual;
    #maximo;
    #apiURL;
    #fotos;

    constructor() {
        this.#busqueda = "MotoGP Montmelo";
        this.#actual = 0;
        this.#maximo = 5;

        this.#apiURL = "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";
        
        this.#fotos = [];
    }

    getFotografias() {
        const params = {
            tags: this.#busqueda,
            tagmode: "any",
            format: "json"
        };

        $.getJSON(this.#apiURL, params)
            .done((data) => {
                this.#procesarJSONFotografias(data);
            })
    }


    #procesarJSONFotografias(data) {
        for (let i = 0; i < this.#maximo && i < data.items.length; i++) {
            this.#fotos.push(data.items[i]);
        }
        this.#mostrarFotografias();      
    }

    #obtenerURL640(urlOriginal) {
        return urlOriginal.replace('_m.jpg', '_z.jpg');
    }

    #mostrarFotografias() {
        const foto = this.#fotos[this.#actual];
        const url_640px = this.#obtenerURL640(foto.media.m);

        const $h2 = $("<h2></h2>").text("Imágenes del circuito de Circuit de Barcelona-Catalunya");
        const $img = $("<img>")
            .attr("src", url_640px)
            .attr("alt", foto.title);

        const $article = $("<article></article>")
            .append($h2)
            .append($img);

        const $header = $("header");

        $header.after($article);

        setInterval(this.#cambiarFotografia.bind(this), 3000);
    }

    #cambiarFotografia() {
        this.#actual++;
        if (this.#actual >= this.#maximo) {
            this.#actual = 0;
        }
        const foto = this.#fotos[this.#actual];
        const url_640px = this.#obtenerURL640(foto.media.m);

        $("article").first().find("img")
            .attr("src", url_640px)
            .attr("alt", foto.title);

    }
}
