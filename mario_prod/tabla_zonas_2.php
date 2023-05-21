<?php
/****************************************************************************
  20150120  >> tabla_zonas.php
  Proyecto: Obsevatorio Mario
  Realiza unas búsquedas agrupadas para un nivel menos detallado que id_barrio  
******************************************************************************/

	require_once 'header.php' ; 
	require_once 'indicadores_grupo.php' ;
	
	$manual['tex'] = 'Esta opción presenta información que incluye la totalidad de los inmuebles vigentes en la fecha escogida. Se inluyen 3 tablas que muestran diferentes niveles de agrupación.
	La primera tabla muestra las estadísticas agrupadas a nivel de SECTOR, estrato y antigüedad.
	La segunda tabla muestra las estadísticas agrupadas a nivel de ZONA, estrato y antigüedad.
	La tercera tabla muestra las estadísticas agrupadas a nivel de estrato y antigüedad.
	
	Las tablas incluyen las siguientes columnas:
	
	<b>zona</b>: muestra la zona de la ciudad (norte|noroccindete|cahpinero), es la clasificación más amplia, cada zona incluye varios sectores.
	<b>sector</b>: muestra el sector espécifico dentro de la zona de la ciudad, esta clasificación se basa en las tablas estandarizadas de metrocuadrado.com. Cada sector incluye varios barrios.
	<b>estrato</b>: muestra el estrato al que corresponde la fila.
	<b>antiguedad_rg</b>:muestra el rango de atigüedad (new|0a10|10a20|20a) al que corresponde la fila
	<b>cuenta</b>: muestra el número de registros vigentes en la fecha de cálculo.
	<b>AVG(precio_metro)</b>: muestra el promedio del indicador.
	<b>MIN(precio_metro)</b>: muestra el valor MÍNIMO del indicador.
	<b>MAX(precio_metro)</b>: muestra el valor MÁXIMO del indicador.
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Operación > Tablas > Por zonas' ) ;
	
	/*
	//Se crea y utiliza el formulario para capturar las variables de entrada a la página
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->Insertar('f_campo','Escoja campo (e.g. precio / precio_metro):','precio_metro') ;
	
	$forma->siempre = TRUE ;
	$forma->Imprimir() ;
	$f_fecha = $forma->Sacar('f_fecha') ;
	$f_campo = $forma->Sacar('f_campo') ;
	*/
	
	// Los queries a continuación, a pesar de que están correctos salen directamente de t_inmueble
	// lo ideal SERÍA sacarlos de una nueva tabla que archive los resultados que se sacan arriba
	
	/* $sql = "
		SELECT estrato, antiguedad_rg, t_barrio.zona, t_barrio.sector, COUNT($f_campo) AS cuenta, AVG($f_campo) , MIN($f_campo) , MAX($f_campo)
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE b_activo = 1 AND b_normal = 1
		GROUP BY zona, sector, estrato, antiguedad_rg
		ORDER BY estrato DESC, antiguedad_rg, sector
	" ; */
	//20161210: cambio del query para que agrupe por sectores
	$sql = "
		SELECT estrato, antiguedad_rg, t_barrio.sector, COUNT($f_campo) AS cuenta, AVG($f_campo) , MIN($f_campo) , MAX($f_campo)
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE b_activo = 1 AND b_normal = 1
		GROUP BY sector, estrato, antiguedad_rg
		ORDER BY sector, estrato DESC, antiguedad_rg
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	foreach ( $arr_salidabd as $key => $val ){
		foreach ( $val as $key1 => $val1 ){
			$arr_salidabd[$key][$key1] = formato_n( $val1 , 0 ) ;
		}
	}

	$n = array() ;
	$g_html = '' ;
	$g_html .= html_arreglo_bi( $arr_salidabd , 1 , $n , "Estadísticas por SECTOR") ;
	$g_html .= '<br>' ;	
	
	$sql = "
		SELECT estrato, antiguedad_rg, t_barrio.zona, COUNT($f_campo) AS cuenta, AVG($f_campo) , MIN($f_campo) , MAX($f_campo)
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE b_activo = 1 AND b_normal = 1 
		GROUP BY zona, estrato, antiguedad_rg
		ORDER BY estrato DESC , antiguedad_rg, zona
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	foreach ( $arr_salidabd as $key => $val ){
		foreach ( $val as $key1 => $val1 ){
			$arr_salidabd[$key][$key1] = formato_n( $val1 , 0 ) ;
		}
	}

	$g_html .= html_arreglo_bi( $arr_salidabd , 1 , $n , "Estadísticas por ZONA") ;
	$g_html .= '<br>' ;
	
	$sql = "
		SELECT estrato, antiguedad_rg, COUNT($f_campo) AS cuenta, AVG($f_campo) , MIN($f_campo) , MAX($f_campo)
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE b_activo = 1 AND b_normal = 1 
		GROUP BY estrato, antiguedad_rg
		ORDER BY estrato DESC, antiguedad_rg
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	foreach ( $arr_salidabd as $key => $val ){
		foreach ( $val as $key1 => $val1 ){
			$arr_salidabd[$key][$key1] = formato_n( $val1 , 0 ) ;
		}
	}

	$g_html .= html_arreglo_bi( $arr_salidabd , 1 , $n , "Estadísticas por ESTRATO") ;
	$g_html .= '<br>' ;
	//*/
	
	echo $g_html ;

?>