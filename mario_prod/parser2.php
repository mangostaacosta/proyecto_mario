<?php
 /****************************************************************************
  20141120  >> parser2.php
  Proyecto: Obsevatorio Mario
  Prueba para ensayar descarga de información de www.metrocuadrado.com
  Esta página puede hacer un parse para extraer los datos específicos de un apartamento 
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
	ini_set('max_execution_time', 600);
  
	$arch_listado = $g_archivobase ;
  
	if (file_exists( $arch_listado )){    
		$handle = fopen($arch_listado, "r");
		$datain = fread($handle, filesize($arch_listado));
		fclose($handle);    
		$arr_list = unserialize($datain);
	}else{
		echo "/n<br>OJO: no se encuentra archivo base de listado: $arch_listado" ;
	}
	
	ver_arr( $arr_list ) ;
	//die();
	
	$i = 0 ;  
	foreach ( $arr_list as $key => $val ){
		$archivo = $val['url'] ;
		$data = file_get_contents( $archivo ) ;
		$i++ ;
		echo "\n<br>Procesando $i con fecha creacion {$val['fecha']}: $archivo" ;
		if ( $data === FALSE ){  //falló la lectura web
			$arr_lee = Array() ;
		}else{
			$arr_lee = metro_parser( $data ) ;  
		}
		ver_arr( $arr_lee , 'arr_lee') ;
		
		if ( isset($arr_lee['id'])){ //comprobar que la página está viva
			$arr_final[] = array_merge( $val, $arr_lee) ;
		}else{
			MsjE("La página no está activa") ;
		}		
		//sleep(1) ;
		if ( $key > 20000 ){
			MsjE( "Demasiados datos, más de 20 mil") ;
			break ;
		}
	}
    
	$prueba = serialize( $arr_final ) ;  
	$handle = fopen($g_archivofinal, "w" );
	fwrite( $handle , $prueba ) ;
	fclose($handle);
	
	echo "\n<br>Fin del proceso de consulta, archivo creado" ;
    
?>
