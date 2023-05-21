<?php
 /****************************************************************************
  20150123  >> seguir_barrios.php
  Proyecto: Obsevatorio Mario
  Saca la información de la BD con el listado de todos los barrios
  y se marcan para seguimiento
  ******************************************************************************/

	require_once 'header.php' ;
	
	$manual['tit'] = 'Barrios' ;
	$manual['tex'] = 'Esta funcionalidad permite visualizar y escoger los barrios a ser marcados para SEGUIMIENTO. La información se presenta en una tabla donde la última columna indica el estado de cada barrio. La tabla incluye las siguientes columnas:	
	<b>Ubicación:</b> muestra la Zona, Sector y nombre de Barrio correspondiente a la fila  
	<b>id_Barrio:</b> presenta el identificador único de barrio acorde con la parametrización en la Base de Datos del sistema.
	<b>Seguir?:</b> muestra si el barrio se encuentra marcado o no para seguimiento. Las filas inciales de la tabla muestran aquellos que se encuentran marcados para SEGUIMIENTO, a continuación se muestran los que no están marcados ordenados alfabéticamente.
	
	Para marcar/desmarcar el SEGUIMIENTO de un barrio se debe hacer click en la última columna de la fila escogida. Al finalizar el proceso de marcación se debe hacer click en el Botón "Enviar", ubicado en la parte superior del formulario. 
	' ;	
	echo VentanaHelp( $manual ) . "<br>" ;
	echo TituloPagina( 'Operación > Seguimiento > Barrios' ) ;
  	
	global $g_conexion ;
	//global $g_fecha ;	
	//$pag = 2 ;
	
	if (isset( $_GET['arr_barrio'] )){
		$arr_barrio = $_GET['arr_barrio'] ;		
	}else{
		//nada
	}
	
	if ( isset( $arr_barrio )){		
	//ya se escogieron los barrios para seguimiento
		//reinicializar las banderas a 0s
		$sql = "
			UPDATE t_barrio SET b_sgto='0' WHERE 1 
		" ;
		$g_conexion->execute( $sql ) ;
		foreach ( $arr_barrio as $key=>$val ){
			$sql = "
				UPDATE t_barrio SET b_sgto='1' WHERE id_barrio='$key' 
			" ;
			$g_conexion->execute( $sql ) ;
			if ( $g_conexion->affectedRows() != 1 ){
				MsjE("BD no actualizó: $sql") ;
			}
		}
	}	
	

	//se procede a desplegar un listado de barrios
	//$sql = "SELECT DISTINCT Barrio, sector, zona FROM MetroCuadrado ORDER BY zona, sector, Barrio " ;
	$sql = "SELECT DISTINCT id_barrio, barrio, sector, zona , b_sgto FROM t_barrio ORDER BY b_sgto DESC, zona, sector, barrio " ;
	
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	
	//ver_arr ($arr_salidabd,'$arr_salidabd') ;
	$arr_mostrar = array() ;
	foreach ( $arr_salidabd as $key => $val ){
		$linea['texto'] = $val['zona'] . " >> " . $val['sector'] . " >> " . $val['barrio'] ;
		$linea['indice'] = $val['id_barrio'] ;
		$linea['valor'] = $val['b_sgto'] ;
		$arr_mostrar[] = $linea ;
		//msj ($texto) ;
		//msj ($indice) ;
	}
	
	$g_html = "<form  action='$_SERVER[PHP_SELF]' method='GET'><input type='submit' value='Enviar'>\n" ;
	$g_html .="<table border=\"0\" width=\"700\">" ;
	$g_html .= '<tr><td>Ubicación</td><td>id_Barrio</td><td>Seguir?</td></tr>\n' ;
	foreach ( $arr_mostrar as $key => $val ){					
		//$g_html .="<table border=\"0\" width=\"300\">" ;
		$t1 = $val['texto'] ;
		$t2 = $val['indice'] ;
		$t3 = FormCheck( "arr_barrio[$t2]" , $val['valor']) ;
		$g_html .="<tr><td>$t1</td><td>$t2</td><td>$t3</td></tr>\n" ;
	}
	$g_html .= "</form>"  ;			
	echo $g_html ;

  
?>