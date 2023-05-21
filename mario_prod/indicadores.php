<?php
 /****************************************************************************
  20141221  >> indicadores.php
  Proyecto: Obsevatorio Mario
  Saca la información de la BD con los indicadores calculados, esta es sobre la tabla que tieno info por apto
  ******************************************************************************/

	require_once 'header.php' ;
	
	$manual['tit'] = ' Indicadores por Inmueble' ;
	$manual['tex'] = 'Esta funcionalidad permite recalcular masivamente el indicador del valor del precio/m2, para todos los inmuebles a partir de la fecha seleccionada. Normalmente se ejecuta al terminar de incorporar los nuevos registros en le Base de Datos durante el proceso de carga de información externa al sistema. 
	<b>Fecha Proceso (obligatorio):</b> Corresponde a la fecha de corte de recálculo del indiccador. Formato de ingreso AAAA-MM-DD. 	
	' ;	
	echo VentanaHelp( $manual ) . "<br>" ;
	echo TituloPagina( 'Operación > Depuración > Indicadores x Inmueble' ) ;
	
	//require_once "cogefecha.php" ;	
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->siempre = FALSE ;
	$forma->Imprimir() ;
	$g_fecha = $forma->Sacar('f_fecha') ;
	
	//global $g_fecha ;
	//global $g_conexion ;
	
	/*
	msj (formato_n(23838483.8374654)) ;	
	msj (formato_n('23838483.8374654')) ;
	msj (formato_n() );
	msj (formato_n('la cabeza del pollo') );
		
	
	$sql = "SELECT * FROM Datos_de_Ubicacion_Web WHERE fecha='$g_fecha'" ;
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	
	$arr_final = $arr_salidabd ;
	
	//ver_arr( $arr_final , 'arr_final' ) ;
	
	$n = Array() ;
	$texto = html_arreglo_bi( $arr_final , 1 , $n , "Indicadores Aptos") ;
  
	include 'header.htm' ;
	echo $texto ;  
	*/
	
	$sql = "
		UPDATE t_inmueble
		SET
		precio_metro = NULL 
		WHERE fecha >= '$g_fecha'
	" ;	
	$g_conexion->execute ($sql) ;
	$filas = $g_conexion->affectedRows() ;	
	MsjE("Inmuebles incializados: $filas") ;
		
	$sql = "
		UPDATE t_inmueble
		SET
		precio_metro = precio / area_privada 
		WHERE area_privada > 0 AND fecha >= '$g_fecha'	
	" ;	
	$g_conexion->execute ($sql) ;
	$filas = $g_conexion->affectedRows() ;	
	MsjE("Inmuebles calculados por area privada: $filas") ;
	
	$sql = "
		UPDATE t_inmueble
		SET
		precio_metro = precio / area_construida 
		WHERE precio_metro IS NULL AND area_construida > 0 AND fecha >= '$g_fecha'	
	" ;	
	$g_conexion->execute ($sql) ;
	$filas = $g_conexion->affectedRows() ;	
	MsjE("Inmuebles calculados por area construida: $filas") ;
	
  
?>