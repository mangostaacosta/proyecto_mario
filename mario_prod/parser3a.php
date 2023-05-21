<?php
 /****************************************************************************
  20150123  >> parser3a.php
  Proyecto: Obsevatorio Mario
  Busca la información de Nivel 3 para los barrios que están en seguimiento. La fecha de corte es la fecha del día respectivo
  ******************************************************************************/

	require_once 'header.php' ;
	include "manual_captura_autom.php" ;
	
	ini_set('max_execution_time', 600 ) ;
	
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Este proceso se puede tardar unos minutos', 'OK para iniciar') ;	
	$forma->siempre = FALSE ;
	$forma->Imprimir() ;
	
	global $g_conexion ;
	
	if (isset( $_GET['indice'] )){
		$indice = $_GET['indice'] ;
	}else{
		$indice = 0 ;
		MsjE( "Indice en 0s" ) ;
	}
	
	//incialización de contadores
	$i_todos = 0 ;		//contador de los registros que se están consultando en la ejecución
	$i_falla = 0 ;		//contador de páginas que no están activas
	$i_prelistos = 0 ;	//contador de registros que ya tiene la información incluida, por lo que no se hace consulta externa
	$i_update = 0 ;
	
	$fecha = date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d"), date("Y"))) ;
	ActivarFechaBD( $fecha ) ;	
	
	//query para buscar los inmuebles de los barrios que se encuentran marcados para seguimiento
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
	
	//buscar y abrir el archivo donde se guardan los resultados de la consulta externa	
	if (file_exists( $g_archivonivel3 )){
		$handle = fopen($g_archivonivel3, "r" );
		$datain = fread($handle, filesize($g_archivonivel3));
		fclose($handle);    
		$arr_final = unserialize($datain);
	}else{
		$arr_final = array() ;
	}
	
	$i = 0 ;	//contador de definir putno de arranque de consultas
	$j = 0 ;	//contador de copia de seguridad
	foreach ( $arr_salidabd as $key => $val ){
		//revisar parámetro $indice para continuar el proceso donde se cortó en caso de que esto haya sucedido
		if ( $i < $indice ){
			//todavía no se llega al indice saltarselos
			$i++ ;
			continue ;
		}
		$i_todos++ ;
		$i++ ;
		if ( isset( $val['piso'] ) AND isset( $val['ascensor'] )){
		//los datos ya están, no es necesario consultar
			$i_prelistos++ ;
			MsjE( "{$val['idM2_ini']} no se procesara ya que tiene datos: P:{$val['piso']} y A:{$val['ascensor']}" ) ;
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
		
		//sleep(1) ;
		if ( $i > 20000 ){		
			MsjE( "Demasiados datos, más de 20 mil") ;
			break ;
		}
		
		//ir guardando copias de seguridad a medida que se consultan los datos externos
		if ( $j == 60 ){
			$prueba = serialize( $arr_final ) ;  
			$handle = fopen($g_archivonivel3, "w" );
			fwrite( $handle , $prueba ) ;
			fclose($handle) ;
			$j = 0 ;
		}	
		$j++ ;		
	}
	
	foreach ( $arr_final as $key => $val ){
		$si = FALSE ;
		if ( isset( $val['piso'] )){ //actualizar los registros que tienen dato de piso
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
		if ( isset( $val['ascensor'] )){	//actualizar los registros que tienen dato de ascensor
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