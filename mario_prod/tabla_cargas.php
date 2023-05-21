<?php
/****************************************************************************
  20150106  >> tabla_cargas.php
  Proyecto: Obsevatorio Mario
  Muesta la cantidad de inmuebles cargados en cada fecha de la BD
******************************************************************************/
	
	require_once 'header.php' ; 
	
	$manual['tit'] = 'Conteo Fechas' ;
	$manual['tex'] = 'Esta opción presenta un listado de la cantidad de registros ingresados al sistema en cada fecha, así como sus fechas de vigencia. Sirve para monitorear los resultados del proceso de captura externa. Los datos se presentan en tres tablas. La primera tabla muestra los datos filtrados hasta la fecha definida por el usuario. La segunda tabla muestra todos los datos ingresados en el sistema sin filtros. La tercera tabla muestra un resumen por cada fecha de captura de datos. 
	
	En primer lugar se presenta un formulario para ingresar la fecha de corte.	
	<b>Fecha Proceso (obligatorio)</b>: se debe escoger la fecha del análisis, el formato es AAAA-MM-DD
	
	A continuación se muestran las tablas de resultados. Incluyen las siguientes columnas:	
	<b>fecha_ini:</b> fecha de ingreso al sistema.
	<b>fecha_fin:</b> fecha de posible pérdida de vigencia de los inmuebles en el sistema.
	<b>fecha:</b> fecha de la última edición/actualización de datos de los inmuebles en el sistema. 
	<b>conteo:</b> cantidad de inmuebles que corresponden a las fechas descritas anteriormente.
	
	Por ejemplo, si una fila presenta la siguiente información en cada columna: 
	fecha_ini = 2015-01-05 
	fecha_fin = 2015-03-03
	fecha = 2015-02-17
	conteo = 9
	Signfica que en la Base de Datos del sistema hay 9 inmuebles que fueron ingresados incialmente el 2015-01-05, los cuales pierden vigencia a partir del 2015-03-03 y cuya última fecha de actulización de datos fue el 2015-02-17.
	
	La penúltima tabla presenta un resumen de los datos de la segunda tabla. En este caso sólo se incluyen las columnas:
	<b>fecha_ini:</b> fecha de ingreso al sistema.
	<b>conteo:</b> cantidad de inmuebles que corresponden a las fechas descritas anteriormente.
	En esta tabla se muestran los inmuebles ingresados en cada fecha sin discriminar por fecha de finalización ó fecha de última actualización.
	
	La última tabla presetna información similar a la anterior, aunque ene este caso se claisfica por fecha de finalización:
	<b>fecha_fin:</b> fecha de cuando el inmueble perdió/pierde vigencia.
	<b>conteo:</b> cantidad de inmuebles que corresponden a las fechas descritas anteriormente.
	
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Administración > Conteo Fechas' ) ;

	
	//Se crea y utiliza el formulario para captruar las variables de entrada a la página
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	//$forma->Insertar('f_campo','Escoja campo (e.g. precio / precio_metro):','precio_metro') ;
	$forma->siempre = TRUE ;
	$forma->Imprimir() ;
	$f_fecha = $forma->Sacar('f_fecha') ;
	//$f_campo = $forma->Sacar('f_campo') ;
	
	
	$n = array() ;
	
	$where = " WHERE (fecha_ini <= '$f_fecha') AND ((fecha_fin > '$f_fecha') OR (fecha_fin IS NULL)) " ;
	$sql = "
		SELECT fecha_ini,fecha_fin,fecha,COUNT(fecha) AS conteo FROM t_inmueble 
		$where
		GROUP BY fecha_ini,fecha_fin,fecha
		ORDER BY fecha_ini,fecha,fecha_fin
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	
	$g_html .= html_arreglo_bi( $arr_salidabd , 1 , $n , "FECHA $f_fecha") ;
	$g_html .= '<br>' ;
	
	$where = " WHERE 1 " ;
	$sql = "
		SELECT fecha_ini,fecha_fin,COUNT(fecha) AS conteo FROM t_inmueble 
		$where
		GROUP BY fecha_ini,fecha_fin
		ORDER BY fecha_ini,fecha_fin
	" ;
	
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	
	$g_html .= html_arreglo_bi( $arr_salidabd , 1 , $n , "TODAS LAS CARGAS") ;
	$g_html .= '<br>' ;	
	
	
	$sql = "
		SELECT fecha_ini,COUNT(fecha) AS conteo FROM t_inmueble 	
		GROUP BY fecha_ini
		ORDER BY fecha_ini
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	
	$g_html .= html_arreglo_bi( $arr_salidabd , 1 , $n , "INICIO CARGAS GENERALES") ;
	$g_html .= '<br>' ;
	
	$sql = "
		SELECT fecha_fin,COUNT(fecha) AS conteo FROM t_inmueble 	
		GROUP BY fecha_fin
		ORDER BY fecha_fin
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	
	$g_html .= html_arreglo_bi( $arr_salidabd , 1 , $n , "FINALIZACIÓN CARGAS GENERALES") ;
	$g_html .= '<br>' ;
		
	
	echo $g_html ;	
	
?>