<?php
/****************************************************************************
  20150114  >> emparejador.php
  Proyecto: Obsevatorio Mario
  Revisa la tabla ta_pseudogemelos y procede a presentarla al usuario con los links respectivos para que éste marque las posibles parejas,
  (hay que mirar bien que pasa con las parejas multiples) para las parejas marcadas xse elimina uno de los papas de t_inm
******************************************************************************/
	
	require_once 'header.php' ;
	
	$manual['tit'] = 'Revisar Gemelos' ;
	$manual['tex'] = 'Esta funcionalidad permite identificar los registros que aunque designan el mismo inmueble subyacente, mantienen un numero plural de enlaces externos. Esta situación se puede presentar cuando la gente republica los aviso en metrocuadrado.com o cuando un inmueble es publicitado por distintas inmobiliarias.
	La vista presenta una tabla con cuatro columnas. 	
	<b>GEMELOS?:</b> La primera columa de la izquierda corresponde a la columna donde el usuario debe marcar las parejas de URLs que se confirman como apuntando al mismo inmueble subyacente.
	<b>Diferentes?:</b> La última columa a la derecha corresponde a la columna donde el usuario debe marcar las parejas de URLs que se confirma apuntando a distintos inmuebles subyacente.
	<b>Inmueble 1:</b> Columna que presenta algunos datos indicativos del primer inmueble a comprarar: barrio, catastro, estrato, precio, código de metrocuadrado.com. Al hacer click al enlace se depliega la página de externa fuente de la información.
	<b>Inmueble 2:</b> Columna que presenta algunos datos indicativos del segundo inmueble a comprarar. Al hacer click al enlace se depliega la página de externa fuente de la información.	
	' ;	
	echo VentanaHelp( $manual ) . "<br>" ;
	echo TituloPagina( 'Operación > Depuración > Revisar Gemelos' ) ;

	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->siempre = FALSE ;
	$forma->Imprimir() ;
	$f_fecha = $forma->Sacar('f_fecha') ;
	
	//Decide si es necesario actualizar la BD dependiendo de si el arreglo f_arrgemelos viene definido por el FORM
	$update = 0 ;
	if (isset( $_GET['f_arrgemelos'] )){
		$f_arrgemelos = $_GET['f_arrgemelos'] ;
		$update = 1 ;
	}else{
		//nada
	}
	if (isset( $_GET['f_arrNOgemelos'] )){
		$f_arrNOgemelos = $_GET['f_arrNOgemelos'] ;
		$update = 1 ;
	}else{
		//nada
	}
	
	$g_html = '' ;
	$i_elimina = 0  ;
	if ( $update == 1 ){
	//aca se borran los registros de cada uno de los campos marcados en el arreglo
		//buscar el listado de psuedogemelos
		$sql = "SELECT inm_1,inm_2 FROM ta_pseudogemelos" ;
		$g_conexion->execute( $sql ) ;
		while ( $arr_salida[] = $g_conexion->fetch() ) ;
		array_pop( $arr_salida ) ;	
		
		//crear una texto para usar en el $sql de busqueda de URL's
		$texto = '' ;		
		foreach ( $f_arrgemelos as $key => $val ){
		//foreach ( $arr_salida as $val ){
			$texto .= $arr_salida[$val]['inm_1'] . ',' . $arr_salida[$val]['inm_2'] . ',' ;		
			//$texto .= $val['inm_1'] . ',' . $val['inm_2'] . ',' ;		
		}
		$texto = substr( $texto,0,-1 ) ;
		$texto = "($texto)" ;
		
		//sacar las URLs de la BD
		$sql = "
			SELECT DISTINCT t_inmueble.id_inmueble AS id, t_datosweb.precio AS precio, id_dato
			FROM t_inmueble
			LEFT JOIN t_datosweb ON t_inmueble.id_inmueble = t_datosweb.id_inmueble
			WHERE t_inmueble.id_inmueble
			IN $texto
		" ;
		$g_conexion->execute( $sql ) ;
		while ( $arr_salida2[] = $g_conexion->fetch() ) ;
		array_pop( $arr_salida2 ) ;	
		foreach ( $arr_salida2 as $val ){		
			$arr_target[$val['id']]['precio'] = $val['precio'] ;
			$arr_target[$val['id']]['id_dato'] = $val['id_dato'] ;		//depronto no se necesita
		}	
	
		foreach ( $f_arrgemelos as $key => $val ){
			// traer la información del id de cada inmueble
			$i1 = $arr_salida[$val]['inm_1'] ;
			$i2 = $arr_salida[$val]['inm_2'] ;
			
			// comprar los precios y elimina el mayor precio, pero primero actualiza a t_dw con el valor de la llave correcta
			if ( $arr_target [$i1]['precio'] > $arr_target [$i2]['precio']  ){
				$sql = "DELETE FROM ta_pseudogemelos WHERE inm_1 = $i1 AND inm_2 = $i2" ;
				$g_conexion->execute( $sql ) ;
				$i1 = $i1 ;
				$i2 = $i2 ;
			}else{
				$sql = "DELETE FROM ta_pseudogemelos WHERE inm_1 = $i1 AND inm_2 = $i2" ;
				$g_conexion->execute( $sql ) ;
				$temp = $i1 ;
				$i1 = $i2 ;
				$i2 = $temp ;
			}
			$sql = "DELETE FROM t_datosweb WHERE id_inmueble = $i1" ;
			$g_conexion->execute( $sql ) ;
			
			$sql = "SELECT idM2_ini,fecha FROM t_inmueble WHERE id_inmueble = $i1" ;
			$g_conexion->execute( $sql ) ;
			$arr_salida3 = $g_conexion->fetch() ;
			$id_temp = $arr_salida3['idM2_ini'] ;
			$fechita = $arr_salida3['fecha'] ;
			
			$sql = "
				INSERT INTO t_si_gemelos (
				fecha_ini,
				idM2_1,
				idM2_2
				) VALUES 
				('$fechita','$i2','$id_temp')
			" ;
			$g_conexion->execute( $sql ) ;
			
			$sql = "DELETE FROM t_inmueble WHERE id_inmueble = $i1" ;
			$g_conexion->execute( $sql ) ;
			$i_elimina ++ ;
		}
		MsjE ("Se han borrado $i_elimina registros de t_inm y de t_dw ya que correspondían a inmuebles gemeliados identificados por el usuario<br>") ;
	
	// Acá se insertan los nuevos pseudogemelos que NO son gemelos
		$texto = '' ;
		foreach ( $f_arrNOgemelos as $key => $val ){
		//foreach ( $arr_salida as $val ){
			$texto .= $arr_salida[$val]['inm_1'] . ',' . $arr_salida[$val]['inm_2'] . ',' ;		
			//$texto .= $val['inm_1'] . ',' . $val['inm_2'] . ',' ;		
		}
		$texto = substr( $texto,0,-1 ) ;
		$texto = "($texto)" ;
		
		//sacar las ID de URLs de la BD
		$sql = "
			SELECT DISTINCT t_inmueble.id_inmueble AS id, idM2_ini, fecha
			FROM t_inmueble			
			WHERE t_inmueble.id_inmueble
			IN $texto
		" ;
		$g_conexion->execute( $sql ) ;
		$arr_salida2 = array() ;
		while ( $arr_salida2[] = $g_conexion->fetch() ) ;
		array_pop( $arr_salida2 ) ;	
		ver_arr ( $arr_salida2 , '$arr_salida2' ) ;
		foreach ( $arr_salida2 as $val ){		
			$arr_tempo[$val['id']] = $val ;
		}
		
		foreach ( $f_arrNOgemelos as $key => $val ){
			$url1 = $arr_tempo[$arr_salida[$val]['inm_1']]['idM2_ini'] ;
			$url2 = $arr_tempo[$arr_salida[$val]['inm_2']]['idM2_ini'] ;
			$fechita = $arr_tempo[$arr_salida[$val]['inm_1']]['fecha'] ;
			
			$sql = "
				INSERT INTO t_no_gemelos (
				fecha_ini,
				idM2_1,
				idM2_2
				) VALUES 
				('$fechita','$url1','$url2'),
				('$fechita','$url2','$url1')				
			" ;
			$g_conexion->execute( $sql ) ;		//inserto en los dos sentidos para que cualquiera sirva de $key
			
			//elimina la parejita de la tabla de pseudogemelos
			$i1 = $arr_salida[$val]['inm_1'] ;
			$i2 = $arr_salida[$val]['inm_2'] ;			
			$sql = "DELETE FROM ta_pseudogemelos WHERE inm_1 = $i1 AND inm_2 = $i2" ;
			$g_conexion->execute( $sql ) ;			
		}	
	}
	
	//buscar el listado de psuedogemelos
	$sql = "SELECT inm_1,inm_2 FROM ta_pseudogemelos" ;
	$g_conexion->execute( $sql ) ;
	$arr_salida = array() ;
	while ( $arr_salida[] = $g_conexion->fetch() ) ;
	array_pop( $arr_salida ) ;	
	
	//crear una texto para usar en el $sql de busqueda de URL's
	$texto = '' ;
	foreach ( $arr_salida as $val ){
		$texto .= $val['inm_1'] . ',' . $val['inm_2'] . ',' ;		
	}
	$texto = substr( $texto,0,-1 ) ;
	$texto = "($texto)" ;
	
	ActivarFechaBD( $f_fecha ) ;
	
	//sacar las URLs de la BD
	$sql = "
		SELECT DISTINCT t_inmueble.id_inmueble AS id, t_inmueble.url, CONCAT_WS('>',barrio,catastro,t_datosweb.estrato,t_datosweb.precio,idM2) AS tx
		FROM t_inmueble
		LEFT JOIN t_datosweb ON t_inmueble.id_inmueble = t_datosweb.id_inmueble
		WHERE b_activo = 1 AND t_inmueble.id_inmueble IN $texto
	" ;
	$g_conexion->execute( $sql ) ;
	$arr_salida2 = array() ;
	while ( $arr_salida2[] = $g_conexion->fetch() ) ;
	array_pop( $arr_salida2 ) ;	
	foreach ( $arr_salida2 as $val ){
		$arr_url[$val['id']] = $val['url'] ;
		$arr_desc[$val['id']] = $val['tx'] ;
		//$arr_target[$val['id']]['precio'] = $val['precio'] ;
		//$arr_target[$val['id']]['id_dat'] = $val['id_dato'] ;	
	}	
	
	//armar el $arr para mostrar en HTML	
	foreach ( $arr_salida as $key => $val ){
		$i1 = $val['inm_1'] ;
		$i2 = $val['inm_2'] ;
		$lin['i1'] = $i1 ;
		$lin['i2'] = $i2 ;
		$lin['id'] = $key ;
		$b_mostrar = TRUE ;
		if (isset($arr_url[$i1])){
			$lin['u1'] = $arr_url[$i1] ;
			$lin['tx1'] = $arr_desc[$i1] ;			
		}else{
			$b_mostrar = FALSE ;
		}
		if (isset($arr_url[$i2])){
			$lin['u2'] = $arr_url[$i2] ;
			$lin['tx2'] = $arr_desc[$i2] ;
		}else{
			$b_mostrar = FALSE ;
		}
		
		if ( $b_mostrar ){
			$arr_mostrar[] = $lin ;
		}		
	}
	//ver_arr( $arr_mostrar , '$arr_mostrar' ) ; 	
	
	//pintar el FORM
	$g_html .= "<form  action='$_SERVER[PHP_SELF]' method='GET'><INPUT TYPE='hidden' NAME='pag' VALUE='1'><INPUT TYPE='hidden' NAME='f_fecha' VALUE='$f_fecha'><input type='submit' value='Enviar'>\n" ;
	$g_html .="<table border=\"0\" width=\"1000\">" ;
	$g_html .="<tr><td>GEMELOS?</td><td>Inmueble 1</td><td>Inmueble 2</td><td>Diferentes?</td></tr>\n" ;
	foreach ( $arr_mostrar as $key => $val ){
		$url = $val['u1'] ;
		$descr = $val['tx1'] ;
		$orden = $val['id'] ;
		$tex1 = "<a href=\"$url\" target=\"_blank\">$descr</a>" ;
		$url = $val['u2'] ;
		$descr = $val['tx2'] ;
		$tex2 = "<a href=\"$url\" target=\"_blank\">$descr</a>" ;		
		$g_html .="<tr><td><input type=\"checkbox\" name=\"f_arrgemelos[]\" value=\"$orden\"></td><td>$tex1</td><td>$tex2</td><td><input type=\"checkbox\" name=\"f_arrNOgemelos[]\" value=\"$orden\"></td></tr>\n" ;
	}
	$g_html .= "</form>"  ;
	
	echo $g_html ;	
?>