<?php
 /****************************************************************************
  20141106  >> parser_test.php
  Proyecto: Obsevatorio Mario
  Prueba para ensayar descarga de información de www.metrocuadrado.com
  Esta página es para hacer los parse de prueba en el site
  ******************************************************************************/
  
	require_once 'header.php' ;
	
	
	$texto1 = 'h2Info.innerHTML += " de " + tipoNegocio + span;
      }
      else {
	h2Info.innerHTML += " del " + tipoNegocio + span;
      }
    }
  </script>
  <!-- Logo Proyecto -->
  
  <ul>	
  
    <li><div>Barrio Com&uacute;n:</div><strong>Galerias</strong></li>
    
  
    <li><div>Barrio Catastral:</div><strong>Alfonso Lopez</strong></li>
                  	
  
    <li>
      <div>Valor arriendo:</div>
      <span itemprop="priceCurrency" style="display: none">COP</span>
      <strong itemprop="price">$2.800.000</strong>
      
	<a class="thickbox det_infoGeneralNegociar" href="#TB_inline?height=340&amp;width=240&amp;inlineId=det_formaContactoDiv" id="negociarPrecioArriendo" onclick="cambiarAsuntoCorreoContacto(2);contarEventoNegociarOmniture();">Negociar precio</a>
        <span class=\'det_asteriskOrange\'>**</span>
      
    </li>	
    
      
	<li>
	  <div>Valor venta:</div>
	  <span itemprop="priceCurrency" style="display: none">COP</span>
	  <strong itemprop="price">$485.000.000</strong>
	  
	    <a class="thickbox det_infoGeneralNegociar" href="#TB_inline?height=340&amp;width=240&amp;inlineId=det_formaContactoDiv" id="negociarPrecioVenta" onclick="cambiarAsuntoCorreoContacto(3);contarEventoNegociarOmniture();">Negociar precio</a>
	    <span class=\'det_asteriskOrange\'>**</span>
	  
        </li>
     ' ;
	 
	 $texto2 = '
	h2Info.innerHTML += " del " + tipoNegocio + span;
      }
    }
  </script>
  <!-- Logo Proyecto -->
  
  <ul>	
  
    <li><div>Barrio Com&uacute;n:</div><strong>Alfonso Lopez</strong></li>
    
  
    <li><div>Barrio Catastral:</div><strong>Alfonso Lopez</strong></li>
                  	
  
      
	<li>
	  <div>Valor venta:</div>
	  <span itemprop="priceCurrency" style="display: none">COP</span>
	  <strong itemprop="price">$142.000.000</strong>
	  
	    <a class="thickbox det_infoGeneralNegociar" href="#TB_inline?height=340&amp;width=240&amp;inlineId=det_formaContactoDiv" id="negociarPrecioVenta" onclick="cambiarAsuntoCorreoContacto(3);contarEventoNegociarOmniture();">Negociar precio</a>
	    <span class=\'det_asteriskOrange\'>**</span>
	  
        </li>
      
      
	<li><div>Valor admon:</div><strong>$22.000</strong>
                 
               
	<li><div>Estrato:</div>3</li>
      '
	;
	
	
	$regex = '@<strong itemprop="price">(.*?)</strong>@';		
	preg_match($regex,$texto1,$match);  
	ver_arr($match, 't1') ;			
	preg_match($regex,$texto2,$match);  
	ver_arr($match, 't2') ;	
	
	$regex = '@<div>Valor venta:</div>.*\n.*\n.*<strong itemprop="price">(.*?)</strong>@';
	preg_match($regex,$texto1,$match);  
	ver_arr($match, 't1') ;			
	preg_match($regex,$texto2,$match);  
	ver_arr($match, 't2') ;	
	
	
	die() ;
	
	
	
	
	if (isset( $_GET['archivo'] )){
		$elarchivo = $_GET['archivo'] ;
	}else{
		echo "Falta el nombre de archivo" ;
		die() ;
	}
	
	$archivo = "descargas/$elarchivo" ; 
	$arch_listado = $archivo ;

	/*
	if (file_exists( $arch_listado )){    
		$handle = fopen($arch_listado, "r");
		$datain = fread($handle, filesize($arch_listado));
		fclose($handle);    
		$arr_list = unserialize($datain);
	}else{
		echo "/n<br>OJO: no se encuentra archivo base de listado: $arch_listado" ;
	}

	if ( is_array($arr_list) ){
		//ver_arr ($arr_list, "Max" );
		foreach($arr_list as $key1 => $val1 ){
			foreach($val1 as $key2 => $val2 ){
				if ( $key2 == 'contruida' ){ $key2 = 'construida' ; }
				$arr_list2[$key1][$key2] = $val2 ;
			}
		}
		
		$prueba = serialize( $arr_list2 ) ;  
		$handle = fopen($arch_listado, "w" );
		fwrite( $handle , $prueba ) ;
		fclose($handle);
	}
		
	die() ;
	*/

	
	$a = '$450.000.000' ;
	$b = PrecioADecimal($a) ;
	msj($a . '_' . $b ) ;
	
	
	global $g_MiIndice ;      //esta global se usa para guardar un arreglo indexado que resume el query de t_datosweb en metedatos.php, se debería camabiar por un OBJETO 
	
	CreaIndiceM2() ;		
	//ver_arr( $g_MiIndice ) ;
	
	$id = '1953-M1466778XX' ;
	$a = BuscaIDM2( $id ) ;
	msj( $a ) ;	
	//die() ;	
	
	
  
  if (isset( $_GET['archivo'] )){
    $archivo = $_GET['archivo'] ;
  }else{
	echo "Falta el nombre de archivo" ;
	exit() ;
  }
  
	$archivo = "descargas/$archivo" ;
  if (file_exists( $archivo )){    
    $handle = fopen($archivo, "r");
    $data = fread($handle, filesize($archivo));		
		//$data = fread($handle, 5000 );
    fclose($handle);	
  } 
	
	//echo $data ;
	
	$regex1 = '@<span class="street-address">.*Tel.*<span itemprop="telephone">(.*?)</span></span>@';
	$regex2 = '@<span class="street-address">.*Cel.*<span itemprop="telephone">(.*?)</span></span>@';
	msj($regex1);
	preg_match($regex1,$data,$match);  
	ver_arr($match, 'r1') ;	
		
	msj($regex2);
	preg_match($regex2,$data,$match);  
	ver_arr($match, 'r2') ;	
	
	
	$regex1 = '@<span itemprop="productID">(C&oacute;digo|Código):\s(.*?)</span>@' ;
	msj($regex1);
	preg_match($regex1,$data,$match);  
	ver_arr($match, 'r1') ;
	
	$regex1 = '@<span itemprop="productID">(Código|C&oacute;digo):\s(.*?)</span>@' ;
	msj($regex1);
	preg_match($regex1,$data,$match);  
	ver_arr($match, 'r1') ;
	
	
	
	$a = metro_parser($data) ;
	ver_arr($a, 'parseado') ;	
	
?>
