<?php
/****************************************************************************
  20150219  >> finalizador_chimbo.php
  Proyecto: Obsevatorio Mario
  
  Actualiza la fecha_fin de t_inmueble
  Esta versión del finalizador, no revisa si metrocuadrado está vigente, sencillamente saca un listado para que se apruebe por el usuario sin hacer chequeos adicionales
  
******************************************************************************/
	
	require_once 'header.php' ; 
	ini_set('max_execution_time', 600 ) ;
	
	$manual['tit'] = 'Prolongar Inmuebles' ;
	$manual['tex'] = 'Esta funcionalidad permite revisar los inmuebles que pierden vigencia en la fecha definida. En primer lugar hace un barrido de las URLs de los registros que pierde vigencia para identificar aquellos que continúan en funcionamiento. Posteriormente se presenta una tabla que muestra el estado de los URLs y donde el usuario puede seleccionar si decide finalizarlos o prolongarlos.
	<b>Fecha Proceso (obligatorio):</b> Corresponde a la fecha de corte. Formato de ingreso AAAA-MM-DD.
	' ;	
	echo VentanaHelp( $manual ) . "<br>" ;
	echo TituloPagina( 'Operación > Depuración > Prolongar Chimbamente' ) ;

	
	//Esta sección es para la ultima iteración del formulario que hace el UPDATE en la tabla de los inmuebles elegidos por el usuario
	if ( isset ( $_POST['pag'] )){
		if ( $_POST['pag'] == 2 ){
			$fecha = $_POST['f_fecha2'] ;
			$lafecha = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $fecha , 5 , 2 ) , substr( $fecha , 8 , 2 ) + 14 , substr( $fecha , 0 , 4 ))) ;
			$arr_cambiar = $_POST['arr_muere'] ;
			ver_arr( $arr_cambiar ) ;
			foreach ( $arr_cambiar as $key => $val ){
				$sql = "UPDATE t_inmueble SET fecha_fin = '$lafecha' WHERE id_inmueble=$key" ;
				$g_conexion->execute ($sql) ;
			}				
		}
		$i = count( $arr_cambiar ) ;
		MsjE("Proceso Finalizado, se prolongaron $i inmuebles desde la fecha: $fecha hasta la fecha: $lafecha") ;
		die() ;
	}
	
	//Se crea y utiliza el formulario para capturar las variables de entrada a la página
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->siempre = FALSE ;
	$forma->Imprimir() ;
	$mifecha = $forma->Sacar('f_fecha') ;

	//Revisar si están activos los links de los t_inmueble que están en la fecha_fin y crear un arreglo de estados para que el usuario pueda escoger los que desea prolongar
	$sql = "
		SELECT id_inmueble,url,idM2_ini,fecha_ini,identif2,precio,comentario,b_manual,b_webact from t_inmueble
		WHERE fecha_fin ='$mifecha'
		ORDER BY b_manual DESC, fecha_ini
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salida = array() ;	
	while( $arr_salida[] = $g_conexion->fetch()) ;
	array_pop( $arr_salida ) ;
	$arr_lee = array() ;
	$i = 0 ;
	$j = 0 ;
	$fecha = date('Y-m-d') ;
	$lafecha = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $fecha , 5 , 2 ) , substr( $fecha , 8 , 2 ) + 14 , substr( $fecha , 0 , 4 ))) ;
	foreach ( $arr_salida as $key=>$val ){
		$i++ ;
		$archivo = $val['url'] ;
		MsjE("\nProcesando $i fecha inicial {$val['fecha_ini']}: $archivo") ;		
		$arr_final = $val ;		
		if ( $archivo == '' OR $val['b_webact'] == '0' ){ //no hay URL o ya se revisó que estaba vacía
			$data = FALSE ;
		}else{
			//$data = file_get_contents( $archivo ) ; //20161101 este es el cambio que evit que busque en metrocuadrado
			$data = FALSE ;
			
		}		
		if ( $data === FALSE ){  //falló la lectura web
			$arr_lee = Array() ;
			$arr_final['web_act'] = 'no' ;
			//MsjE("Página WEB INACTIVA") ;
			MsjE("Página WEB no fue buscada") ;
			$j++ ;
		}else{
			$arr_lee = metro_parser( $data ) ;
			if ( isset($arr_lee['id'])){ //comprobar que la página está viva y marcar el arreglo final correspondientemente con un "si"
				$arr_final['web_act'] = 'si' ;
			}else{
				$arr_final['web_act'] = 'no' ;
				$sql = "UPDATE t_inmueble SET b_webact = '0' WHERE id_inmueble={$val['id_inmueble']}" ;
				$g_conexion->execute ($sql) ;
				$j++ ;
				MsjE("Página WEB INACTIVA") ;
			}
		}
		if ( $val['b_manual'] == 1 ){ //todos los manuales son perdonados en principio
			$arr_final['web_act'] = 'si' ;
		}
		
		ver_arr( $arr_lee , 'arr_lee') ;		
		$arr_mostrar[] = $arr_final ;		
	}
	
	ver_arr( $arr_mostrar ) ;
	
	$arr_html1 = array() ;
	$arr_html2 = array() ;
	foreach ( $arr_mostrar as $key => $val ){					
		//$g_html .="<table border=\"0\" width=\"300\">" ;		
				
		$manual = ( $val['b_manual'] == 1 ? 'Manual' : 'Auto' ) ;
		$borrar = ( $val['web_act'] == 'si' ? '1' : '0' ) ; 	//asignar el flag para mostrar al usuario como candidato a prolongación
		$check = FormCheck("arr_muere[{$val['id_inmueble']}]", $borrar ) ;
		$linea['Inmueble'] = "<a href=\"{$val['url']}\" target=\"_blank\">{$val['idM2_ini']}</a>" ;
		$linea['Fecha Ini'] = $val['fecha_ini'] ; 
		$linea['Caracteristicas'] = $val['identif2'] ;
		$linea['__Precio__'] = '$ ' . formato_n( $val['precio'] / 1000000 , 1 ) ;
		$linea['Coments'] = $val['comentario'] ;
		$linea['Tipo'] = $manual ;
		$linea['Prolongar'] = $check ;
		if ( $borrar == '0' ){ //$arr_html1 se llena con los candidatos a NO renovar porque la página está inactiva
			$arr_html1[] = $linea ;
			msj ('entré inactivo') ;
		}else{	//$arr_html2 se llena con los candidatos a SI renovar
			$arr_html2[] = $linea ; 
			msj ('entré activo') ;
			
		}		
	}
	
	$arr_html = array_merge( $arr_html1 , $arr_html2 ) ; 
	
	$n = array() ;
	$g_html = "Registros con fecha_fin: $mifecha, que tienen la página externa INACTIVA: $j <br>" ;
	$j = $i - $j ;
	$g_html .= "Registros con fecha_fin: $mifecha, que tienen la página externa ACTIVA: $j <br>" ;
	$g_html .= "<form  action='$_SERVER[PHP_SELF]' method='POST'><input type='submit' value='Enviar'>" ;
	$g_html .= "<input type='hidden' NAME='f_fecha2' VALUE='$mifecha'>" ;
	$g_html .= "<input type='hidden' NAME='pag' VALUE='2'>" ;
	$g_html .= html_arreglo_bi( $arr_html , 1 , $n , "Inmuebles por Finalizar: $mifecha") ;	
	$g_html .= "</form>"  ;			
	echo $g_html ;
	
?>