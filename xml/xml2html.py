# -*- coding: utf-8 -*-
import xml.etree.ElementTree as ET

class Html:
    def __init__(self, titulo="Información del Circuito"):
        """Crea la estructura base del HTML y enlaza el CSS"""
        self.raiz = ET.Element('html', lang="es")
        self.head = ET.SubElement(self.raiz, 'head')
        ET.SubElement(self.head, 'meta', charset="UTF-8")
        ET.SubElement(self.head, 'title').text = titulo
        ET.SubElement(self.head, 'link', rel="stylesheet", href="../estilo/estilo.css")
        ET.SubElement(self.head, 'link', rel="stylesheet", href="../estilo/layout.css")
        self.body = ET.SubElement(self.raiz, 'body')

    # ---------------- Métodos para contenido ----------------
    def addTitulo(self, texto, nivel=1):
        nivel = min(max(nivel, 1), 6)
        h = ET.SubElement(self.body, 'h' + str(nivel))
        if texto:
            h.text = texto
        else:
            h.text = ""

    def addParrafo(self, texto):
        p = ET.SubElement(self.body, 'p')
        if texto:
            p.text = texto
        else:
            p.text = ""

    def addLista(self, elementos, tipo='ul'):
        lista = ET.SubElement(self.body, tipo)
        for elem in elementos:
            li = ET.SubElement(lista, 'li')
            if elem:
                li.text = elem
            else:
                li.text = ""

    def addListaLinks(self, elementos):
        """elementos = lista de tuplas (texto, url)"""
        lista = ET.SubElement(self.body, 'ul')
        for texto, url in elementos:
            li = ET.SubElement(lista, 'li')
            a = ET.SubElement(li, 'a', href=url, target="_blank")
            if texto:
                a.text = texto
            else:
                a.text = ""

    # ---------------- Métodos para multimedia ----------------
    def addImagen(self, ruta, alt="Imagen"):
        """Añade una imagen con ruta completa pasada por parámetro"""
        ET.SubElement(self.body, 'img', src=ruta, alt=alt)

    def addVideo(self, ruta, descripcion=""):
        """Añade un video con cierre correcto <video></video> y ruta pasada por parámetro"""
        video_elem = ET.SubElement(self.body, 'video', controls="controls")
        ET.SubElement(video_elem, 'source', src=ruta, type="video/mp4")
        video_elem.text = ""  # asegura cierre <video></video>

        if descripcion:
            p = ET.SubElement(self.body, 'p')
            if descripcion:
                p.text = descripcion
            else:
                p.text = ""

    # ---------------- Método para generar HTML ----------------
    def escribir(self, nombreArchivo):
        arbol = ET.ElementTree(self.raiz)
        ET.indent(arbol)
        arbol.write(nombreArchivo, encoding='utf-8', xml_declaration=False)

        # Añadir DOCTYPE manualmente
        with open(nombreArchivo, "r+", encoding="utf-8") as f:
            contenido = f.read()
            f.seek(0, 0)
            f.write("<!DOCTYPE html>\n" + contenido)

        print(f"Archivo HTML '{nombreArchivo}' generado correctamente.")

def procesarXML_XPath_HTML(archivoXML):
    #ABRO XML Y PARSEO
    try:
        arbol = ET.parse(archivoXML)
    except IOError:
        print ('No se encuentra el archivo ', archivoXML)
        exit()
    except ET.ParseError:
        print("Error procesando en el archivo XML = ", archivoXML)
        exit()

    #DEFINO ESPACIO DE NOMBRES
    raiz = arbol.getroot()
    ns = {'uniovi': 'http://www.uniovi.es'}

    # Datos básicos
    datos = {}
    for tag in ['nombre','distancia','anchura','fecha','hora_inicio','localidad','pais','patrocinador']:
        elem = raiz.find(f'uniovi:{tag}', ns)
        if elem is not None and elem.text:
            if tag in ['distancia','anchura']:
                datos[tag] = f"{elem.text.strip()} {elem.attrib.get('unidad','')}"
            elif tag == 'hora_inicio':
                datos[tag] = f"{elem.text.strip()} {elem.attrib.get('zona','')}"
            else:
                datos[tag] = elem.text.strip()

    # Referencias
    referencias = []
    for ref in raiz.findall('uniovi:referencias/uniovi:referencia', ns):
        texto = ref.text.strip()
        url = ref.attrib.get('url', '')  # Si en XML se define atributo url
        if not url and "http" in texto:  # Intentar extraer URL del texto si existe
            partes = texto.rsplit("http", 1)
            texto = partes[0].strip()
            url = "http" + partes[1].strip()
        referencias.append((texto, url))

    # Galería, DEVUELVE EL ATRIBUTO NOMBRE DE CADA FOTO,
    fotos = [foto.attrib['nombre'] for foto in raiz.findall('uniovi:galeria/uniovi:foto', ns)]

    # Videos, BUSCA TODOS LOS NODOS VIDEO EN VIDEOS, SACA SU NOMBRE Y DESCRIPCION, SI NO TIENEN, "", Y LO GUARDA COMO TUPLA
    videos = [(video.attrib['nombre'], video.attrib.get('descripcion',""))
              for video in raiz.findall('uniovi:videos/uniovi:video', ns)]

    # Resultados
    ganador_elem = raiz.find('uniovi:resultado/uniovi:vencedor', ns)

    if ganador_elem is not None:
        ganador = ganador_elem.text.strip()
        tiempo_ganador = ganador_elem.attrib.get('tiempo','')
    else:
        ganador = ""
        tiempo_ganador = ""

    clasificacion = []
    for puesto in raiz.findall('uniovi:resultado/uniovi:clasificacion_mundial/uniovi:puesto', ns):
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

    # Secciones básicas
    html.addTitulo(datos.get('nombre',''), nivel=1)
    html.addParrafo(f"Distancia: {datos.get('distancia','')}")
    html.addParrafo(f"Anchura: {datos.get('anchura','')}")
    html.addParrafo(f"Fecha: {datos.get('fecha','')}")
    html.addParrafo(f"Hora inicio: {datos.get('hora_inicio','')}")
    html.addParrafo(f"Localidad: {datos.get('localidad','')}")
    html.addParrafo(f"País: {datos.get('pais','')}")
    html.addParrafo(f"Patrocinador: {datos.get('patrocinador','')}")

    # Referencias
    if referencias:
        html.addTitulo("Referencias", nivel=2)
        html.addListaLinks(referencias)

    # Galería de fotos
    if fotos:
        html.addTitulo("Galería de fotos", nivel=2)
        for foto in fotos:
            html.addImagen(f"../{foto}")

    # Videos
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
