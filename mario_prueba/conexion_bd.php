<?php
	/***********
	* 20141012  >> conexion_bd.php  
	* establece la conexion con la base de datos (en este caso usando objeto de MYSQL)  y la asigna a la variable global 
	* $g_conexion
	***********/
	
	require_once "header.php" ;
	require_once "clmysqldb.php" ;
	global $g_depura ;
		
	$g_conexion = new MySQLConnection ( $bd_nombre , $bd_usuario , $bd_clave ) ;
	$g_conexion->connect();	
	if ( $g_depura > 0){
		$g_conexion->muestre( $g_depura ) ;
	}
	if ( isset ( $g_bd )){
		if (  $g_bd == 0 ){
			$g_conexion->ejecutando = 0 ;
		}		
	}	
	
?>
