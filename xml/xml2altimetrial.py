# -*- coding: utf-8 -*-
import xml.etree.ElementTree as ET
import sys
import math

# --- Clase Svg (Sin cambios, ya optimizada) ---
class Svg(object):
    def __init__(self, width=800, height=300):
        self.raiz = ET.Element('svg', xmlns="http://www.w3.org/2000/svg", version="2.0",
                                width=str(width), height=str(height))

    def addRect(self,x,y,width,height,fill, strokeWidth,stroke):
        ET.SubElement(self.raiz,'rect', x=str(x), y=str(y), width=str(width), height=str(height),
                      fill=fill, strokeWidth=str(strokeWidth), stroke=stroke)

    def addLine(self,x1,y1,x2,y2,stroke,strokeWidth):
        ET.SubElement(self.raiz,'line', x1=str(x1), y1=str(y1), x2=str(x2), y2=str(y2),
                      stroke=stroke, strokeWidth=str(strokeWidth))

    def addPolyline(self, points, stroke, strokeWidth, fill):
        ET.SubElement(self.raiz, 'polyline', points=points, stroke=stroke,
                      strokeWidth=str(strokeWidth), fill=fill)

    def addText(self,texto,x,y,fontFamily,fontSize,style):
        element = ET.SubElement(self.raiz,'text', x=str(x), y=str(y), fontFamily=fontFamily,
                                fontSize=str(fontSize), style=style)
        element.text = texto

    def addVerticalText(self, texto, x, y, size="12"):
        """Añade texto con orientación vertical, como en la imagen de referencia."""
        style = "writing-mode: tb; glyph-orientation-vertical: 0; text-anchor: start;"
        self.addText(texto, x, y, "sans-serif", size, style)

    def escribir(self, nombreArchivoSVG):
        arbol = ET.ElementTree(self.raiz)
        ET.indent(arbol)
        arbol.write(nombreArchivoSVG, encoding='utf-8', xml_declaration=True)

# --------------------------------------------------------------------------
# --- Función procesarXML_para_altimetria (Sin cambios) ---
# --------------------------------------------------------------------------
def procesarXML_para_altimetria(archivoXML):
    """
    Lee XML del circuito y extrae distancia, altitud Y sector.
    Devuelve lista de tuplas: [(dist_acumulada, altitud, sector), ...].
    """
    try:
        arbol = ET.parse(archivoXML)
    except (IOError, ET.ParseError) as e:
        print(f"ERROR: No se encuentra o no se puede procesar el archivo XML '{archivoXML}': {e}")
        sys.exit(1)
    raiz = arbol.getroot()
    ns = {'uniovi': 'http://www.uniovi.es'}
    puntos_altimetria = []
    distancia_acumulada = 0.0
    try:
        # 1. Punto de Origen (distancia 0, altitud, sector 1)
        origen = raiz.find('.//uniovi:origen', ns)
        alt_origen = float(origen.find('uniovi:altitud', ns).text)
        puntos_altimetria.append((distancia_acumulada, alt_origen, "1"))
        tramos = raiz.findall('.//uniovi:tramo', ns)
        for tramo in tramos:
            dist_tramo = float(tramo.find('uniovi:distancia', ns).text)
            coord_final = tramo.find('uniovi:coordenadas_finales', ns)
            alt_final = float(coord_final.find('uniovi:altitud', ns).text)
            sector_elem = tramo.find('uniovi:sector', ns)
            sector_numero = sector_elem.get('numero') if sector_elem is not None else "1"
            distancia_acumulada += dist_tramo
            puntos_altimetria.append((distancia_acumulada, alt_final, sector_numero))
    except (AttributeError, ValueError, TypeError) as e:
        print(f"ERROR CRÍTICO: Fallo al extraer datos del XML: {e}")
        sys.exit(1)
    return puntos_altimetria

