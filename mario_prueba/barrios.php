<?php
 /****************************************************************************
  20141221  >> barrios.php
  Proyecto: Obsevatorio Mario
  Saca la información de la BD con el listado de todos los barrios
	y después de seleccionar el barrio con el query de diferencias para la tabla consolidada de MAX
  ******************************************************************************/

	require_once 'header.php' ;
	require_once 'header.htm' ;
  	
	global $g_fecha ;
	global $g_conexion ;
	$pag = 2 ;
	
	if (isset( $_GET['f_barrio'] )){
    $barrio = $_GET['f_barrio'] ;		
  }else{
		//nada
  }
	
	

	if ( !isset( $barrio )){		//la primera vez que entra a la página debe desplegar un listado de barrios
		$sql = "SELECT DISTINCT Barrio, sector, zona FROM MetroCuadrado ORDER BY zona, sector, Barrio " ;
		$g_conexion->execute( $sql ) ;
		$arr_salidabd = array() ;
		while ($arr_salidabd[] = $g_conexion->fetch()) ;
		array_pop( $arr_salidabd ) ;
		
		ver_arr ($arr_salidabd,'$arr_salidabd') ;
		$arr_mostrar = array() ;
		foreach ( $arr_salidabd as $key => $val ){
			$linea['texto'] = "(" . $val['zona'] . " >> " . $val['sector'] . ")" ;
			$linea['indice'] = $val['Barrio'] ;
			$arr_mostrar[] = $linea ;
			//msj ($texto) ;
			//msj ($indice) ;
		}
		
		$g_html = "<form  action='$_SERVER[PHP_SELF]' method='GET'><INPUT TYPE='hidden' NAME='pag' VALUE='$pag'><input type='submit' value='Enviar'>\n" ;
		$g_html .="<table border=\"0\" width=\"700\">" ;
		foreach ( $arr_mostrar as $key => $val ){					
			//$g_html .="<table border=\"0\" width=\"300\">" ;
			$t1 = $val['indice'] ;
			$t2 = $val['texto'] ;			
			$g_html .="<tr><td><input type=\"radio\" name=\"f_barrio\" value=\"$t1\">$t1</td><td>$t2</td></tr>" ;
		}
		$g_html .= "</form>"  ;			
    echo $g_html ;
		die() ;		
	}else{											//ya se escogio el barrio
		$sql = "
		SELECT 
			Datos_de_Ubicacion_Web.id,
			Datos_de_Ubicacion_Web.Precio_Sobre_Mt2, 
			Datos_de_Ubicacion_Web.estrato AS EstratoAptoVenta, 
			MetroCuadrado.valorizacion, 
			MetroCuadrado.barrio, 
			MetroCuadrado.DeltaMas31_2a8, 
			MetroCuadrado.Delta16a30_2a8, 
			MetroCuadrado.ValorEdad2a8, 
			MetroCuadrado.ValorEdad16a30, 
			Datos_de_Ubicacion_Web.Antiguedad
		FROM `MetroCuadrado`
		INNER JOIN `Datos_de_Ubicacion_Web`
		ON 
			MetroCuadrado.barrio = Datos_de_Ubicacion_Web.catastro 
			AND MetroCuadrado.estrato = Datos_de_Ubicacion_Web.estrato
		WHERE MetroCuadrado.barrio = '$barrio'
			AND Datos_de_Ubicacion_Web.Estado = 1
		ORDER BY `Delta16a30_2a8` DESC
		" ;
		$g_conexion->execute( $sql ) ;
		$arr_salidabd = array() ;
		while ($arr_salidabd[] = $g_conexion->fetch()) ;
		array_pop( $arr_salidabd ) ;
		
		foreach ( $arr_salidabd  as $key => $val ){
			$arr_salidabd[$key]['valorizacion'] = formato_n( $val['valorizacion'] * 100, 1 ) . "%" ;		
		}
		
		//ver_arr( $arr_salidabd , '$arr_salidabd' ) ;
			
		$n = array() ;
		$g_html = html_arreglo_bi( $arr_salidabd , 1 , $n , "Resumen Barrio") ;
  
		
		echo $g_html ;  
  
	}
	
	
  
?>