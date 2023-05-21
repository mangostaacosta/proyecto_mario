<?php
 /****************************************************************************
  20141120  >> parser2.php
  Proyecto: Obsevatorio Mario
  Hace la buesqueda de información Nivel 2 de metrocuadrado. Para evitar problemas de timeout
  la página puede recibir un parametro $indice, que le indeica a partir de qué registro del
  archivo de oferta debe comenzar a descargar información y adicionalmente va guardando cada 60 registros
  
  Prueba para ensayar descarga de información de www.metrocuadrado.com
  Esta página puede hacer un parse para extraer los datos específicos de un apartamento 
  
  20160124: No está leyendo bien, por posible cambio en formato M2
  ******************************************************************************/


  
  /*    
      <li><div>Barrio Com&uacute;n:</div><strong>Prados De La Sabana</strong></li>
      <li><div>Barrio Catastral:</div><strong>Villa Del Prado</strong></li>
  	<li>
	  <div>Valor venta:</div>
	  <span itemprop="priceCurrency" style="display: none">COP</span>
	  <strong itemprop="price">$230.000.000</strong>
		    <a class="thickbox det_infoGeneralNegociar" href="#TB_inline?height=300&amp;width=220&amp;inlineId=det_formaContactoDiv" id="negociarPrecioVenta" onclick="cambiarAsuntoCorreoContacto(3);contarEventoNegociarOmniture();">Negociar precio</a>
	    <span class='det_asteriskOrange'>**</span>
        </li>
 	<li><div>Valor admon:</div><strong>$150.000</strong>
 	<li><div>Estrato:</div>3</li>
 			<li><div>&Aacute;rea:</div>65&nbsp;mts<sup>2</sup></li>
                	<li><div>&Aacute;rea construida:</div>65&nbsp;mts<sup>2</sup></li>
 	<li><div>Habitaciones:</div>3</li>
 	<li><div>Ba&ntilde;os:</div>2</li>
 	<li><div>Garajes:</div>1</li>
*/

  
  
	require_once 'header.php' ;
	include "manual_captura_autom.php" ;
	echo TituloPagina( 'Operación > Captura Automática > Datos N2' ) ;
	
	ini_set('max_execution_time', 2000 ) ;
	
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Puede tardar unos minutos', 'OK para iniciar') ;
	$forma->Insertar('indice','Indice Actual') ;
	$forma->siempre = FALSE ;
	$forma->Imprimir() ;
	
	if (isset( $_GET['indice'] )){
		$indice = $_GET['indice'] ;
	}else{
		$indice = 0 ;
		MsjE( "Indice en 0s" ) ;
	}
  
	$arch_listado = $g_archivobase ;
  
	if (file_exists( $arch_listado )){    
		$handle = fopen($arch_listado, "r");
		$datain = fread($handle, filesize($arch_listado));
		fclose($handle);    
		$arr_list = unserialize($datain) ;
		$cant = count( $arr_list ) ;
		MsjE("Registros a procesar: $cant") ;
	}else{
		echo "/n<br>OJO: no se encuentra archivo base de listado: $arch_listado" ;
	}
	
	ver_arr( $arr_list ) ;
	//die();
	
	if (file_exists( $g_archivofinal )){
		$handle = fopen($g_archivofinal, "r" );
		$datain = fread($handle, filesize($g_archivofinal));
		fclose($handle);    
		$arr_final = unserialize($datain);
	}else{
		$arr_final = array() ;
	}
	
	
	$i = 0 ;
	$j = 0 ;
	$t1 = time() ; //20170619
	
	foreach ( $arr_list as $key => $val ){
		if ( $i < $indice ){
			$i++ ;
			continue ;
		}
		$archivo = $val['url'] ;
		$data = file_get_contents( $archivo ) ;

/*		
$archtemp = "../mario_data/apto.dat" ;
$handle = fopen($archtemp, "w" );
fwrite( $handle , $data ) ;
fclose($handle);
exit() ;
*/
		
		
		
		
		
		MsjE( "\n<br>Procesando $i\n<br>Con fecha creacion {$val['fecha']}: $archivo" ) ;
		//echo "\n<br>Procesando $i con fecha creacion {$val['fecha']}: $archivo" ;
		if ( $data === FALSE ){  //falló la lectura web
			$arr_lee = Array() ;
		}else{
			$arr_lee = metro_parser( $data ) ;
		}
		
		if ( isset($arr_lee['id'])){ //comprobar si falló lectura de id
			//nada
		}else{
			$arr_lee = metro_parser_nuevo( $data ) ;  //ensayar lectura para página de para estrenar
		}
		ver_arr( $arr_lee , 'arr_lee') ;
		
		if ( isset($arr_lee['id'])){ //comprobar que la página está viva
			$arr_final[] = array_merge( $val, $arr_lee) ;
		}else{
			MsjE("La página no está activa") ;
		}

		if ( $j == 60 ){
			$prueba = serialize( $arr_final ) ;  
			$handle = fopen($g_archivofinal, "w" );
			fwrite( $handle , $prueba ) ;
			fclose($handle) ;
			$j = 0 ;
		}
		//sleep(1) ;
		if ( $key > 20000 ){
			MsjE( "Demasiados datos, más de 20 mil") ;
			break ;
		}
		$i++ ;
		$j++ ;
	}
    
	$t2 = time() ; //20170619
	$t1 = $t2 - $t1 ;
	$t2 = $t1 / $i ;
	$prueba = serialize( $arr_final ) ;  
	$handle = fopen($g_archivofinal, "w" );
	fwrite( $handle , $prueba ) ;
	fclose($handle);
	
	msjE("Tiempo de Ejecución de Consulta: $t1 seg. Tiempo promedio: $t2 seg") ;	
	echo "\n<br>Fin del proceso de consulta, archivo creado" ;
    
?>
