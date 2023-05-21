<?php
/****************************************************************************
  20150106  >> crea_t_barrio.php
  Proyecto: Obsevatorio Mario
  Realiza actividades para automatizar la subida de t_barrio desde MC
  Para ejecutar el código se debe crear un archivo plano que sólo inluya las tres columnas del archivo estandarizado de metrocuadrado correspondientes a Barrio, Sector, Zona
  Este archivo debe tener formato csv
******************************************************************************/
	
	require_once 'header.php' ; 
	
	$manual['tit'] = 'Generar idBarrios' ;
	$manual['tex'] = 'Esta opción automatiza el proceso de creación de idBarrios que sean únicos en la Base de Datos del Sistema. Debido a que los barrios e idBarrios han sido parametrizados con anticipación, esta opción sólo se debe ejecutar en caso de que se detecten cambios en la información catastral o se deseen incluir barrios adicionales en el sistema.
	
	La página presenta un formulario para ingresar el código de usuario (esta funcionalidad sólo puede ser ejecutada por un usuario autorizado).		
	<b>Usuario Autorizado (obligatorio)</b>: se debe ingresar el código del usuario autorizado
	
	El proceso que se ejecuta con esta opción es el siguiente:
	0. Via FTP se debe cargar el archivo plano barrios_mc.csv (mejor sin tildes) al directorio de mario_data. El archivo debe tener 3 columnas separadas por comas.
	1. El sistema carga un archivo plano que debe contener la categorización de ZONA, SECTOR, BARRIO. En el archivo plano el campo de SECTOR debe ser ingresado de tal forma que identifique las ubicaciones speciales a las que se hace seguimiento (ej SECTOR Naranja). Antiguamente el sector debía corresponder con la información de las tablas estandarizadas de metrocuadrado.com pero ya no es así.
	2. Con base en la información cargada, el sistema identifica nombres de barrios que estén duplicados en zonas o sectores diferentes.
	3. El sistema asigna una codificación única a los nombres de barrios (minúsculas separados por "_") y en caso de las repeticiones, agrega asteríscos a los nombres repetidos para distinguir los nombres.	
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Administración > Generar idBarrios' ) ;
	
	//Se crea y utiliza el formulario para capturar las variables de entrada a la página
	$forma = new Formulario() ;
	//$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->Insertar('f_usuario','Usuario Autorizado:') ;
	$forma->siempre = FALSE ;
	$forma->Imprimir() ;
	//$f_fecha = $forma->Sacar('f_fecha') ;
	$usuario = $forma->Sacar('f_usuario') ;
	
	
	$archivo = "../mario_data/barrios_mc.csv" ;
	if (file_exists( $archivo )){    
		echo "SI se encuentra archivo: $archivo " ;	
	}else{
		echo "NO se encuentra archivo: $archivo " ;
		die() ;
	}
	
	$sql = "
		TRUNCATE TABLE taux_barrio
	" ;
	$g_conexion->execute ($sql) ;
	
	$sql = "
		LOAD DATA LOCAL INFILE '$archivo' INTO TABLE `taux_barrio` 
		FIELDS TERMINATED BY ',' LINES TERMINATED BY '\r\n' IGNORE 1 LINES 
	" ;
	$g_conexion->execute ($sql) ;
	
	
	
	
	//20150330: se sutituye esta consulta por una consulta directa sobre t_mc, para elminar el paso intermedio de la creación del archivo plano
	//$sql = "SELECT DISTINCT barrio, sector, zona FROM t_mc" ;
	
	//20161210: cambio del query para que esl script funcion sin usar t_mc
	$sql = "
		SELECT DISTINCT barrio, sector, zona FROM taux_barrio
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salida = array() ;
	while( $arr_salida[] = $g_conexion->fetch()) ;
	array_pop( $arr_salida ) ;
	//ver_arr ( $arr_salida , 'arreglo los barrios' ) ;
		
	//borrar los datos de taux porque hay muchos repetidos
	$sql = "
		TRUNCATE TABLE taux_barrio
	" ;
	$g_conexion->execute ($sql) ;
	
	//volver a llenar dejando solo filas únicas
	foreach ( $arr_salida as $val ){
		$a = $val['barrio'] ;
		$b = $val['sector'] ;
		$c = $val['zona'] ;
		$sql = "
			INSERT INTO taux_barrio (
			barrio,
			sector,
			zona
			) VALUES (
			'$a',
			'$b',
			'$c'
			)
		" ;
		$g_conexion->execute ($sql) ;		
	}
	
	//mostrar los barrios repetidos
	
	$sql = "
		SELECT * FROM (SELECT barrio, COUNT(barrio) as cuenta FROM taux_barrio GROUP BY barrio) AS t_aux WHERE cuenta > 1
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salida = array() ;
	while( $arr_salida[] = $g_conexion->fetch()) ;
	array_pop( $arr_salida ) ;
	ver_arr ( $arr_salida , 'los repetidos' ) ;
	foreach ( $arr_salida as $key=> $val ){
		$arr_repe[$val['barrio']] = $val['barrio'] ; 
	}
		
	if ( $usuario != 'macmax' ){	//validación antes de update en bd
		MsjE('No se actualiza t_barrio, usuario no identificado') ;
		die() ;
	}
	
	//ahora is viene un query con identidad semi unicas, cuando detecte un barrio repetido, se le pone un cosito
	
	//primero borrar t_barrio
	$sql = "
		TRUNCATE TABLE t_barrio
	" ;
	$g_conexion->execute ($sql) ;
	
	
	$sql = "
		SELECT barrio, sector, zona FROM taux_barrio
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salida = array() ;
	while( $arr_salida[] = $g_conexion->fetch()) ;
	array_pop( $arr_salida ) ;
	//ver_arr ( $arr_salida , 'arreglo los barrios' ) ;
	
	
	foreach ( $arr_salida as $key => $val ){
		$barrio = $val['barrio'] ;
		$sector = $val['sector'] ;
		$zona = $val['zona'] ;
		$id_barrio = $barrio ;
		
		if ( isset( $arr_repe[$barrio] )){
			msj( "Dentro de reptidos con $id_barrio" ) ;
			$id_barrio = $arr_repe[$barrio] ;
			$arr_repe[$barrio] .= '*' ;  //se le agrega cosito al sgte nombre
		}
	
		//20161210: la fecha solia ser '2015-01-11'
		$sql = "
			INSERT INTO t_barrio (
			id_barrio,
			fecha,
			ciudad,
			barrio,
			sector,
			zona
			) VALUES (
			'$id_barrio',
			'2016-12-10',
			'Bogota',
			'$barrio',
			'$sector',
			'$zona'			
			)			
		" ;
		$g_conexion->execute ($sql) ;		
	}
	
	$sql = "SELECT id_barrio, barrio FROM t_barrio " ;	
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	ver_arr ($arr_salidabd,'$arr_salidabd') ;
	
	foreach ( $arr_salidabd as $val ) {
		$barrio = $val['id_barrio'] ;
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
	
	//crear el campo id_barrio en la tabla estándar de metrocuadrado
	//20150330: Se corrige la actualización para que los asteriscos se incluyan en los barrios repetidos
	
	//query para sacar los id_barrios con identificador único
	$sql = "SELECT id_barrio, barrio, sector, zona FROM t_barrio" ;
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	
	//ciclo para crear un arreglo indexado acorde con los datos de sector, zona, barrio
	$arr_unicos = array() ;
	foreach ( $arr_salidabd as $val ) {
		$llave = "{$val['zona']}_{$val['sector']}_{$val['barrio']}" ;
		if ( isset( $arr_unicos[$llave] )){
			MsjE("El identificador $llave está repetido. Se cancela el proceso") ;
			die() ;
		}
		$arr_unicos[$llave] = $val['id_barrio'] ;
	}
	
	$sql = "SELECT id_llave, Barrio, sector, zona FROM t_mc " ;
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	ver_arr ($arr_salidabd,'$arr_salidabd') ;
		
	foreach ( $arr_salidabd as $val ) {
		$aguja = "{$val['zona']}_{$val['sector']}_{$val['Barrio']}" ;
		$id_barrio = $arr_unicos[$aguja] ;
		$id = $val['id_llave'] ;
		
		/*
		$barrio = $val['barrio'] ;
		$id = $val['id_llave'] ;
		$barrio = strtolower( $barrio ) ;
		$barrio = trim( $barrio ) ;
		$search = array(' ') ;
		$replace = array ('_') ;
		$barrio = str_replace($search,$replace, $barrio );
		*/
		
		//query de actualización de id_barrio en tabla estandarizada. Antiguamente la variable $id_barrio se llamaba $barrio
		$sql = "UPDATE t_mc
				SET
					id_barrio = '$id_barrio'
				WHERE id_llave = '$id' ";
		
		$g_conexion->execute ($sql) ;
	}	
?>