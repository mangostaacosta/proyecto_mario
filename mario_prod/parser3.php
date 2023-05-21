<?php
 /****************************************************************************
  20150123  >> seguir_barrios.php
  Proyecto: Obsevatorio Mario
  Saca la información de la BD con el listado de todos los barrios
  y se marcan para seguimiento
  ******************************************************************************/

	require_once 'header.php' ;
  	
	global $g_conexion ;
	
	
	$i_todos = 0 ;
	$i_falla = 0 ;
	$i_prelistos = 0 ;
	$i_update = 0 ;
	
	$fecha = date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"))) ;
	
	ActivarFechaBD( $fecha ) ;	
	
	$sql = "
		SELECT 
		id_inmueble,		
		t_inmueble.fecha,
		idM2_ini,
		url,
		precio,
		t_barrio.id_barrio,
		b_duda_barrio,
		piso,
		ascensor		
		FROM t_barrio
		LEFT JOIN t_inmueble ON t_barrio.id_barrio=t_inmueble.id_barrio
		WHERE b_sgto=1
		AND b_activo=1 AND b_manual=0
		ORDER BY t_inmueble.fecha
	" ;
	
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	ver_arr( $arr_salidabd ) ;
	
	
	foreach ( $arr_salidabd as $key => $val ){
		$i_todos++ ;
		if ( isset( $val['piso'] ) AND isset( $val['ascensor'] )){
		//los datos ya están no es necesario consultar
			$i_prelistos++ ;
			MsjE( "{$val['idM2_ini']} no se procesara ya que tiene datos: P: {$val['piso']} y A:{$val['ascensor']}" ) ;
			continue; 
		}
	
		$link = $val['url'] ;
		$link .= '/otrosDatos?keepThis=true' ;
		$data = file_get_contents( $link ) ;
		
		$nota = $val['idM2_ini'] ;
		echo "\n<br>Procesando $i : $nota" ;
		if ( $data === FALSE ){  //falló la lectura web
			$i_falla++ ;
			MsjE('Falló la página') ;
			$arr_lee = Array() ;
		}else{
			$arr_lee = niv3_parser( $data ) ;  
		}
		$arr_final[$val['id_inmueble']] = $arr_lee;
		$i++ ;
		//sleep(1) ;
		if ( $i > 20000 ){		
			MsjE( "Demasiados datos, más de 20 mil") ;
			break ;
		}
	}
	
	foreach ( $arr_final as $key => $val ){
		$si = FALSE ;
		if ( isset( $val['piso'] )){
			$piso = $val['piso'] ;
			$sql = "
				UPDATE t_inmueble SET
				piso='$piso'
				WHERE id_inmueble='$key'
			" ;
			$g_conexion->execute( $sql ) ;
			if ( $g_conexion->affectedRows() != 1 ){
				MsjE("BD no actualizó: $sql") ;
			}else{
				$si = TRUE ;
			}
		}
		if ( isset( $val['ascensor'] )){
			$ascensor = $val['ascensor'] ;
			$sql = "
				UPDATE t_inmueble SET
				ascensor='$ascensor'
				WHERE id_inmueble='$key'
			" ;
			$g_conexion->execute( $sql ) ;
			if ( $g_conexion->affectedRows() != 1 ){
				MsjE("BD no actualizó: $sql") ;
			}else{
				$si = TRUE ;
			}		
		}
		if ( $si ){
			$i_update++ ;
		}
	}
	
	MsjE( "Cantidad de inmuebles para seguimiento: $i_todos" ) ;
	MsjE( "Cantidad de enlaces web con fallas: $i_falla" ) ;
	MsjE( "Cantidad de inmuebles que ya tenían datos Nivel3: $i_prelistos" ) ;
	MsjE( "Cantidad de inmuebles que fueron actualizados: $i_update" ) ;
	
	
	/*	
	function niv3_parser( $data ){
		
		$arr_regex['piso'] = '@<li><div>Nro Piso:</div>(\d*?)</li>@';
		$regex = '@<li><div>Nro piso:</div>(\d*?)</li>@';
		preg_match($regex,$data,$match);
		$arrsalida['piso'] = $match[1] ;
		
		$arr_regex['ascensor'] = '@<li><div>(Número|N&uacute;mero) Ascensores:</div>(\d*?)</li>@';
		$regex = '@<li><div>(Número|N&uacute;mero) Ascensores:</div>(\d*?)</li>@';		
		preg_match($regex,$data,$match);
		$arrsalida['ascensor'] = $match[2] ;
		
		ver_arr ( $arrsalida ,'$arrsalida en ni3_parser') ;
		
		return( $arrsalida ) ;		
	}
	*/
?>