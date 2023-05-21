<?php
 /****************************************************************************
  20141120  >> parser0c.php
  Proyecto: Obsevatorio Mario
  Intenta replicar el código RUBY que le compré a sinnaptic: 
  En este caso tratando de aprovechar un código URL de M2 despues de los cambios de 201706, incialmente nesta en prueba no fucniona todas las opciones del menu
  ******************************************************************************/
  
	require_once 'header.php' ;
	include "manual_captura_autom.php" ;
	include "lee_M2.php" ;
	echo TituloPagina( 'Operación > Captura Automática > Descarga MC' ) ;
	
	$tipo = 'apartamento' ;
	$ciudad = 'bogota' ;
	//$zona="chapinero";
	//$tiempo = 'Más de 20 años' ;
	//$tiempo = 'Entre 10 y 20 años' ;
	//$tiempo = 'Entre 5 y 10 años' ;
	//$tiempo = 'Entre 0 y 5 años' ;
	//$tiempo = 'Para Estrenar' ;
	
	$pd=0;	
	$ph=1000000;
	$ca=0 ;
	$out = 0 ;
	
	$estado = 'Usado' ;	
	$max="16";
	
	
	//if(isset($_POST['Ciudad'])) 
	//	$ciudad=$_POST["Ciudad"];
		
	//if(isset($_POST['TipoInmueble'])) 
	//	$tipo = $_POST['TipoInmueble'];
	
	//if(isset($_POST['ca'])) 
	//	$ca = $_POST['ca'];
	
	/*
	$ca = 'Compra' ;
	
	if(isset($_POST['pd'])) 
		$pd = $_POST['pd'] * 1000000 ;
		
	if(isset($_POST['ph'])) 
		$ph = $_POST['ph'] * 1000000 ;				

	//if(isset($_POST['out'])) 
	//	$out = $_POST['out'];	
	$out = 'JSON' ;
	
	if(isset($_POST['estado'])) 
		$estado = $_POST['estado'];	

	
	//20180226: para usar abajo en el for
		switch ($tiempo){
			case 'Sobre Plano' :
			case 'En Construcción' :
			case 'Para Estrenar' :
				$ti = 'new' ;
				break ;
			case 'Entre 0 y 5 años' :
			case 'Entre 5 y 10 años' :
				$ti = '0a10' ;
				break ;
			case 'Entre 10 y 20 años' :
				$ti = '10a20' ;
				break ;
			case 'Más de 20 años' :
				$ti = '20a' ;
				break ;
			default:
				$ti = 'nn' ;
				break ;
	
	
	*/
	
	//20180226: implementación de consulta masiva de todos los tiempos estándar
	if(isset($_POST['tiempo'])) 		
		$tiempo = $_POST['tiempo'];
	
	if(isset($_POST['zona'])) 
		$zona = $_POST['zona'];	
	
	if(isset($_POST['max'])) 
		$max = $_POST['max']; //20170625: está por implementar bien aun no sirve porque no cambia el código
	
	if ( $tiempo != 'ESPECIAL'){
		f_parser0( $ciudad, $tipo, $ca, $pd, $ph, $out, $zona, $max, $estado, $tiempo ) ;
	}else{
		//$arr_temp = array('Para Estrenar','Entre 10 y 20 años','Más de 20 años'); //20180515: se cambio preventivamente para no bajar nuevos ya que se detecto durante arreglo de problema de GET que muchos inmubles nuevos realmente estaban con edad y debe ser erro de M2
		$arr_temp = array('Entre 10 y 20 años','Más de 20 años');
		foreach ( $arr_temp as $key=>$val ){
			$tiempo = $val;
			f_parser0( $ciudad, $tipo, $ca, $pd, $ph, $out, $zona, $max, $estado, $tiempo ) ;
		}
		
		$arr_temp = array('Entre 0 y 5 años','Entre 5 y 10 años');
		$max = $max/2 ;
		$max = (integer)$max ;
		foreach ( $arr_temp as $key=>$val ){
			$tiempo = $val;
			f_parser0( $ciudad, $tipo, $ca, $pd, $ph, $out, $zona, $max, $estado, $tiempo ) ;
		}		
	}
	

	
	/*
	//$comando = "ruby m2_tests.rb $ciudad $tipo $ca $pd $ph $out $zona $max $estado \"$tiempo\"" ;
	//msj( $comando ) ;	
	//$output = shell_exec( $comando ) ;
		
	$output = leer_m2( $ciudad, $tipo, $ca, $pd, $ph, $out, $zona, $max, $estado, $tiempo ) ;
	
	$dt = date('Ymd') ;
	$zo = strtolower( substr($zona,0,4) ) ;
	
	switch ($tiempo){
		case 'Sobre Plano' :
		case 'En Construcción' :
		case 'Para Estrenar' :
			$ti = 'new' ;
			break ;
		case 'Entre 0 y 5 años' :
		case 'Entre 5 y 10 años' :
			$ti = '0a10' ;
			break ;
		case 'Entre 10 y 20 años' :
			$ti = '10a20' ;
			break ;
		case 'Más de 20 años' :
			$ti = '20a' ;
			break ;
		default:
			$ti = 'nn' ;
			break ;
	}	
	
	$archivo = $g_ruta . $dt . '_' . $zo . '_' . "$ti.txt" ;
	msj( $archivo ) ;
	
	$bytes_ini = filesize($archivo) ;

	if (file_exists( $archivo )){
		$handle = fopen($archivo, "r" );
		$datain = fread($handle, filesize($archivo));
		fclose($handle);		
	}else{
		$datain = '' ;
	}
	
	$datain .= $output ;
	
	$handle = fopen($archivo, "w" );
	fwrite( $handle , $datain ) ;
	fclose($handle) ;
	clearstatcache();	
	$bytes_fin = filesize($archivo) ;
	
	MsjE( "Ha sido creado el archivo: $archivo. Tamaño inicial: $bytes_ini , tamaño final $bytes_fin" ) ;
	*/
	
	function f_parser0( $ciudad, $tipo, $ca, $pd, $ph, $out, $zona, $max, $estado, $tiempo ){
		
		global $g_ruta ;
		
		$output = leer_m2( $ciudad, $tipo, $ca, $pd, $ph, $out, $zona, $max, $estado, $tiempo ) ;
		
		$dt = date('Ymd') ;
		$zo = strtolower( substr($zona,0,4) ) ;
		
		switch ($tiempo){
			case 'Sobre Plano' :
			case 'En Construcción' :
			case 'Para Estrenar' :
				$ti = 'new' ;
				break ;
			case 'Entre 0 y 5 años' :
			case 'Entre 5 y 10 años' :
				$ti = '0a10' ;
				break ;
			case 'Entre 10 y 20 años' :
				$ti = '10a20' ;
				break ;
			case 'Más de 20 años' :
				$ti = '20a' ;
				break ;
			default:
				$ti = 'nn' ;
				break ;
		}	
		
		$archivo = $g_ruta . $dt . '_' . $zo . '_' . "$ti.txt" ;
		msj( $archivo ) ;
		
		$bytes_ini = filesize($archivo) ;

		if (file_exists( $archivo )){
			$handle = fopen($archivo, "r" );
			$datain = fread($handle, filesize($archivo));
			fclose($handle);		
		}else{
			$datain = '' ;
		}
		
		$datain .= $output ;
		
		$handle = fopen($archivo, "w" );
		fwrite( $handle , $datain ) ;
		fclose($handle) ;
		clearstatcache();	
		$bytes_fin = filesize($archivo) ;
		
		MsjE( "Ha sido creado el archivo: $archivo. Tamaño inicial: $bytes_ini , tamaño final $bytes_fin" ) ;
		
		return 1;		
	}
	
	
?>
