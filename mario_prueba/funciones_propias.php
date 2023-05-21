<?php
  /****************************************************************************
  20141012  >> funciones_propias.php
  Proyecto: Observatorio Mario
  Funciones que son particulares para este aplicativo
  ******************************************************************************/
  
	function arma_menu_radio( $arr_opciones ){
		
		$texto = "<form action='$_SERVER[PHP_SELF]' method='GET'><input type=\"submit\" value=\"Enviar\">" ;
		
		foreach ( $arr_opciones as $key => $val1) {
			$val = $val1['temat'] . " n=" . $val1['cuent'] ;
			$texto.= "<br><input type=\"radio\" name=\"f_tema\" value=\"$key\">$val" ;
		}		
		$texto.= "<br><input type=\"hidden\" name=\"f_preg\" value=\"0\"></form> " ;
		
		return $texto ;	
	}
	
	
	function metro_parser_nuevo( $data ) {
		$arr_regex['id'] = '@<h3>C&oacute;digo web:<\/h3>.*\n.*<\/dt>.*\n.*<dd>.*\n.*<h4>.*\n\t+(.*)<\/h4>@' ;  //20190120
		$arr_regex['construida'] = '@<h3>&Aacute;rea construida.*\n.*\n.*\n\t+de (.*?) m@' ;  //20190120
		//$arr_regex['area'] = '@<h3>&Aacute;rea privada.*\n.*<\/dt>.*\n.*<dd>.*\n.*<h4>Desde (.*?) hasta@' ;  //20190120
		$arr_regex['catastro'] = '@<h3>Nombre del barrio catastral<\/h3>.*\n.*\n.*\n.*<h4>.*\n\t+(.*?)<\/h4>@' ;  //20190120		
		$arr_regex['estrato'] = '@<h3>Estrato.*\n.*\n.*\n.*\n\t+(.*?)<\/h4>@' ;  //20190120
		
		
		$arr_regex['precio'] = '@<dd class="important">.*\n.*(\$.*)</dd>@' ;	//20180515
		$arr_regex['tele1'] = '@\t\t\t\t\t(\d+)</li>@' ;  //20190112
		
		$arr_regex['barrio'] = '@Bogotá D.C., (.*?)</h1>@' ;  //20170725 lo que pasa acá es que realmente el catastral es el otro
		//$arr_regex['construida'] = '@<dt><h3>&Aacute;rea construida</h3></dt>.*\n.*<dd><h4>(.*?) m<sup>2</sup>@' ;  //20170603			
		//$arr_regex['estrato'] = '@<dt><h3>Estrato</h3></dt>.*\n.*<dd><h4>(.*?)</h4>@' ;  //20170603
		
		foreach ( $arr_regex as $key => $regex ){
			preg_match($regex,$data,$match);
			//$data = $match[0] ;  //posible optimización usando ".*" al final de cada regex
			if ( $key == 'tele1' ){	//para unificar los telefonos y celulares en un solo campo
				$t1 = $match[1] ;				
			}elseif ( $key == 'tele2' ){
				$t2 = $t1 . " " . $match[1] ;
				$arrsalida['telef'] = $t2 ;	
			}else{
				$arrsalida[$key] = $match[1] ;
			}
		}
		
		return $arrsalida ;
	}
	
	//20170603: Nueva función para leer M2
	function metro_parser( $data ) {
	
	//  '^(([A-Z]\d{8})|(\d{9}))$'
	
		$arr_regex['id'] = '@<h3>C&oacute;digo web</h3></dt>.*\n.*<dd><h4>(.*?)</h4>@' ;  //20190120
		//$arr_regex['id'] = '@<dt><h3>C&oacute;digo web</h3></dt>.*\n.*<dd><h4>(.*?)</h4>@' ;  //20180224
		//$arr_regex['id'] = '@<div class="m_property_info_code">C&oacute;d web:\n\t(.*?)</div>@' ;  //20170603
		//$arr_regex['id'] = '@Cód web: (.*?)</div>@' ;
		//$arr_regex['id'] = '@<span itemprop="productID">C&oacute;digo:\s(.*?)</span>@';
		//$arr_regex2['id'] = '@<span itemprop="productID">(C&oacute;digo|Código):\s(.*?)</span>@' ;

		//arreglo para los cambios en apartamentos para estrenar
		//$arr_regex['id2'] = '@<h3>C&oacute;digo web:<\/h3>.*\n.*<\/dt>.*\n.*<dd>.*\n.*<h4>.*\n\t+(.*)<\/h4>@' ;  //20190120

//20170603
/*
<div class="m_property_info_title">
	<h1 itemprop="headline">Apartamento en Venta, Bogotá D.C., CHICO SANTA BARBARA</h1>
</div>
*/		
		
		$arr_regex['barrio'] = '@Bogotá D.C., (.*?)</h1>@' ;  //20170725 lo que pasa acá es que realmente el catastral es el otro			
		//$arr_regex['barrio'] = '@href="http://m.metrocuadrado.com/apartamentos/venta/bogota/(.*?)/@' ;
		
		$arr_regex['catastro'] = '@<dt><h3>Nombre del barrio catastral</h3></dt>.*\n.*<dd><h4>(.*?)</h4>@' ;  //20170725
		//$arr_regex['catastro'] = '@<div>Barrio Catastral:</div><strong>(.*?)</strong>@';	//20170603: no funciona
		
		$arr_regex['precio'] = '@<dd class="important">.*\n.*(\$.*)</dd>@' ;	//20180515
		//$arr_regex['precio'] = '@<dd class="important">.*\n.*(\$.*)\n@' ;	//20180226
		//$arr_regex['precio'] = '@<dd class="important">(.*?)</dd>@' ;	//20170603
		//$arr_regex['precio'] = '@<li><h3><b>Valor Venta</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;		
		//$arr_regex['precio'] = '@<strong itemprop="price">(.*?)</strong>@';
		
		$arr_regex['admon'] = '@<dt><h3>Valor de administraci&oacute;n</h3></dt>.*\n.*<dd><h4>(.*?)</h4>@' ; //20180226
		//$arr_regex['admon'] = '@<li><h3><b>Valor Administración</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;
		//$arr_regex['admon'] = '@<div>Valor admon:</div><strong>(.*?)</strong>@';
		
		$arr_regex['estrato'] = '@<dt><h3>Estrato</h3></dt>.*\n.*<dd><h4>(.*?)</h4>@' ;  //20170603
		//$arr_regex['estrato'] = '@<li><h3><b>Estrato</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;
		//$arr_regex['estrato'] = '@<div>Estrato:</div>(\d*)</li>@';		
		
		$arr_regex['area'] = '@<dt><h3>&Aacute;rea privada</h3></dt>.*\n.*<dd><h4>(.*?) m<sup>2</sup>@' ;  //20170603
		//$arr_regex['area'] = '@<li><h3><b>Área</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?) mts@' ;
		//$arr_regex['area'] = '@<div>&Aacute;rea:</div>(\d+)&nbsp;mts<sup>@';
		
		
		$arr_regex['construida'] = '@<dt><h3>&Aacute;rea construida</h3></dt>.*\n.*<dd><h4>(.*?) m<sup>2</sup>@' ;  //20170603
		//$arr_regex['construida'] = '@<li><h3><b>Área Construida</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?) mts@' ;
		//$arr_regex['construida'] = '@<div>&Aacute;rea construida:</div>(\d+)&nbsp;mts<sup>@';
				
		$arr_regex['habit'] = '@<dt><h3>Habitaciones</h3></dt>.*\n.*\n.*\n.*\n.*<dd><h4>(.*?)</h4></dd>@' ;  //20170603
		//$arr_regex['habit'] = '@<li><h3><b>Habitaciones</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;
		//$arr_regex['habit'] = '@<div>Habitaciones:</div>(\d*)</li>@';
				
		$arr_regex['banhos'] = '@<dt><h3>Ba&ntilde;os</h3></dt>.*\n.*\n.*\n.*\n.*<dd><h4>(.*?)</h4></dd>@' ;  //20170603
		//$arr_regex['banhos'] = '@<li><h3><b>Baños</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;		
		//$arr_regex['banhos'] = '@<div>Ba&ntilde;os:</div>(\d*)</li>@';
		
		$arr_regex['garaj'] = '@<h3>Parqueadero</h3>.*\n.*\n.*<dd>(.*?)</dd>@' ;  //20190113
		//$arr_regex['garaj'] = '@<dt><h3>Parqueadero</h3></dt>.*\n.*\n.*\n.*\n.*<dd><h4>(.*?)</h4></dd>@' ;  //20170603
		//$arr_regex['garaj'] = '@<li><h3><b>Garajes</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;
		//$arr_regex['garaj'] = '@<div>Garajes:</div>(\d*)</li>@';
		
		$arr_regex['piso'] = '@<h3>Número de piso</h3>.*\n.*\n.*\n.*<h4>(.*?)</h4>@' ;	//20190112
		//$arr_regex['piso'] = '@<li><h3><b>Número de piso:</b></h3></li>.*\n.*<li><h4>(.*?)</h4>@' ;
		
		$arr_regex['tele1'] = '@\t\t\t\t\t(\d+)</li>@' ;  //20190112
		//$arr_regex['tele1'] = '@<hr />\n.*\n.*\n\t\t\t\t\t(\d*)</li>@' ;  //20190112
		//$arr_regex['tele1'] = '@<li>(\d*)</li>@' ;  //20170603: super mañoso
		//$arr_regex['tele1'] = '@<div class="telefono_anunciante">(?.*\n)*.*<li>(.*?)</li>@' ;  //20170603: no funciono
		//$arr_regex['tele1'] = '@<div class="telefono_anunciante">.*\n.*\n.*\n.*\n.*\n.*\n.* (.*?)\n@' ; //20170603: no funciono
		//$arr_regex['tele1'] = '@<b>Tel:</b>(.*?)<br/>@' ;
		//$arr_regex['tele1'] = '@<span class="street-address">.*Tel.*<span itemprop="telephone">(.*?)</span></span>@';
		
		
		$arr_regex['tele2'] = '@\t(\d*)\n@' ;  //20170603: super mañoso pero ni idea si funcione
		//$arr_regex['tele2'] = '@<span class="street-address">.*Cel.*<span itemprop="telephone">(.*?)</span></span>@';
		
		
		$arr_regex['ascensor'] = '@<h3>Número de Ascensores</h3>.*\n.*</dt>.*\n.*\n.*\n.*\n.*<h4>(.*?)</h4>@' ;	//20170603
		//$arr_regex['ascensor'] = '@<li><h3><b>Número Ascensores:</b></h3></li>.*\n.*<li><h4>(.*?)</h4>@'; //20160704
		
		
		//$arr_regex['direcc'] = '@(<input type="hidden" id="latitude" value=".*">).*\n@' ; //20180515; se comenta este campo porque signo '<' en la idireccón esta genernado un error en la cadena del GET de edita_inm.php, segurmante por nueva política de seguridad de apache en servidor 
		//$arr_regex['direcc'] = '@(<input type="hidden" id="latitude" value=".*">).*\n@' ; //20180226
		//$arr_regex['direcc'] = '@(<input type="hidden" id="latitude" value=".*">\n.*<input type="hidden" id="longitude" value=".*">)@' ; //20180226
		//$arr_regex['direcc'] = '@<span class="street-address" itemprop="streetAddress">(.*?)</span>@';
		
		
		
		
		foreach ( $arr_regex as $key => $regex ){
			preg_match($regex,$data,$match);
			//$data = $match[0] ;  //posible optimización usando ".*" al final de cada regex
			if ( $key == 'tele1' ){	//para unificar los telefonos y celulares en un solo campo
				$t1 = $match[1] ;				
			}elseif ( $key == 'tele2' ){
				$t2 = $t1 . " " . $match[1] ;
				$arrsalida['telef'] = $t2 ;	
			}else{
				$arrsalida[$key] = $match[1] ;
			}
		}
		
		return $arrsalida ;
  
  //echo "<pre>" ; 
  //print_r($arrsalida) ;  
	}
	
	
	//20170603: Esta funcion ya no sirve para leer M2
	//20160124: Nueva función para leer M2
	function metro_parser_old1( $data ) {
	
	//  '^(([A-Z]\d{8})|(\d{9}))$'
  
		//$arr_regex['id'] = '@<span itemprop="productID">C&oacute;digo:\s(.*?)</span>@';
		//$arr_regex2['id'] = '@<span itemprop="productID">(C&oacute;digo|Código):\s(.*?)</span>@' ;
		$arr_regex['id'] = '@Cód web: (.*?)</div>@' ;
		
		//$arr_regex['barrio1'] = '@<li><div>Barrio Com&uacute;n:</div><strong>(.*?)</strong></li>@';
		//$arr_regex['barrio2'] = '@<li><div>Barrio ((Com&uacute;n:)|(Común:))</div><strong>(.*?)</strong></li>@';
		//$arr_regex['barrio3'] = '@<li><div>Barrio Común:</div><strong>(.*?)</strong></li>@';
		//$arr_regex['barrio4'] = '@<li><div>Barrio Común:</div><strong>(.*?)</strong></li>|<li><div>Barrio Com&uacute;n:</div><strong>(.*?)</strong></li>@';		
		//$arr_regex['barrio5'] = '@<li><div>Barrio Com&uacute;n:</div><strong>(.*?)</strong></li>|<li><div>Barrio Común:</div><strong>(.*?)</strong></li>@';		
		$arr_regex['barrio'] = '@href="http://m.metrocuadrado.com/apartamentos/venta/bogota/(.*?)/@' ;
		
		$arr_regex['catastro'] = '@<div>Barrio Catastral:</div><strong>(.*?)</strong>@';
		
		//$arr_regex['precio'] = '@<strong itemprop="price">(.*?)</strong>@';
		$arr_regex['precio'] = '@<li><h3><b>Valor Venta</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;		
		
		//$arr_regex['admon'] = '@<div>Valor admon:</div><strong>(.*?)</strong>@';
		$arr_regex['admon'] = '@<li><h3><b>Valor Administración</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;
		
		//$arr_regex['estrato'] = '@<div>Estrato:</div>(\d*)</li>@';		
		$arr_regex['estrato'] = '@<li><h3><b>Estrato</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;
		
		//$arr_regex['area'] = '@<div>&Aacute;rea:</div>(\d+)&nbsp;mts<sup>@';
		$arr_regex['area'] = '@<li><h3><b>Área</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?) mts@' ;
		
		//$arr_regex['construida'] = '@<div>&Aacute;rea construida:</div>(\d+)&nbsp;mts<sup>@';
		$arr_regex['construida'] = '@<li><h3><b>Área Construida</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?) mts@' ;
		
		//$arr_regex['habit'] = '@<div>Habitaciones:</div>(\d*)</li>@';
		$arr_regex['habit'] = '@<li><h3><b>Habitaciones</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;
		
		//$arr_regex['banhos'] = '@<div>Ba&ntilde;os:</div>(\d*)</li>@';
		$arr_regex['banhos'] = '@<li><h3><b>Baños</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;
		
		//$arr_regex['garaj'] = '@<div>Garajes:</div>(\d*)</li>@';
		$arr_regex['garaj'] = '@<li><h3><b>Garajes</b></h3></li>.*\n.*<li>.*\n.*<h4>(.*?)</h4>@' ;
		
		$arr_regex['piso'] = '@<li><h3><b>Número de piso:</b></h3></li>.*\n.*<li><h4>(.*?)</h4>@' ;
		
		//$arr_regex['tele1'] = '@<span class="street-address">.*Tel.*<span itemprop="telephone">(.*?)</span></span>@';
		$arr_regex['tele1'] = '@<b>Tel:</b>(.*?)<br/>@' ;
		
		$arr_regex['tele2'] = '@<span class="street-address">.*Cel.*<span itemprop="telephone">(.*?)</span></span>@';
		$arr_regex['direcc'] = '@<span class="street-address" itemprop="streetAddress">(.*?)</span>@';
		
		$arr_regex['ascensor'] = '@<li><h3><b>Número Ascensores:</b></h3></li>.*\n.*<li><h4>(.*?)</h4>@'; //20160704
		
		foreach ( $arr_regex2 as $key => $regex ){
			preg_match($regex,$data,$match);
			$arrsalida[$key] = $match[2] ;			
		}
		
		foreach ( $arr_regex as $key => $regex ){
			preg_match($regex,$data,$match);
			//$data = $match[0] ;  //posible optimización usando ".*" al final de cada regex
			if ( $key == 'tele1' ){	//para unificar los telefonos y celulares en un solo campo
				$t1 = $match[1] ;
			}elseif ( $key == 'tele2' ){
				$t2 = $t1 . " " . $match[1] ;
				$arrsalida['telef'] = $t2 ;	
			}else{
				$arrsalida[$key] = $match[1] ;
			}
		}
		
		return $arrsalida ;
  
  //echo "<pre>" ; 
  //print_r($arrsalida) ;  
	}
	
	//20160124: Esta función ya no sirve para leer M2 debido al cambio de formato sufrido
	function metro_parser_old2( $data ) {
	
	//  '^(([A-Z]\d{8})|(\d{9}))$'
  
		//$arr_regex['id'] = '@<span itemprop="productID">C&oacute;digo:\s(.*?)</span>@';
		$arr_regex2['id'] = '@<span itemprop="productID">(C&oacute;digo|Código):\s(.*?)</span>@' ;
		$arr_regex2['barrio'] = '@<li><div>Barrio (Com&uacute;n|Común):</div><strong>(.*?)</strong></li>@';		
		//$arr_regex['barrio1'] = '@<li><div>Barrio Com&uacute;n:</div><strong>(.*?)</strong></li>@';
		//$arr_regex['barrio2'] = '@<li><div>Barrio ((Com&uacute;n:)|(Común:))</div><strong>(.*?)</strong></li>@';
		//$arr_regex['barrio3'] = '@<li><div>Barrio Común:</div><strong>(.*?)</strong></li>@';
		//$arr_regex['barrio4'] = '@<li><div>Barrio Común:</div><strong>(.*?)</strong></li>|<li><div>Barrio Com&uacute;n:</div><strong>(.*?)</strong></li>@';		
		//$arr_regex['barrio5'] = '@<li><div>Barrio Com&uacute;n:</div><strong>(.*?)</strong></li>|<li><div>Barrio Común:</div><strong>(.*?)</strong></li>@';		
		
		$arr_regex['catastro'] = '@<div>Barrio Catastral:</div><strong>(.*?)</strong>@';
		//$arr_regex['precio'] = '@<strong itemprop="price">(.*?)</strong>@';
		$arr_regex['precio'] = '@<div>Valor venta:</div>.*\n.*\n.*<strong itemprop="price">(.*?)</strong>@';
		$arr_regex['admon'] = '@<div>Valor admon:</div><strong>(.*?)</strong>@';
		$arr_regex['estrato'] = '@<div>Estrato:</div>(\d*)</li>@';
		$arr_regex['area'] = '@<div>&Aacute;rea:</div>(\d+)&nbsp;mts<sup>@';
		$arr_regex['construida'] = '@<div>&Aacute;rea construida:</div>(\d+)&nbsp;mts<sup>@';
		$arr_regex['habit'] = '@<div>Habitaciones:</div>(\d*)</li>@';
		$arr_regex['banhos'] = '@<div>Ba&ntilde;os:</div>(\d*)</li>@';
		$arr_regex['garaj'] = '@<div>Garajes:</div>(\d*)</li>@';
		$arr_regex['tele1'] = '@<span class="street-address">.*Tel.*<span itemprop="telephone">(.*?)</span></span>@';
		$arr_regex['tele2'] = '@<span class="street-address">.*Cel.*<span itemprop="telephone">(.*?)</span></span>@';
		$arr_regex['direcc'] = '@<span class="street-address" itemprop="streetAddress">(.*?)</span>@';
		
		foreach ( $arr_regex2 as $key => $regex ){
			preg_match($regex,$data,$match);
			$arrsalida[$key] = $match[2] ;			
		}
		
		foreach ( $arr_regex as $key => $regex ){
			preg_match($regex,$data,$match);
			//$data = $match[0] ;  //posible optimización usando ".*" al final de cada regex
			if ( $key == 'tele1' ){	//para unificar los telefonos y celulares en un solo campo
				$t1 = $match[1] ;
			}elseif ( $key == 'tele2' ){
				$t2 = $t1 . " " . $match[1] ;
				$arrsalida['telef'] = $t2 ;	
			}else{
				$arrsalida[$key] = $match[1] ;
			}
		}
		
		return $arrsalida ;
  
  //echo "<pre>" ; 
  //print_r($arrsalida) ;  
	}
	
	function niv3_parser( $data ){
		
		$arr_regex['piso'] = '@<li><div>Nro Piso:</div>(\d*?)</li>@';
		$regex = '@<li><div>Nro piso:</div>(\d*?)</li>@';
		preg_match($regex,$data,$match);
		$arrsalida['piso'] = $match[1] ;
		
		$arr_regex['ascensor'] = '@<li><div>(Número|N&uacute;mero) Ascensores:</div>(\d*?)</li>@';
		$regex = '@<li><div>(Número|N&uacute;mero) Ascensores:</div>(\d*?)</li>@';		
		preg_match($regex,$data,$match);
		$arrsalida['ascensor'] = $match[2] ;
		
		ver_arr ( $arrsalida ,'$arrsalida en ni3_parser') ;
		
		return( $arrsalida ) ;		
	}
	
	function CreaIndiceM2(){
		global $g_conexion ;
		global $g_MiIndice ;
		
		$arr_salida = array() ;
		$sql = 'SELECT idM2,id_inmueble FROM t_datosweb' ;
		$g_conexion->execute ($sql);
		while( $linea = $g_conexion->fetch()){
			$arr_salida[] = $linea ;
		}
		foreach ( $arr_salida as $val ) {
			$key = $val['idM2'] ;
			$g_MiIndice[$key] = $val['id_inmueble'] ;
		}
		return (sizeof( $g_MiIndice ));
	}
	
	function BuscaIDM2($id){
		global $g_MiIndice ;
		
		if ( isset( $g_MiIndice[$id] )) {
			return $g_MiIndice[$id] ;
		}else{
			return 0 ;
		}
	}
	
	function CreaBarriosBD(){
		global $g_conexion ;
		global $g_MiBarrios ;
		
		$arr_salida = array() ;
		$sql = 'SELECT id_barrio FROM t_barrio' ;
		$g_conexion->execute ($sql);
		while( $linea = $g_conexion->fetch()){
			$arr_salida[] = $linea ;
		}
		foreach ( $arr_salida as $val ) {
			$key = $val['id_barrio'] ;
			$g_MiBarrios[$key] = $val['id_barrio'] ;
		}		
		return (sizeof( $g_MiBarrios ));
	}
	
	function BuscaBarrio($id){
		global $g_MiBarrios ;
		
		if ( isset( $g_MiBarrios[$id] )) {
			//msj ( "BuscaBarrio SI encontró la id: $id" ) ;
			return 1 ;			
		}else{
			msj ( "BuscaBarrio NO encontró la id: $id" ) ;
			return 0 ;
		}
	}
	
	// Devuelve un arreglo con las parejas de registros que ya han sido señaladas como gemelos en la BD
	function CreaSiGemeloBD(){
		global $g_conexion ;
		global $g_MiSiGemelo ;
		
	
		$sql = 'SELECT idM2_1, idM2_2 FROM t_si_gemelos ORDER BY idM2_1' ;
		$g_conexion->execute ($sql);
		$arrsalida = array() ;
		while( $linea = $g_conexion->fetch()){
			$arr_salida[] = $linea ;
		}
		foreach ( $arr_salida as $val ) {
			$key = $val['idM2_2'] ;
			$g_MiSiGemelo[$key] = $val['idM2_1'] ;
		}
		return (sizeof( $g_MiSiGemelo ));
	}
	
	function TraeGemelo( $idurl ){
		if ( isset( $g_MiSiGemelo[$idurl] )) {
			//msj ( "BuscaBarrio SI encontró la id: $id" ) ;
			return $g_MiSiGemelo[$idurl] ;			
		}else{
			msj ( "TraeGemelo NO encontró la URL: $idurl" ) ;
			return 0 ;
		}
	}

	
	function CreaNoGemeloBD(){
		global $g_conexion ;
		global $g_MiNoGemelo ;
		
	
		$sql = 'SELECT idM2_1, idM2_2 FROM t_no_gemelos' ;
		$g_conexion->execute ($sql);
		$arrsalida = array() ;
		while( $linea = $g_conexion->fetch()){
			$arr_salida[] = $linea ;
		}
		foreach ( $arr_salida as $val ) {
			$key = $val['idM2_1'] . $val['idM2_2'] ;
			$g_MiNoGemelo[$key] = $key ;
		}
		return (sizeof( $g_MiNoGemelo ));
	}
	
	function BuscaNoGemelo($M2_1,$M2_2){
		global $g_MiNoGemelo ;
		
		$id = $M2_1 . $M2_2 ;
		if ( isset( $g_MiNoGemelo[$id] )) {
			//msj ( "BuscaBarrio SI encontró la id: $id" ) ;
			return 1 ;			
		}else{
			msj ( "BuscaNoGemelo NO encontró la combinacion: $id" ) ;
			return 0 ;
		}
	}
	
	function CreaBarriosRepe(){
		global $g_conexion ;
		global $g_MiRepeBarrios ;
		
	
		$sql = 'SELECT * FROM (SELECT barrio, COUNT(barrio) as cuenta FROM t_barrio GROUP BY barrio) AS t_aux WHERE cuenta > 1' ;
		$g_conexion->execute ($sql);
		$arrsalida = array() ;
		while( $linea = $g_conexion->fetch()){
			$arr_salida[] = $linea ;
		}
		foreach ( $arr_salida as $val ) {
			$barrio = $val['barrio'] ;
			$barrio = strtolower( $barrio ) ;
			$barrio = trim( $barrio ) ;
			$search = array(' ') ;
			$replace = array ('_') ;
			$barrio = str_replace($search,$replace, $barrio );
				
			$g_MiRepeBarrios[$barrio] = $barrio ;
		}
		return (sizeof( $g_MiRepeBarrios ));
	}
	
	function BuscaBarrioRepe( $id_barrio ){
		global $g_MiRepeBarrios ;		
		
		if ( isset( $g_MiRepeBarrios[$id_barrio] )) {
			msj ( "BuscaBarrioRepe SI encontró la id: $id_barrio" ) ;
			return 1 ;			
		}else{  
			//msj ( "BuscaBarrioRepe NO encontró la combinacion: $id" ) ;
			return 0 ;
		}
	}
	
	function FormTexto( $nombre, $variable , $titulo = '' , $area = 0 , $letras = 50){
		if ( $area == 0 ){
			return ( "$titulo<INPUT TYPE='text' name='$nombre' VALUE='$variable' SIZE='$letras' >" ) ;
		}else{
			return( "$titulo<textarea rows='2' cols='$letras' name='$nombre'>$variable</textarea>") ;
		}
	}
	
	function FormCheck( $nombre, $variable ){
	// esta es una funcion restringida a campos binarios inicialmente
		if ( $variable == 0 ){
			$temp = '' ;
		}else{
			$temp = 'checked' ;
		}
		return( "<input type=\"checkbox\" name=\"$nombre\" value='1' $temp>") ;
	}
	
	function FormPuntos( $nombre, $variable ){	
		msj("FormPuntos nombre:$nombre valor:$variable") ;
		$texto = "<select name='$nombre'>\n" ;
		$arr_num = array(7,6,5,1,0) ;
		$arr_num = array(7,6,5,1,0) ;
		//for ( $i = 7 ; $i >= 0 ; $i-- ){
		foreach ( $arr_num as $key => $i ){
			if ( $i == $variable ){
				$texto .= "<option  selected=\"selected\"  autocomplete=\"off\" value='$i'>$i</option>\n" ;
			}else{
				$texto .= "<option value='$i'>$i</option>\n" ;
			}
		}				
		return $texto ;
	}
	
	function FormPuntos1( $nombre, $variable , $arr_nom_punts ){
	//crea el texto para el campo correspondiente a una de las variables de evaluación en el FORM de html
		msj("FormPuntos nombre:$nombre valor:$variable") ;
		$texto = "<select name='$nombre'>\n" ;		
		$arr_num = array(7,6,5,1,0) ;
		//for ( $i = 7 ; $i >= 0 ; $i-- ){
		
		foreach ( $arr_num as $key => $i ){
			$tNom = $arr_nom_punts[$key] ;
			if ( $i == $variable ){				
				$texto .= "<option  selected=\"selected\"  autocomplete=\"off\" value='$i'>$i: $tNom</option>\n" ;
			}else{
				$texto .= "<option value='$i'>$i: $tNom</option>\n" ;
			}
		}				
		return $texto ;
	}
	
	
	function FormMenuBarrio( $nombre, $variable='NINGUNO' ){
		global $g_conexion ;
		
		//$sql = "SELECT DISTINCT id_barrio, barrio, sector, zona , b_sgto FROM t_barrio ORDER BY b_sgto DESC, zona, sector, barrio " ;	
		$sql = "SELECT DISTINCT id_barrio, barrio, sector, zona , b_sgto FROM t_barrio ORDER BY zona, b_sgto DESC, barrio " ;	
		$g_conexion->execute( $sql ) ;
		$arr_salidabd = array() ;
		while ($arr_salidabd[] = $g_conexion->fetch()) ;
		array_pop( $arr_salidabd ) ;
		
		//ver_arr ($arr_salidabd,'$arr_salidabd') ;
		$arr_mostrar = array() ;
		foreach ( $arr_salidabd as $key => $val ){
			$linea['texto'] = $val['zona'] . ">>" . $val['id_barrio'] . ">" . $val['sector'] ;
			$indice = $val['id_barrio'] ;
			$linea['valor'] = $val['b_sgto'] ;
			$arr_mostrar[$indice] = $linea ;
			//msj ($texto) ;
			//msj ($indice) ;
		}
		
		$texto = "<select name='$nombre'>\n" ;
		foreach ( $arr_mostrar as $key => $val ){
			if ( $key == $variable ){
				$texto .= "<option  selected=\"selected\"  autocomplete=\"off\" value='$key'>{$val['texto']}</option>\n" ;
			}else{
				$texto .= "<option value='$key'>{$val['texto']}</option>\n" ;
			}
		}
	
		return $texto ; 
	}
	
	function ActivarFechaBD( $mifecha ){
	//ajusta la bandera b_activo en la BD t_inmueble de acuerdo con la fecha, para buscar inmuebles que estuvieran activos en lso 15 DIAS CONSITGUOS
		global $g_conexion ;
	
		$lafecha = $mifecha ;
		//$lafecha = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $mifecha , 5 , 2 ) , 15 , substr( $mifecha , 0 , 4 ))) ;
		//$lafecha = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $mifecha , 5 , 2 ) , 25 , substr( $mifecha , 0 , 4 ))) ;
		//$mañana        = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
		
		msj("la fecha: $lafecha") ;			
		
		$sql = "UPDATE t_inmueble SET b_activo = '0' WHERE 1" ;
		$g_conexion->execute ($sql) ;		
		$sql = "UPDATE t_inmueble SET b_activo = '1' WHERE (fecha_ini<='$lafecha' AND (fecha_fin IS NULL OR fecha_fin>'$lafecha'))" ;
		$g_conexion->execute ($sql) ;	
	}
	
	
	function ActivarFechaBD_Rango( $psfecha ){
	//ajusta la bandera b_activo en la BD t_inmueble de acuerdo con la fecha, para buscar inmuebles que estuvieran activos en lso 15 DIAS CONTIGUOS
		global $g_conexion ;
	
		$lafecha = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $psfecha , 5 , 2 ) , substr( $psfecha , 8 , 2 ) + 7 , substr( $psfecha , 0 , 4 ))) ;
		$lafecha2 = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $psfecha , 5 , 2 ) , substr( $psfecha , 8 , 2 ) - 7 , substr( $psfecha , 0 , 4 ))) ;
		//$mañana        = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
		msj("la fecha: $lafecha la fecha2: $lafecha2") ;			
		
		$sql = "UPDATE t_inmueble SET b_activo = '0' WHERE 1" ;
		$g_conexion->execute ($sql) ;		
		$sql = "UPDATE t_inmueble SET b_activo = '1' WHERE (fecha_ini<'$lafecha' AND (fecha_fin IS NULL OR fecha_fin>'$lafecha2'))" ;
		$g_conexion->execute ($sql) ;	
	}
	
	function VentanaHelp( $manual ){	
	
		$orig = array( '"' , '(' , ')' ) ;
		$repl = array( '\"' ,  '\(' , '\)' ) ;
		
		$texto = str_replace( $orig , $repl , $manual['tex'] ) ; 
		$texto = str_replace(array("\r\n", "\r", "\n"), "<br />", $texto); 
		
		$html = '<button onclick="myFunction()">Guía de Usuario</button>
			<script>
			function myFunction() {
				var myWindow = window.open("", "_blank", "scrollbars=yes, width=300, height=400"); ' ;
		$html .= "myWindow.document.write(\"<h2>{$manual['tit']}</h2><p>$texto</p>\"); " ;
		$html .= '
			}
			</script>' ;
		
		return $html ;
	}
	
	function TituloPagina( $texto ) {
		$html = "<center><h2>$texto</h2></center>" ;
		return $html ;
	}
	
	
	
?>