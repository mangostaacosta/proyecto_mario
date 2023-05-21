<?php
 /****************************************************************************
  20141106  >> test_query.php
  Proyecto: Obsevatorio Mario
  Prueba para ensayar algunos resultados agrupados de la la BD, por ejemplo comprar inicialmente la distribucion zona/sectorr/barrio
  en el segundo caso, arreglar la llave de t_barrio
  ******************************************************************************/
  
	require_once 'header.php' ;
	
	global $g_fecha ;
	global $g_conexion ;
	
		
	if (isset( $_GET['archivo'] )){
		$elarchivo = $_GET['archivo'] ;
	}else{
		echo "Falta el nombre de archivo" ;
		die() ;
	}
	
	$archivo = "$g_ruta/$elarchivo" ; 
	$arch_listado = $archivo ;

	if (file_exists( $arch_listado )){    
		$handle = fopen($arch_listado, "r");
		$datain = fread($handle, filesize($arch_listado));
		fclose($handle);     
		$arr_list = unserialize($datain);
	}else{
		echo "/n<br>OJO: no se encuentra archivo base de listado: $arch_listado" ;
	}
	ver_arr ( $arr_list ) ;
	die() ;
	
	
	
	
	
	
	$sql = "SELECT id_param FROM t_parametros" ;
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	foreach ( $arr_salidabd as $val ){
		$arr_nombre[$val['id_param']] = 'v_' . $val['id_param'] ;		
	}
	foreach ( $arr_nombre as $key => $val ){
		//$sql = "UPDATE t_parametros SET nom_var = '$val' WHERE ID='$key' " ;
		//$g_conexion->execute( $sql ) ;
		msj($val) ;
	}
	
	die();
	
	
	$sql = "SELECT ID FROM t_parametros" ;
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	foreach ( $arr_salidabd as $val ){
		$arr_nombre[$val['ID']] = 'v_' . $val['ID'] ;		
	}
	foreach ( $arr_nombre as $key => $val ){
		$nombre = $val ;
		$sql = "UPDATE t_parametros SET nom_var = '$val' WHERE ID='$key' " ;
		$g_conexion->execute( $sql ) ;
	}
	
	die();


	$sql = "SELECT DISTINCT barrio, sector, zona FROM MetroCuadrado ORDER BY zona, sector, barrio " ;
	//$sql = "SELECT DISTINCT `zona` , `barrio` , `catastro`, COUNT(`barrio`) FROM `t_datosweb`  GROUP BY `zona` , `barrio` , `catastro` ORDER BY  `zona` , `barrio` , `catastro`" ;	
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	//ver_arr ($arr_salidabd,'$arr_salidabd') ;
	$arr_mostrar = $arr_salidabd ;
	
	/*
	foreach ( $arr_salidabd as $key => $val ){
		$linea['texto'] = "(" . $val['zona'] . " >> " . $val['sector'] . ")" ;
		$linea['indice'] = $val['Barrio'] ;
		$arr_mostrar[] = $linea ;
		//msj ($texto) ;
		//msj ($indice) ;
	}
	*/
	
	$n = array() ;
	$g_html = html_arreglo_bi( $arr_mostrar , 1 , $n , "Barrios Dinámicos") ;
	echo $g_html ;  
	
	$sql = "SELECT id_barrio, barrio FROM t_barrio " ;
	
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	ver_arr ($arr_salidabd,'$arr_salidabd') ;
	
	foreach ( $arr_salidabd as $val ) {
		$barrio = $val['barrio'] ;
		$id = $val['id_barrio'] ;
		$barrio = strtolower( $barrio ) ;
		$barrio = trim( $barrio ) ;
		$search = array(' ') ;
		$replace = array ('_') ;
		$barrio = str_replace($search,$replace, $barrio );			
		
		$sql = "UPDATE t_barrio
					SET
						id_barrio = '$barrio'				
					WHERE id_barrio = '$id' ";
		$g_conexion->execute ($sql) ;
	}
	
	/*
	$sql = "INSERT INTO t_datosweb ( fecha, fuente, url, idM2, time_publicado, tipo, zona, barrio, catastro, telefono, direccion, precio, contacto, tipo_contacto, id_inmueble ) VALUES ( '2015-01-12', 'metrocuadrado', 'http://www.metrocuadrado.com/venta/apartamento-en-bogota-rincon-del-chico-rincon-del-chico-con-3-habitaciones-2-ba%c3%b1os-2-garajes-estrato-6-area-98-mts-$480.000.000-id-3422-867837', '3422-867837', 'Publicado hoy', 'apartamento', 'nort', 'Rincón Del Chicó', 'Rincon Del Chico', '3118289258', 'Calle 93B #13 - 14', '480000000', 'nom_asesor', 'asesor', 'BORRAR')" ;
	
	$g_conexion->execute ($sql) ;
	die() ;
	*/
	
?>
