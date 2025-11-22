import xml.etree.ElementTree as ET

class Html:
    def __init__(self, titulo="Información del Circuito"):
        self.raiz = ET.Element('html', lang="es")
        self.head = ET.SubElement(self.raiz, 'head')
        ET.SubElement(self.head, 'meta', charset="UTF-8")
        ET.SubElement(self.head, 'title').text = titulo
        ET.SubElement(self.head, 'link', rel="stylesheet", href="../estilo/estilo.css")
        ET.SubElement(self.head, 'link', rel="stylesheet", href="../estilo/layout.css")
        self.body = ET.SubElement(self.raiz, 'body')

    def addTitulo(self, texto, nivel=1):
        nivel = min(max(nivel, 1), 6)
        if nivel == 1:
            header = ET.SubElement(self.body, 'header')
            h = ET.SubElement(header, 'h' + str(nivel))
        else:
            h = ET.SubElement(self.body, 'h' + str(nivel))
        h.text = texto

    def addParrafo(self, texto):
        ET.SubElement(self.body, 'p').text = texto

    def addLista(self, elementos, tipo='ul'):
        lista = ET.SubElement(self.body, tipo)
        for elem in elementos:
            ET.SubElement(lista, 'li').text = elem

    def addListaLinks(self, elementos):
        lista = ET.SubElement(self.body, 'ul')
        for texto, url in elementos:
            li = ET.SubElement(lista, 'li')
            ET.SubElement(li, 'a', href=url, target="_blank").text = texto

    def addImagen(self, ruta, alt="Imagen"):
        ET.SubElement(self.body, 'img', src=ruta, alt=alt)

    def addVideo(self, ruta, descripcion=""):
        video_elem = ET.SubElement(self.body, 'video', controls="controls")
        ET.SubElement(video_elem, 'source', src=ruta, type="video/mp4")
        if descripcion:
            ET.SubElement(self.body, 'p').text = descripcion


    def escribir(self, nombreArchivo):
        arbol = ET.ElementTree(self.raiz)
        ET.indent(arbol)
        arbol.write(nombreArchivo, encoding='utf-8', method='html')

        f = open(nombreArchivo, "r", encoding="utf-8")
        contenido = f.read()
        f.close()

        f = open(nombreArchivo, "w", encoding="utf-8")
        f.write("<!DOCTYPE html>\n" + contenido)
        f.close()


def procesarXML_XPath_HTML(archivoXML):
    try:
        arbol = ET.parse(archivoXML)
    except IOError:
        print ('No se encuentra el archivo ', archivoXML)
        exit()
    except ET.ParseError:
        print("Error procesando en el archivo XML = ", archivoXML)
        exit()

    raiz = arbol.getroot()
    ns = '{http://www.uniovi.es}'

    datos = {}
    tags_a_buscar = ['nombre', 'distancia', 'anchura', 'vueltas', 'fecha', 'hora_inicio', 'localidad', 'pais', 'patrocinador']


    for tag in tags_a_buscar:
        elem = raiz.find(f'{ns}{tag}')
        if elem is not None and elem.text:
            if tag in ['distancia', 'anchura']:
                datos[tag] = f"{elem.text.strip()} {elem.attrib.get('unidad','')}"
            elif tag == 'hora_inicio':
                datos[tag] = f"{elem.text.strip()} {elem.attrib.get('zona','')}"
            else:
                datos[tag] = elem.text.strip()

    referencias = []
    for ref in raiz.findall(f'{ns}referencias/{ns}referencia'):
        texto = ref.text.strip()
        url = ref.attrib.get('url', '')
        if not url and "http" in texto:
            partes = texto.rsplit("http", 1)
            texto = partes[0].strip()
            url = "http" + partes[1].strip()
        referencias.append((texto, url))

    fotos = [foto.attrib['nombre'] for foto in raiz.findall(f'{ns}galeria/{ns}foto')]


    videos = [(video.attrib['nombre'], video.attrib.get('descripcion',""))
              for video in raiz.findall(f'{ns}videos/{ns}video')]

    ganador_elem = raiz.find(f'{ns}resultado/{ns}vencedor')

    if ganador_elem is not None:
        ganador = ganador_elem.text.strip()
        tiempo_raw = ganador_elem.attrib.get('tiempo','')
        tiempo_ganador = tiempo_raw.replace('PT', '').replace('M', ' min ').replace('S', ' s')
    else:
        ganador = ""
        tiempo_ganador = ""

    clasificacion = []
    for puesto in raiz.findall(f'{ns}resultado/{ns}clasificacion_mundial/{ns}puesto'):
        if puesto.text:
            clasificacion.append(puesto.text.strip())
        else:
            clasificacion.append("")

    return datos, referencias, fotos, videos, ganador, tiempo_ganador, clasificacion


def main():
    archivoXML = "circuitoEsquema.xml"
    archivoHTML = "InfoCircuito.html"

    datos, referencias, fotos, videos, ganador, tiempo_ganador, clasificacion = procesarXML_XPath_HTML(archivoXML)

    html = Html(titulo=f"Info Circuito - {datos.get('nombre','')}")

    html.addParrafo(f"Nombre: {datos.get('nombre','')}")
    html.addParrafo(f"Distancia: {datos.get('distancia','')}")
    html.addParrafo(f"Anchura: {datos.get('anchura','')}")
    html.addParrafo(f"Vueltas: {datos.get('vueltas','')}")

    html.addParrafo(f"Fecha: {datos.get('fecha','')}")
    html.addParrafo(f"Hora inicio: {datos.get('hora_inicio','')}")
    html.addParrafo(f"Localidad: {datos.get('localidad','')}")
    html.addParrafo(f"País: {datos.get('pais','')}")
    html.addParrafo(f"Patrocinador: {datos.get('patrocinador','')}")

    if referencias:
        html.addTitulo("Referencias", nivel=2)
        html.addListaLinks(referencias)

    if fotos:
        html.addTitulo("Galería de fotos", nivel=2)
        for foto in fotos:
            html.addImagen(f"../{foto}")

    if videos:
        html.addTitulo("Videos", nivel=2)
        for nombre, descripcion in videos:
            html.addVideo(f"../{nombre}", descripcion)

    html.addTitulo("Resultados", nivel=2)
    html.addParrafo(f"Vencedor: {ganador} (Tiempo: {tiempo_ganador} )")
    if clasificacion:
        html.addTitulo("Clasificación Mundial", nivel=3)
        html.addLista(clasificacion, tipo='ol')

    html.escribir(archivoHTML)


if __name__ == "__main__":
    main()
