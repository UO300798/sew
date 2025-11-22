# -*- coding: utf-8 -*-
import xml.etree.ElementTree as ET
import sys

class Kml:
    def __init__(self):
        self.raiz = ET.Element('kml', xmlns="http://www.opengis.net/kml/2.2")
        self.doc = ET.SubElement(self.raiz,'Document')

    def addLineString(self, nombre, extrude, tesela, listaCoordenadas, modoAltitud, color, width):
        ET.SubElement(self.doc,'name').text = nombre
        pm = ET.SubElement(self.doc,'Placemark')
        ls = ET.SubElement(pm, 'LineString')
        ET.SubElement(ls,'extrude').text = extrude
        ET.SubElement(ls,'tessellate').text = tesela
        ET.SubElement(ls,'coordinates').text = listaCoordenadas
        ET.SubElement(ls,'altitudeMode').text = modoAltitud

        estilo = ET.SubElement(pm, 'Style')
        linea = ET.SubElement(estilo, 'LineStyle')
        ET.SubElement(linea, 'color').text = color
        ET.SubElement(linea, 'width').text = str(width)

    def escribir(self, nombreArchivoKML):
        arbol = ET.ElementTree(self.raiz)
        ET.indent(arbol)
        arbol.write(nombreArchivoKML, encoding='utf-8', xml_declaration=True)

def main():
    archivoXML = "circuitoEsquema.xml"
    archivoKML = "circuito.kml"

    try:
        arbol = ET.parse(archivoXML)
    except IOError:
        print('No se encuentra el archivo ', archivoXML)
        sys.exit(1)
    except ET.ParseError:
        print("Error procesando en el archivo XML = ", archivoXML)
        sys.exit(1)

    raiz = arbol.getroot()
    ns = '{http://www.uniovi.es}'

    coordenadas = []
    coordenadas.append((
            raiz.find(f'.//{ns}origen/{ns}longitud').text,
            raiz.find(f'.//{ns}origen/{ns}latitud').text
        ))

    for tramo in raiz.findall(f'.//{ns}tramo'):
        coordenadas.append((
            tramo.find(f'{ns}coordenadas_finales/{ns}longitud').text,
            tramo.find(f'{ns}coordenadas_finales/{ns}latitud').text
            ))


    coordenadas_str = "\n".join([f"{lon},{lat}" for lon, lat in coordenadas])

    nuevoKML = Kml()
    nuevoKML.addLineString(
        nombre="Circuito KML",
        extrude="1",
        tesela="1",
        listaCoordenadas=coordenadas_str,
        modoAltitud="relativeToGround",
        color="ff0000ff",
        width="5"
    )
    nuevoKML.escribir(archivoKML)

if __name__ == "__main__":
    main()