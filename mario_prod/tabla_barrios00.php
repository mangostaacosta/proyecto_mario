<?php
/****************************************************************************
  20150120  >> tabla_barrios00.php
  Proyecto: Obsevatorio Mario
  Realiza un filtro sobre los resultados de indicadores grupo y muestra "plain vanilla"
  los tres principales arreglos
******************************************************************************/

	require_once 'header.php' ; 
	require_once 'indicadores_grupo.php' ;
	
	$manual['tex'] = 'Esta opción presenta información que incluye la totalidad de los inmuebles vigentes en la fecha escogida. En este caso se presentan 4 tablas, las 3 últimas son las indicadas en caso de que se quiera copiar la informació para procesarla usando una hoja de cálculo como excel.
	La primera tabla muestra el promedio del campo elegido (e.g. precio_metro) al lado del promedio se presenta entre parentésis la cantida de datos disponibles para calcular el valor promedio. A mayor cantidad de datos más confianza.
	La segunda tabla muestra la misma información del promedio; en este caso los npumeros no tienen formato por que se pueden descargar fácilmente a una hoja de cálculo.
	La tercera tabla muestra la información de la cantidad de datos en cada caso. También es fácil de descargar a hoja de cálculo. Es decir que la primera tabla resume el contendio de estas dos tablas.
	La cuarta table presenta información de una manera más vertical, y complementa los datos de promedios. En este caso la tabla incluye las columnas <b>estrato</b> y la columna <b>antiguedad_rg</b> las cuales sirven para identificar a qué estrato y qué antiguedad pertenece la información de cada fila.
	A la derecha de estas columnas se puede encontrar la información del promedio del indicador pero adicionalmente se incluye información de mínimo y máximo valor incluido en la muestra.
	
	En la parte inferior se podrá encontrar una explicación detallada de las columnas de las tablas.
	' . $manual['tex'] ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Operación > Tablas > Todos' ) ;
	
	
	MsjE("Inmuebles incluidos en la búsqueda: $conteo_total ") ;
	$n = array() ;	
	
	$g_html = '' ;	
	$g_html .= html_arreglo_bi( $arr_cuenta , 1 , $n , "Tabla por Barrios") ;
	$g_html .= '<br>' ;	
	$g_html .= html_arreglo_bi( $arr_salida , 1 , $n , "Estadísticas por Barrio") ;
	$g_html .= '<br>' ;
	$g_html .= html_arreglo_bi( $arr_salida1 , 1 , $n , "Promedios por Barrio") ;
	$g_html .= '<br>' ;	
	$g_html .= html_arreglo_bi( $arr_salida2 , 1 , $n , "Conteos por Barrio Version HORIZ") ;
	$g_html .= '<br>' ;	
	$g_html .= html_arreglo_bi( $arr_salidabd , 1 , $n , "Estadísticas Generales sin Filtro") ;
	$g_html .= '<br>' ;
	
	echo $g_html ;

?>