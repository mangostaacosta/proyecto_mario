<?php
 /****************************************************************************
  20150116  >> manual_captura_autom.php
  Proyecto: Obsevatorio Mario
  Código que define la matriz con el texto de ayuda para las página relacionadas con el "cargue automático" de información"
  POST: $manual
 ******************************************************************************/
	
	$manual['tit'] = 'Captura Automática' ;
	$manual['tex'] = 'Este conjunto de opciones permiten ingresar información de internet (metrocuadrado.com) automáticamente. Se incluyen las siguientes sub-opciones de menú:
	<b>Descarga MC:</b> Permite ejecutar el proceso de descarga del archivo plano de MC, presenta los valores estandarizados de descarga diaria. En la última versión permite descargar un máximo de 1000 apartamentos. Para el campo de "Antigüedad" la última opción ESPECIAL sirve para generar en un sólo proceos los 5 archivos correspondientes a las 5 categorías de antigüedad que tiene el menú desplegable.   
	<b>Reset Captura:</b> Inicializa los archivos donde se guarda la información. Normalmente se debe ejecutar antes de arrancar el proceso a no ser que se quiera continuar un proceso de cargue automático que haya quedado a la mitad.
	<b>Datos N1 estándar:</b> Arma el archivo para la recopilación de la información Nivel 1 de los inmuebles que están listados en los archivos estandarizados de descarga de MC.
	Es recomendable generar todos los archivos para una fecha determinada antes de ejecutar la siguiente opción "Datos N2", no obstante si son muchos datos, se sugiere dividir el proceso para no saturar el servidor.
	<b>Datos N1 especial:</b> Arma el archivo para la recopilación de la información Nivel 1 de los inmuebles que estan listados en archivos NO estandarizados. Se utiliza cuando se requiere infomración diferente a la estandarizada. En caso de usar esta opción se debe adicionar el siguiente texto al final de la URL:
	"?fecha=AAAA-MM-DD&zona=(nort|nocc|chap)&antig=(new|0a10|10a20|20a)&tipo=(apartamento|casa)&archivo=nombre_archivo.txt"
	<b>Datos N2:</b> Recopila la información de nivel 2 para el archivo que se armó utilizando las 2 opciones anteriores. Dado que el código consulta cada página independientemente, la ejecución toma varios minutos.
	En caso de que el proceso se cierre por exceso de tiempo se debe volver a ejecutar adicionando el siguiente texto al final de la URL:
	"?indice=XXXX" , donde XXXX es el múltiplo de 60 anterior al último registro procesado.
	<b>Insertar a BD:</b> La opción se debe ejecutar después de haber generado el archivo de resultados Nivel 2 (con la opción anterior). Carga la información en la Base de Datos realizando validaciones de gemelos y actualizando la infromación de los inmuebles anteiores. Al final muestra un resumen de los registros y cambios realizados en la Base de Datos.
	<b>Datos N3 (seg):</b> Esta opción ya no es funcional debido a los cambios que tuvo M2 a finales del 2016: <i>NO VIGENTE: Recopila la información Nivel 3 únicamente para los inmuebles que estén en los barrios que hayan sido marcados como barrios para "seguimiento". Dado que consulta cada página independeinte puede tardar algunos minutos. Al finalizar presetna un resumen de los cambios realizados.
	En caso de que el proceso se cierre por exceso de tiempo se debe volver a ejecutar adicionando el sguiente texto al final de la URL:
	"?indice=XXXX" , donde XXXX es el múltiplo de 60 anterior al último registro procesado.</i>
	
	Algunas opciones presentan un formulario donde se requiere introudir la fecha de ejecución del proceso. EL formato a utilizar es AAAA-MM-DD.	
	' ;
	
	echo VentanaHelp( $manual ) . "<br>" ;
?>