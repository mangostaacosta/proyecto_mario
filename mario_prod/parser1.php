<?php
 /****************************************************************************
  20141106  >> parser1.php
  Proyecto: Obsevatorio Mario
  Prueba para ensayar descarga de información de www.metrocuadrado.com
  Esta página puede hacer un parse para extraer las direcciones (duplicadas) del listado de aptos de metrocuadrado
  ******************************************************************************/
  
	require_once 'header.php' ;
	
	//sleep(3) ;
  
	if (isset( $_GET['archivo'] )){
		$elarchivo = $_GET['archivo'] ;
	}else{
		echo "Falta el nombre de archivo" ;
		die() ;
	}
	$archivo = "$g_ruta/$elarchivo" ;
	
	if (isset( $_GET['zona'] )){
		$f_zona = $_GET['zona'] ;
	}else{
		echo "Falta el nombre de la zona" ;
		die() ;
	}	
	if (isset( $_GET['antig'] )){
		$f_antig = $_GET['antig'] ;
	}else{
		echo "Falta la antigüedad" ;
		die() ;
	}
	if (isset( $_GET['tipo'] )){
		$f_tipo = $_GET['tipo'] ;
	}else{
		echo "Falta la tipologia" ;
		die() ;
	}
		
	if (isset( $_GET['fecha'] )){
		$fecha = $_GET['fecha'] ;
	}else{
		$fecha = date('Y-m-d') ;
		echo "Se toma la fecha de hoy: $fecha" ;		
	}
	
	
  
	if (file_exists( $archivo )){    
		$handle = fopen($archivo, "r");
		$data = fread($handle, filesize($archivo));
		fclose($handle);	
	}else{
		echo "No se encuentra archivo: $archivo " ;
	}
  
    
  //$regex = '/itemprop=\\\"price\\\">\s*(\$\d+(\.\d+)*)/';   //no sirvio
  //$regex = '/itemprop=\\\"price\\\">/';
  //$regex = '/itemprop=\\\"price\\\">.*(\$\d+(\.\d+)*)/';  //para coger el precio
  //$regex = '/<a href=\\\"(http:.+)\\\"/';  //very greedy
  //$regex = '@<a href=\\\"(http://www.metrocuadrado.com/venta.+?)\\\"@';  //not greedy
    
  //echo "$regex<br><br>\n$data<br><br>\n" ;
  //echo "$regex<br><br>\n" ;  
  //preg_match($regex,$data,$match);
  //var_dump($match);
  //echo "<pre>" ; 
  //print_r($match) ;
  //echo $match[1];  
  
  $regex1 = '@<a href=\\\"(http://www.metrocuadrado.com/venta.+?)\\\"@';  //not greedy
  preg_match_all($regex1,$data,$arrmatch1);
  $regex2 = '/<div class=\\\"Publicadohace\\\">(\\\t)*(.*?)<br/' ;
  preg_match_all($regex2,$data,$arrmatch2);
  
  // a continuación se elimanan todas las direcciones duplicadas
  $keep = 1 ;
  foreach ($arrmatch1[1] as $val){
		if ( $keep == 1 ){
			$arr_temp[] = $val ;		
		}
		$keep = ( $keep == 1 ? 0 : 1 ) ;	
  }
  
  if ( count($arr_temp) != count($arrmatch2[2])){
		$urls = count($arr_temp) ;
		$fecha_tex = count($arrmatch2[2]) ;	
		echo "OJO: Problema de analizador caracterográfico, cantidad urls:$urls, cantidad fechas: $fecha_tex" ;
  }
  
  
  $arch_guardar = $g_archivobase ;
    
  if (file_exists( $arch_guardar )){    
    $handle = fopen($arch_guardar, "r");
    $datain = fread($handle, filesize($arch_guardar));
    fclose($handle);    
    $arr_salida = unserialize($datain);
  }else{
		$arr_salida = Array() ;
  }
  
	
  
  
	foreach ($arrmatch2[2] as $key => $val){
		$arr_temp2['fecha'] = $fecha ;
		$arr_temp2['url'] = $arr_temp[$key] ;
		$arr_temp2['time'] = $val ;
		$arr_temp2['zona'] = $f_zona ;
		$arr_temp2['antig'] = $f_antig ;		
		$arr_temp2['tipo'] = $f_tipo ;
		$arr_salida[] = $arr_temp2 ;	
	} 
	//print_r($arrmatch) ;

  echo "<pre>" ;
  print_r($arr_salida) ;
  
  $prueba = serialize( $arr_salida ) ;  
  $handle = fopen($arch_guardar, "w" );
  fwrite( $handle , $prueba ) ;
  fclose($handle);
  
?>
