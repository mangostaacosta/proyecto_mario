<?php
 /****************************************************************************
  20141221  >> matriz.php
  Proyecto: Obsevatorio Mario
  Saca la información de la BD con los indicadores calculados, esta es sobre la tabla que tieno info por apto
  ******************************************************************************/

	require_once 'header.php' ;
  //require_once "cogefecha.php" ;
	
	global $g_fecha ;
	global $g_conexion ;
	
		
	$sql = "SELECT * FROM MatrizEvaluacion WHERE 1 ORDER BY Puntaje_Total DESC" ;
	$g_conexion->execute( $sql ) ;
  $arr_salidabd = array() ;
  while ($arr_salidabd[] = $g_conexion->fetch()) ;
  array_pop( $arr_salidabd ) ;
	
	$arr_final = $arr_salidabd ;
	
	//ver_arr( $arr_final , 'arr_final' ) ;
	
  $n = Array() ;
  $texto = html_arreglo_bi( $arr_final , 1 , $n , "Matriz Evaluación") ;
  
  include 'header.htm' ;
  echo $texto ;  
  
?>