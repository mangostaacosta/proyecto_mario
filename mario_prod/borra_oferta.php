<?php
 /****************************************************************************
  20150122  >> borra_oferta.php
  Proyecto: Obsevatorio Mario
  Borra los archivos estandarizados de carga a la BD para que al ejecutar el proceso de carga no se metan datos antiguos
  Neesario antes de empezar a cargar archivos nuevos de M2  
  ******************************************************************************/
  
	require_once 'header.php' ;
	include "manual_captura_autom.php" ;
	
	
	$arch_guardar = $g_archivobase ;		
	$handle = fopen($arch_guardar, "w" );
	fwrite( $handle , '' ) ;
	fclose($handle);
	
	$arch_guardar = $g_archivofinal ;		
	$handle = fopen($arch_guardar, "w" );
	fwrite( $handle , '' ) ;
	fclose($handle);
	
	$arch_guardar = $g_archivonivel3 ;		
	$handle = fopen($arch_guardar, "w" );
	fwrite( $handle , '' ) ;
	fclose($handle);
	
	echo "listo !!" ;
    
?>