# --------------------------------------------------------------------------
# --- Función Principal (CON EJE Y) ---
# --------------------------------------------------------------------------
def main():
    archivoXML = "circuitoEsquema.xml"
    archivoSVG = "altimetria.svg" # Nuevo nombre de archivo

    # 1. Extraer datos con sectores
    puntos_completos = procesarXML_para_altimetria(archivoXML)

    # 2. Configurar dimensiones y padding
    SVG_WIDTH, SVG_HEIGHT = 800, 300
    # Aumentamos PADDING para dejar espacio para la etiqueta "Altitud (m)"
    PADDING = 60
    graph_width = SVG_WIDTH - 2 * PADDING
    graph_height = SVG_HEIGHT - 2 * PADDING

    # 3. Calcular escalas y límites
    distancias = [p[0] for p in puntos_completos]
    altitudes = [p[1] for p in puntos_completos]

    max_dist = max(distancias) if distancias else 1
    min_alt_base = min(altitudes) if altitudes else 0
    min_y_ref = math.floor(min_alt_base / 5) * 5 # Redondear hacia abajo para la línea base
    max_alt = math.ceil(max(altitudes) / 5) * 5 # Redondear hacia arriba

    range_dist = max_dist if max_dist > 0 else 1
    range_alt = max_alt - min_y_ref if max_alt > min_y_ref else 1

    # Funciones de escalado
    def scale_x(dist):
        return PADDING + (dist / range_dist) * graph_width
    def scale_y(alt):
        return PADDING + graph_height - ((alt - min_y_ref) / range_alt) * graph_height

    # 4. Inicializar SVG (Añadir espacio para etiquetas de sector)
    LABEL_Y_OFFSET = 70
    nuevoSVG = Svg(width=SVG_WIDTH, height=SVG_HEIGHT + LABEL_Y_OFFSET)

    # --- DIBUJO DE EJES (NUEVA LÓGICA REINTRODUCIDA) ---
    BASE_Y = scale_y(min_y_ref) # Coordenada Y de la línea horizontal de la base

    # Dibuja el Eje Y (Altitud)
    # Va desde la parte superior del padding (Y=60) hasta la base del gráfico
    nuevoSVG.addLine(PADDING, PADDING, PADDING, BASE_Y, "black", "1")

    # Dibuja el Eje X (Línea de base)
    nuevoSVG.addLine(PADDING, BASE_Y, SVG_WIDTH - PADDING, BASE_Y, "black", "1")

    # 5. Generar puntos para el perfil
    base_points = [f"{scale_x(0):.2f},{BASE_Y:.2f}"] # Base izquierda (sobre el eje X)

    # Puntos del perfil (Altitud real)
    for dist, alt, sector in puntos_completos:
        base_points.append(f"{scale_x(dist):.2f},{scale_y(alt):.2f}")

    # Punto de la base derecha
    base_points.append(f"{scale_x(max_dist):.2f},{BASE_Y:.2f}")

    # Cerrar el contorno
    base_points.append(f"{scale_x(0):.2f},{BASE_Y:.2f}")
    points_str = " ".join(base_points)

    # 6. Dibujar la polilínea completa (Línea roja del perfil y línea de base inferior)
    nuevoSVG.addPolyline(points=points_str, stroke="red", strokeWidth="2.5", fill="none")

    # 7. Etiquetas del Eje Y (Altitud en Metros)

    # Etiqueta Altitud (m) (Vertical)
    nuevoSVG.addText("Altitud (m)", PADDING - 40, SVG_HEIGHT / 2, "sans-serif", "14",
                     style="writing-mode: tb; text-anchor: middle;")

    # Valor Mínimo (junto a la línea de base)
    nuevoSVG.addText(f"{min_y_ref:.0f} m", PADDING - 10, BASE_Y, "sans-serif", "12",
                     style="text-anchor: end; dominant-baseline: middle;")

    # Valor Máximo (en la parte superior)
    nuevoSVG.addText(f"{max_alt:.0f} m", PADDING - 10, scale_y(max_alt), "sans-serif", "12",
                     style="text-anchor: end; dominant-baseline: middle;")

    # 8. Etiquetas de Sector (S1, S2, S3, S4, Final)

    etiquetas_sector = {}
    etiquetas_sector['1'] = puntos_completos[0][0] # Inicio S1
    current_sector = '1'

    for dist, alt, sector_num in puntos_completos:
        if sector_num != current_sector and sector_num not in etiquetas_sector:
             etiquetas_sector[sector_num] = dist
             current_sector = sector_num

    # Posición Y para el texto vertical, justo debajo de la línea base
    LABEL_Y_POS = BASE_Y + 10

    for sector_num in sorted(etiquetas_sector.keys()):
        dist_inicio = etiquetas_sector[sector_num]
        x_coord = scale_x(dist_inicio)
        nombre_etiqueta = f"Sector {sector_num}"

        # Replicar el estilo vertical
        nuevoSVG.addVerticalText(nombre_etiqueta, x=x_coord, y=LABEL_Y_POS, size="12")

    # Añadir la etiqueta "Final"
    x_final = scale_x(max_dist)
    nuevoSVG.addVerticalText("Final", x=x_final, y=LABEL_Y_POS, size="14")

    # 9. Guardar el archivo SVG
    nuevoSVG.escribir(archivoSVG)

    print(f"✅ Archivo '{archivoSVG}' generado correctamente con perfil, etiquetas de sector y eje de Altitud (m).")

if __name__ == "__main__":
    main()