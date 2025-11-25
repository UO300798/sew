import xml.etree.ElementTree as ET
import sys
import math

class Svg(object):
    def __init__(self):
        self.raiz = ET.Element(
            'svg',
            xmlns="http://www.w3.org/2000/svg",
            version="2.0",
            viewBox="0 0 800 350",
            width="800",
            height="350"
        )

    def addLine(self, x1, y1, x2, y2, stroke, strokeWidth):
        ET.SubElement(self.raiz, 'line',
                      x1=x1, y1=y1, x2=x2, y2=y2,
                      stroke=stroke,
                      **{'stroke-width': strokeWidth}) # Corregido: stroke-width

    def addPolyline(self, points, stroke, strokeWidth, fill):
        ET.SubElement(self.raiz, 'polyline',
                      points=points, stroke=stroke, fill=fill,
                      **{'stroke-width': strokeWidth})

    def addText(self, texto, x, y, fontFamily, fontSize, style):
        attribs = {
            'x': str(x),
            'y': str(y),
            'font-family': fontFamily,
            'font-size': fontSize,
            'style': style
        }
        elem = ET.SubElement(self.raiz, 'text', **attribs)
        elem.text = texto

    def escribir(self, nombreArchivoSVG):
        arbol = ET.ElementTree(self.raiz)
        ET.indent(arbol)
        arbol.write(nombreArchivoSVG, encoding='utf-8', xml_declaration=True)

def procesarXML_para_altimetria(archivoXML):
    try:
        arbol = ET.parse(archivoXML)
    except (IOError, ET.ParseError) as e:
        print(f"ERROR: No se encuentra o no se puede procesar el archivo XML '{archivoXML}': {e}")
        sys.exit(1)

    raiz = arbol.getroot()
    ns = '{http://www.uniovi.es}'

    puntos_altimetria = []
    distancia_acumulada = 0.0

    alt_origen = float(raiz.find(f'.//{ns}origen/{ns}altitud').text)
    puntos_altimetria.append((0.0, alt_origen, "1"))

    for tramo in raiz.findall(f'.//{ns}tramo'):
        dist = float(tramo.find(f'{ns}distancia').text)
        distancia_acumulada += dist
        altitud = float(tramo.find(f'{ns}coordenadas_finales/{ns}altitud').text)

        sector_elem = tramo.find(f'{ns}sector')
        sector = sector_elem.get('numero') if sector_elem is not None else "1"

        puntos_altimetria.append((distancia_acumulada, altitud, sector))


    return puntos_altimetria


def main():
    archivoXML = "circuitoEsquema.xml"
    archivoSVG = "altimetria.svg"

    puntos = procesarXML_para_altimetria(archivoXML)

    SVG_WIDTH, SVG_HEIGHT = 800, 300
    PADDING = 60
    graph_width = SVG_WIDTH - 2 * PADDING
    graph_height = SVG_HEIGHT - 2 * PADDING

    max_dist = max(p[0] for p in puntos)
    min_alt = min(p[1] for p in puntos)
    max_alt = max(p[1] for p in puntos)
    min_y_ref = min_alt
    max_y_ref = max_alt

    def scale_x(dist):
        return PADDING + (dist / max_dist) * graph_width

    def scale_y(alt):
        return PADDING + graph_height - ((alt - min_y_ref) / (max_y_ref - min_y_ref)) * graph_height

    nuevoSVG = Svg()
    BASE_Y = scale_y(min_y_ref)

    nuevoSVG.addLine(str(PADDING), str(PADDING), str(PADDING), str(BASE_Y), "black", "1")
    nuevoSVG.addLine(str(PADDING), str(BASE_Y), str(SVG_WIDTH - PADDING), str(BASE_Y), "black", "1")

    points_str = f"{scale_x(0):.2f},{BASE_Y:.2f}"
    for dist, alt, _ in puntos:
        points_str += f" {scale_x(dist):.2f},{scale_y(alt):.2f}"
    points_str += f" {scale_x(max_dist):.2f},{BASE_Y:.2f} {scale_x(0):.2f},{BASE_Y:.2f}"

    nuevoSVG.addPolyline(points_str, "red", "2.5", "none")

    nuevoSVG.addText('Altitud (m)', str(PADDING - 40), str(SVG_HEIGHT / 2), 'Verdana', '14',
                 "writing-mode: tb; glyph-orientation-vertical: 0; text-anchor: middle; dominant-baseline: middle;")

    nuevoSVG.addText(f"{min_y_ref:.0f} m", str(PADDING - 10), str(BASE_Y), 'Verdana', '14',
                     "writing-mode: tb; glyph-orientation-vertical: 0; text-anchor: end; dominant-baseline: middle;")

    nuevoSVG.addText(f"{max_y_ref:.0f} m", str(PADDING - 10), str(scale_y(max_y_ref)), 'Verdana', '14',
                     "writing-mode: tb; glyph-orientation-vertical: 0; text-anchor: end; dominant-baseline: middle;")



    sectores_vistos = set()
    LABEL_Y_POS = BASE_Y + 10

    for dist, _, sector in puntos:
        if sector not in sectores_vistos:
            sectores_vistos.add(sector)
            nuevoSVG.addText(f"Sector {sector}", str(scale_x(dist)), str(LABEL_Y_POS), "sans-serif", "12",
                           "writing-mode: tb; glyph-orientation-vertical: 0; text-anchor: start;")

    nuevoSVG.addText("Final", str(scale_x(max_dist)), str(LABEL_Y_POS), "sans-serif", "14",
                    "writing-mode: tb; glyph-orientation-vertical: 0; text-anchor: start;")

    nuevoSVG.escribir(archivoSVG)

if __name__ == "__main__":
    main()
