<?php
/****************************************************************************
  20150120  >> tabla_zonas.php
  Proyecto: Obsevatorio Mario
  Realiza unas búsquedas agrupadas para un nivel menos detallado que id_barrio  
******************************************************************************/

	require_once 'header.php' ; 
	
	//Se crea y utiliza el formulario para capturar las variables de entrada a la página
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->Insertar('f_campo','Escoja campo (e.g. precio / precio_metro):','precio_metro') ;
	
	$forma->siempre = TRUE ;
	$forma->Imprimir() ;
	$f_fecha = $forma->Sacar('f_fecha') ;
	$f_campo = $forma->Sacar('f_campo') ;
	
	
	// Los queries a continuación, a pesar de que están correctos salen directamente de t_inmueble
	// lo ideal SERÍA sacarlos de una nueva tabla que archive los resultados que se sacan arriba
	
	$sql = "
		SELECT t_barrio.sector, estrato, COUNT($f_campo) AS cuenta, AVG($f_campo) , MIN($f_campo) , MAX($f_campo)
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE b_activo = 1 AND b_normal = 1 AND t_inmueble.fecha = '$f_fecha' 
		GROUP BY sector, estrato
		ORDER BY sector, estrato DESC
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
		SELECT t_barrio.zona, estrato, COUNT($f_campo) AS cuenta, AVG($f_campo) , MIN($f_campo) , MAX($f_campo)
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE b_activo = 1 AND b_normal = 1 AND t_inmueble.fecha = '$f_fecha' 
		GROUP BY zona, estrato
		ORDER BY zona, estrato DESC
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
		SELECT estrato, COUNT($f_campo) AS cuenta, AVG($f_campo) , MIN($f_campo) , MAX($f_campo)
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE b_activo = 1 AND b_normal = 1 AND t_inmueble.fecha = '$f_fecha' 
		GROUP BY estrato
		ORDER BY estrato DESC
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