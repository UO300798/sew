# -*- coding: utf-8 -*-
import xml.etree.ElementTree as ET
import sys

class Kml:
    def __init__(self):
        """
        Crea el elemento raíz y el espacio de nombres
        """
        self.raiz = ET.Element('kml', xmlns="http://www.opengis.net/kml/2.2")
        self.doc = ET.SubElement(self.raiz,'Document')


    def addPlacemark(self,nombre,descripcion,long,lat,alt,modoAltitud):
        """
        Añade un elemento <Placemark> con puntos <Point>
        """
        pm=ET.SubElement(self.doc,'Placemark')
        ET.SubElement(pm,'name').text=nombre
        ET.SubElement(pm,'description').text = descripcion
        punto = ET.SubElement(pm,'Point')
        ET.SubElement(punto,'coordinates').text = '{},{},{}'.format(long,lat,alt)
        ET.SubElement(punto,'altitudeMode').text = modoAltitud



    def addLineString(self,nombre,extrude,tesela, listaCoordenadas, modoAltitud, color, ancho):
        """
        Añade un elemento <Placemark> con líneas <LineString>
        """
        ET.SubElement(self.doc,'name').text = nombre
        pm = ET.SubElement(self.doc,'Placemark')
        # Añadimos nombre al Placemark de la línea también
        #ET.SubElement(pm,'name').text = "Trazado del Circuito"
        ls = ET.SubElement(pm, 'LineString')
        ET.SubElement(ls,'extrude').text = extrude

        ET.SubElement(ls,'tessellate').text = tesela
        ET.SubElement(ls,'coordinates').text = listaCoordenadas
        ET.SubElement(ls,'altitudeMode').text = modoAltitud
        estilo = ET.SubElement(pm, 'Style')
        linea = ET.SubElement(estilo, 'LineStyle')
        ET.SubElement (linea, 'color').text = color
        ET.SubElement (linea, 'width').text = str(ancho)

    def escribir(self,nombreArchivoKML):
        """
        Escribe el archivo KML con declaración y codificación
        """
        arbol = ET.ElementTree(self.raiz)
        ET.indent(arbol)
        arbol.write(nombreArchivoKML, encoding='utf-8', xml_declaration=True)
        print(f"Archivo KML '{nombreArchivoKML}' generado correctamente.")

    def ver(self):
        """
        Muestra el archivo KML. Se utiliza para depurar
        """
        print("\nElemento raiz = ", self.raiz.tag)

        if self.raiz.text != None:
            print("Contenido = " , self.raiz.text.strip('\n'))
        else:
            print("Contenido = " , self.raiz.text)
        print("Atributos = " , self.raiz.attrib)

        for hijo in self.raiz.findall('.//'):
            print("\nElemento = " , hijo.tag)
            if hijo.text != None:
                print("Contenido = ", hijo.text.strip('\n'))
            else:
                print("Contenido = ", hijo.text)
            print("Atributos = ", hijo.attrib)



def procesarXML_XPath_KML(archivoXML):
    """
    Función procesarXML_XPath_KML(archivoXML)
    Lee un archivo XML de circuito, extrae nombre y coordenadas usando XPath,
    y devuelve el nombre y una cadena de coordenadas formateada para KML.
    """
    try:
        arbol = ET.parse(archivoXML)
    except IOError:
        print ('No se encuentra el archivo ', archivoXML)
        exit()
    except ET.ParseError:
        print("Error procesando en el archivo XML = ", archivoXML)
        exit()

    raiz = arbol.getroot()


    ns = {'uniovi': 'http://www.uniovi.es'}
    lista_coordenadas_tuplas = []
    nombre_circuito = ""

    try:
        elem_nombre = raiz.find('uniovi:nombre', ns)
        if elem_nombre is not None and elem_nombre.text:
            nombre_circuito = elem_nombre.text.strip()

        origen = raiz.find('.//uniovi:origen', ns)
        if origen is None:
             print("ERROR: No se encontró el elemento <origen> en el XML.")
             sys.exit(1)

        lat_origen = origen.find('uniovi:latitud', ns).text
        lon_origen = origen.find('uniovi:longitud', ns).text
        #alt_origen = origen.find('uniovi:altitud', ns).text
        lista_coordenadas_tuplas.append((lon_origen, lat_origen))

        coordenadas_tramos = raiz.findall('.//uniovi:tramo/uniovi:coordenadas_finales', ns)
        if not coordenadas_tramos:
             print("ADVERTENCIA: No se encontraron <coordenadas_finales> en los <tramo>.")

        for coord_final in coordenadas_tramos:
            lat = coord_final.find('uniovi:latitud', ns).text
            lon = coord_final.find('uniovi:longitud', ns).text
            #alt = coord_final.find('uniovi:altitud', ns).text
            lista_coordenadas_tuplas.append((lon, lat))

    except AttributeError:
        print("hay algun error")
        sys.exit(1)

    if not lista_coordenadas_tuplas:
        print("no hay coordenadas validas")
        sys.exit(1)

    coordenadas_kml_string = "\n".join([f"{lon},{lat}" for lon, lat in lista_coordenadas_tuplas])

    return nombre_circuito, coordenadas_kml_string

def main():
    """Función principal: procesa el XML y genera el KML usando la clase Kml."""
    archivoXML = "circuitoEsquema.xml"
    archivoKML = "circuito.kml"

    print(f"Procesando archivo XML: '{archivoXML}'...")
    nombre_circuito_extraido, coordenadas_str = procesarXML_XPath_KML(archivoXML)

    print(f"Generando archivo KML: '{archivoKML}'...")
    nuevoKML = Kml()

    nuevoKML.addLineString(
        nombre="Circuito KML",
        extrude="1",
        tesela="1",
        listaCoordenadas=coordenadas_str,
        modoAltitud="relativeToGround",

        color="ff0000ff",
        ancho="5"
    )


    nuevoKML.escribir(archivoKML);


if __name__ == "__main__":
    main()