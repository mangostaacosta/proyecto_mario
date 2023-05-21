<?php
 /****************************************************************************
  20141120  >> pintador.php
  Proyecto: Obsevatorio Mario
  Toma el arreglo de salida de parser2.php y lo saca en una tabla html
  ******************************************************************************/
  
	require_once "funciones.php" ;
  require_once 'header.php' ;
    
  if (isset( $_GET['archivo'] )){
    $elarchivo = $_GET['archivo'] ;
		$archivo = "descargas/$elarchivo" ;
  }else{
		echo "Se utilizara el archivo por defecto: $g_archivofinal" ;
		$archivo = $g_archivofinal ;		
  }
	
	
	
	echo $archivo ;
		
	  
  if (file_exists( $archivo )){    
    $handle = fopen($archivo, "r");
    $datain = fread($handle, filesize($archivo));
    fclose($handle);
    $arr_final = unserialize($datain);
  }else{
		echo "/n<br>OJO: no se encuentra archivo base de salida: $archivo" ;
  }
	
	ver_arr( $arr_final , 'arr_final' ) ;
	
  $n = Array() ;
  $texto = html_arreglo_bi( $arr_final , 1 , $n , "Listado Inmobiliario para DON MAX") ;
  
  include 'header.htm' ;
  echo $texto ;  
  
?>