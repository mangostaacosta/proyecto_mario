<?php
 /****************************************************************************
  20141106  >> parser1.php
  Proyecto: Obsevatorio Mario
  Prueba para ensayar descarga de información de www.metrocuadrado.com
  Esta página puede hacer un parse para extraer las direcciones (duplicadas) del listado de aptos de metrocuadrado
  
  20160124: Cambio debido a que formato de página descarga menú de M2 cambió, incluso dejó d incluir fecha de publicación 
  
  ******************************************************************************/
  
	require_once 'header.php' ;
	echo TituloPagina( 'Operación > Captura Automática > Datos N1 estándar' ) ;
	
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

  
	//20160626: cambio de regex 
	
/*20170603
NUEVO:
<div class="m_rs_list_item_main">

			<div class="content">
		<div class="header">
				<a itemprop="url" class="data-details-id" href="http://www.metrocuadrado.com/inmueble/venta-apartamento-bogota-cedro-salazar-2-habitaciones-2-banos-1-garajes/3155-1817756">
				<h2 itemprop="name">Apartamento en Venta, CEDRO SALAZAR Bogotá D.C..</h2></a>	
	

/*20160626
NUEVO ANTERIOR:  
,"urlDetalleInmueble":"http://www.metrocuadrado.com/venta/apartamento-en-bogota-chapinero-alto-ingemar-con-2-habitaciones-2-ba%c3%b1os-2-garajes-estrato-5-area-97-mts-$560.500.000-id-3422-533-8",

ANTIGUO
<a href=\"http://www.metrocuadrado.com/venta/apartamento-en-bogota-marly-quesada-con-3-habitaciones-1-ba%c3%b1os-0-garajes-estrato-3-area-82-mts-$215.000.000-id-4653-1344722\" target=\"_blank\" itemprop=\"url\"> 
*/
	//$regex1 = '@<a href=\\\"(http://www.metrocuadrado.com/venta.+?)\\\"@';  //not greedy pre201606
	//$regex1 = '@urlDetalleInmueble":"(http://www.metrocuadrado.com/venta.+?)"@';  //not greedy
	//$regex1 = '@<a itemprop="url" class="data-details-id" .* href="(http://www.metrocuadrado.com/.+?)"@';  //not greedy
	$regex1 = '@<a itemprop="url" class="data-details-id" .* href="(https://www.metrocuadrado.com/.+?)"@';  //not greedy  //20190113
	preg_match_all($regex1,$data,$arrmatch1);
	
	
	//20160124: Desactivación de búsqueda de tag "publicadohace" ya que dejó de funcionar en M2
	//$regex2 = '/<div class=\\\"Publicadohace\\\">(\\\t)*(.*?)<br/' ; //20160124: este tag dejó de ser funcional en M2
	//preg_match_all($regex2,$data,$arrmatch2);

	//20150225: intentando mirar si puedo extraer información adicional como precio TODAVIA EN PROYECTO
	//20160626: cambio de regex 

/*20170603
NUEVO:
	<span itemprop="price">$340.000.000</span>

/*20160626
NUEVO ANTERIOR:  
,"valorVentaConFormato":"$600.000.000",
ANTIGUO
<i itemprop=\"price\">\t\t\t$270.000.000<meta itemprop=\"priceCurrency\" content=\"COP\" />
*/
	//$regex3 = '/<i itemprop=\\\"price\\\">\\\t\\\t\\\t(.+?)<meta/' ; //20160124: para coger el triple tabulador
	//$regex3 = '/<i itemprop=\\\"price\\\">(.+?)<meta/' ;
	//$regex3 = '/valorVentaConFormato":"(.+?)"/' ; 
	$regex3 = '@<span itemprop="price">(.+?)</span>@' ;
	preg_match_all($regex3,$data,$arrmatch3);
	
	
	//ver_arr($arrmatch1) ;
	//ver_arr($arrmatch2) ;
	//ver_arr($arrmatch3) ;

	// a continuación se elimanan todas las direcciones duplicadas
	
	//exit() ;
	
	//20160124: ahora las urls viene duplicadas 3 veces
	/*
	$keep = 1 ;
	foreach ($arrmatch1[1] as $val){		
		if ( $keep == 1 ){
			$arr_temp[] = $val ;		
		}
		$keep = ( $keep == 1 ? 0 : 1 ) ;
	}
	*/
	
	//20160124: ahora las urls viene duplicadas 3 veces
	
	$last = '1' ;
	foreach ($arrmatch1[1] as $val){		
		if ( $last == $val ){
			//nada
		}else{
			$arr_temp[] = $val ;
		}
		$last = $val ;
	}
	
	//20160124: analisis ahora basado en precios y no fechas
	if ( count($arr_temp) != count($arrmatch3[1])){
		$urls = count($arr_temp) ;
		$fecha_tex = count($arrmatch3[1]) ;	
		echo "OJO: Problema de analizador caracterográfico, cantidad urls:$urls, cantidad precios: $fecha_tex <br>" ;
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
	
	$sumatoria = 0 ;   
	foreach ($arrmatch3[1] as $key => $val){
		$arr_temp2['fecha'] = $fecha ;
		$arr_temp2['url'] = $arr_temp[$key] ;
		$arr_temp2['time'] = 'NA' ; //20160124: ya no hay fecha publicación
		$arr_temp2['zona'] = $f_zona ;
		$arr_temp2['antig'] = $f_antig ;		
		$arr_temp2['tipo'] = $f_tipo ;
		$arr_temp2['pprec'] = $arrmatch3[1][$key] ;
		$arr_salida[] = $arr_temp2 ;
		$ultimo = $arr_temp2['url'] ;
		$sumatoria += PrecioADecimal( $arr_temp2['pprec'] ) ;
	}
	//print_r($arrmatch) ;

  $conteo = count( $arrmatch3[1] ) ;
  $total = count( $arr_salida ) ;
  $sumatoria = formato_n( $sumatoria / $conteo , 0 ) ;
  MsjE( "Se agregaron $conteo registros. La URL del último de los registros está en renglón inferior.<br>El promedio de precio fue: $sumatoria.<br>Por favor revisar que la zona sea la esperada: <b>$f_zona</b> <br>$ultimo<br>Total registros listos para sgte paso: $total" ) ;
    
  ver_arr($arr_salida) ;
  
  $prueba = serialize( $arr_salida ) ;  
  $handle = fopen($arch_guardar, "w" );
  fwrite( $handle , $prueba ) ;
  fclose($handle);
  
?>
