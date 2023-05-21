<?php
/****************************************************************************
  20150106  >> depurador.php
  Proyecto: Obsevatorio Mario
  Realiza actividades de activar/inactivar en la BD para limpiar los cálculos
  Actualiza b_normal de inmuebles
  Revisa gemelos
  Marca los barrios homónimos
  Actualiza la fecha_fin de t_inmueble  
******************************************************************************/
	
	require_once 'header.php' ; 
	
	$manual['tit'] = 'Limpieza Datos' ;
	$manual['tex'] = 'Esta funcionalidad permite identificar los inmuebles con precios ó áreas por fuera de lo normal. para los precios se considera que un valor inferior a $40 millones es fuera de los normal, ya para área se considera que un área inferior a 10 mt2 es fuera de lo normal.
	<b>Fecha Proceso (obligatorio):</b> Corresponde a la fecha de corte de validación de información. Formato de ingreso AAAA-MM-DD. 	
	' ;	
	echo VentanaHelp( $manual ) . "<br>" ;
	echo TituloPagina( 'Operación > Depuración > Limpieza Datos' ) ;
	
	$pmin = 40000000 ;		//precio mínimo normal
	$amin = 10 ;			//área mínima normal
	
	
	//Se crea y utiliza el formulario para capurar las variables de entrada a la página
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->siempre = FALSE ;
	$forma->Imprimir() ;
	$mifecha = $forma->Sacar('f_fecha') ;

	
	global $g_MiRepeBarrios ;
	CreaBarriosRepe() ;	
	
	//actualizar la bandera de inmuebles con precios demasiado bajos	
	$sql = "
		UPDATE t_inmueble
		SET
		b_normal = 0 
		WHERE precio < $pmin
	" ;
	$g_conexion->execute ($sql) ;
	$filas = $g_conexion->affectedRows() ;	
	$pmint = formato_n( $pmin,0 ) ;
	MsjE("Inmuebles convertidos a no normales por Precio menor a $pmint: $filas") ;
	
	
	//Corregir datos de AREA o actualizar la bandera
	//mirar si hay datos de area construida
	$sql = "
		SELECT id_inmueble, area_construida 
		FROM t_inmueble
		WHERE area_privada IS NULL OR area_privada = 0
	" ;
	$g_conexion->execute ($sql) ;
	while( $arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	foreach ( $arr_salidabd as $val ){
		$area = $val['area_construida'] ;
		if ( $area > 0 ){
			$sql = "
				UPDATE t_inmueble SET
				area_privada = '$area'
				WHERE id_inmueble = '{$val['id_inmueble']}'
			" ;
			$g_conexion->execute ($sql) ;
		}
	}
	$sql = "
		UPDATE t_inmueble
		SET
		b_normal = 0 
		WHERE area_privada < $amin
	" ;
	$g_conexion->execute ($sql) ;
	$filas = $g_conexion->affectedRows() ;		
	MsjE("Inmuebles convertidos a no normales por Area menor a $amin: $filas") ;
	
	
	
	//actualiar los precio_metro
	//$mifecha = date('Y-m-d') ;
	//require_once "indicadores.php?pag=1&g_fecha=$mifecha" ; 

	
	//identificar si hay algún gemelo "autoevidente" y alertar
	$sql = "
		SELECT * FROM (SELECT id_inmueble, idM2_ini, COUNT(idM2_ini) as cuenta FROM t_inmueble GROUP BY idM2_ini) AS t_aux WHERE cuenta > 1	
	" ;
	$g_conexion->execute ($sql) ;
	while( $arr_salida[] = $g_conexion->fetch()) ;
	array_pop( $arr_salida ) ;
	ver_arr ( $arr_salida , 'arreglo gemelos que no debería estar' ) ;
	if ( sizeof( $arr_salida ) > 0 ){
	//hay problemas
		MsjE( 'Problemas con Gemelos en t_inmueble:<br>' ) ;
		$n = Array() ;
		$texto = html_arreglo_bi( $arr_salida , 1 , $n , "Gemelos NO Admitidos") ;  
		echo $texto ;
	}
	
	
	//marcar los inmuebles que tienen barrio del conjunto de dudosos porque barrios tienen homonimo	
	ActivarFechaBD( $mifecha ) ;
	
	$sql = "
		SELECT id_inmueble, id_barrio FROM t_inmueble WHERE b_activo = 1 	
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salida = array() ;
	while( $arr_salida[] = $g_conexion->fetch()) ;
	array_pop( $arr_salida ) ;
	foreach ( $arr_salida as $val ){
		if ( BuscaBarrioRepe( $val['id_barrio'] ) == 1 ){
		//Hay que actualizar la bandera
			$id = $val['id_inmueble'] ;		
			$sql = "
				UPDATE t_inmueble
				SET
				b_duda_barrio = 1				
				WHERE id_inmueble = '$id' 
			";
			$g_conexion->execute ($sql) ;		
		}
	}
	
	//ver_arr( $g_MiRepeBarrios ) ;
	
	
	//actualizar las fechas finales de los registros que aún no tienen fecha fecha final asignada
	$sql = "
		SELECT id_inmueble,fecha FROM t_inmueble
		WHERE fecha_fin IS NULL		
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salida = array() ;
	while( $arr_salida[] = $g_conexion->fetch()) ;
	array_pop( $arr_salida ) ;
	foreach ( $arr_salida as $val ){
		$fecha = $val['fecha'] ;
		$lafecha = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $fecha , 5 , 2 ) , substr( $fecha , 8 , 2 ) + 15 , substr( $fecha , 0 , 4 ))) ;
		$sql = "UPDATE t_inmueble SET fecha_fin = '$lafecha' WHERE id_inmueble={$val['id_inmueble']}" ;
		$g_conexion->execute ($sql) ;	
	}
	
	//también se revisa la fecha final, si está a menos de 3 días se actualiza en 7 días mas
	$sql = "
		SELECT id_inmueble,idM2_ini,fecha,fecha_fin FROM t_inmueble
		WHERE fecha='$mifecha'
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salida = array() ;
	while( $arr_salida[] = $g_conexion->fetch()) ;
	array_pop( $arr_salida ) ;
	
	$lapso = 60 * 60 * 24 * 3 ;	//tres días
	foreach ( $arr_salida as $linea ){
		$lafecha_fin = $linea['fecha_fin'] ;
		$el_fin = mktime( 0 ,0 , 0 , substr( $lafecha_fin , 5 , 2 ) , substr( $lafecha_fin , 8 , 2 ), substr( $lafecha_fin , 0 , 4 )) ;
		$lafecha_hoy = $mifecha ;
		$el_hoy = mktime( 0 ,0 , 0 , substr( $lafecha_hoy , 5 , 2 ) , substr( $lafecha_hoy , 8 , 2 ), substr( $lafecha_hoy , 0 , 4 )) ;
		
		//verificar si faltan menos de tres días para la fecha final
		if ( ( $el_fin - $el_hoy ) < $lapso ){
			//se corre la fecha final
			$lafecha = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $lafecha_hoy , 5 , 2 ) , substr( $lafecha_hoy , 8 , 2 ) + 7 , substr( $lafecha_hoy , 0 , 4 ))) ;
			$id_inmueble = $linea['id_inmueble'] ;
			$sql = "
				UPDATE t_inmueble SET
				fecha_fin='$lafecha' 
				WHERE id_inmueble = '$id_inmueble'
			" ;
			$g_conexion->execute ($sql) ;
			MsjE("Se actualizó fecha_fin en {$linea['idM2_ini']} de $lafecha_fin a $lafecha") ;
		}else{
			//se mantiene la fecha final
			MsjE("Se mantiene fecha_fin en {$linea['idM2_ini']} de $lafecha_fin") ;
		}
	}
	
	
	/*Este componente se migro a finalizador.php
	//Revisar si están activos los links de los t_inmueble que están en la fecha_fin y actualizar fecha_fin
	$sql = "
		SELECT id_inmueble,url,fecha_ini,idM2_ini from t_inmueble
		WHERE fecha_fin ='$mifecha'
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salida = array() ;	
	while( $arr_salida[] = $g_conexion->fetch()) ;
	array_pop( $arr_salida ) ;
	$arr_lee = array() ;
	$i = 0 ;
	$fecha = date('Y-m-d') ;
	$lafecha = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $fecha , 5 , 2 ) , substr( $fecha , 8 , 2 ) + 14 , substr( $fecha , 0 , 4 ))) ;
	foreach ( $arr_salida as $key=>$val ){	
		$archivo = $val['url'] ;
		$data = file_get_contents( $archivo ) ;
		$i++ ;
		echo "\n<br>Procesando $i fecha inicial {$val['fecha_ini']}: $archivo" ;
		if ( $data === FALSE ){  //falló la lectura web
			$arr_lee = Array() ;
		}else{
			$arr_lee = metro_parser( $data ) ;  
		}
		ver_arr( $arr_lee , 'arr_lee') ;
		
		if ( isset($arr_lee['id'])){ //comprobar que la página está viva
			$arr_final[] = array_merge( $val, $arr_lee) ;
			$sql = "UPDATE t_inmueble SET fecha_fin = '$lafecha' WHERE id_inmueble={$val['id_inmueble']}" ;
			$g_conexion->execute ($sql) ;
		}else{
			MsjE("La página no está activa") ;
		}		
		//sleep(1) ;
		if ( $key > 20000 ){
			MsjE( "Demasiados datos, más de 20 mil") ;
			break ;
		}
	}
	*/
	
?>