<?php
 /****************************************************************************
  20151128  >> lee_M2.php
  Proyecto: Obsevatorio Mario
  Intenta replicar el código RUBY que le compré a sinnaptic 
  ******************************************************************************/

	function leer_m2( $ciudad, $tipo, $ca, $pd, $ph, $out, $zona, $max, $estado, $tiempo ){
		
		//$ciudad = "bogota" ;
		//$tipo = "apartamento" ;
		//$zona = "chapinero" ;
		$page = "1" ;
		$top = ceil($max/50) ;
		$texto = "" ;
		$top = min( 20, $top) ;	//no mas de 1000 aptos para no reventar el timeout en las siguientes etapas
		
		for ( $page = 1 ; $page <= $top ; $page++ ){
			$url = "http://www.metrocuadrado.com/search/list/non-ajax/?&mnrogarajes=&mnrobanos=&mnrocuartos=&mtiempoconstruido=$tiempo&marea=&mvalorarriendo=&mvalorventa=&mciudad=$ciudad&mubicacion=&mtiponegocio=venta&mtipoinmueble=$tipo&mzona=$zona&msector=&mbarrio=&selectedLocationCategory=2&selectedLocationFilter=mzona&mestadoinmueble=&madicionales=&orderBy=&sortType=&companyType=&companyName=&midempresa=&currentPage=$page&totalPropertiesCount=300&totalUsedPropertiesCount=300&totalNewPropertiesCount=0" ;
			
			/*
			$url = "http://www.metrocuadrado.com/search/list/ajax?&mnrogarajes=&mnrobanos=&mnrocuartos=&mtiempoconstruido=Entre 10 y 20 años&marea=&mvalorarriendo=&mvalorventa=&mciudad=bogota&mubicacion=&mtiponegocio=venta&mtipoinmueble=apartamento&mzona=norte&msector=&mbarrio=&selectedLocationCategory=2&selectedLocationFilter=mzona&mestadoinmueble=&madicionales=&orderBy=&sortType=&companyType=&companyName=&midempresa=&mgrupo=&mgrupoid=&mbasico=&currentPage=2&totalPropertiesCount=500&totalUsedPropertiesCount=500&totalNewPropertiesCount=0&sfh=1";
			*///esta línea fue un ejemplo de los nuevos query de POST que estaba generando M2 en 20180301 pero al fin no fue necesario
			
			$texto .= trae_info_m2( $url, $out, $max, $tiempo, 0 ) ;
		}
		return $texto ;	
	}
	
	function trae_info_m2( $url, $out, $max, $tiempo, $ronda ){
				
		$url = substr( $url, 7 ) ;
		$url = myUrlEncode( $url ) ; //20170625: estaba molestando con un caracter &current
		$url = 'http://' . $url ;
		$url = str_replace('+','%20',$url); 
				
		msj("<br>\nConsulta_M2: $url <br>") ;
		
		$opts = array('http' => 
			array(
				'header'  => "Content-Type: text/xml\r\n",
				'user_agent' => "Mozilla/5.0 (Windows; U; Windows NT 5.1; cs; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8",
				'timeout' => 30
				)
			);
		
		$context = stream_context_create( $opts ) ;
		$web_contents = file_get_contents( $url, false, $context ) ; // rubi: web_contents  = open(encoded_url+"&irA=1") {|f| f.read }		
		
		if ( $web_contents === FALSE){
			msjE('La página de metrocuadrado devolvió un error') ;
			$salida = "" ;
		}else{
			//echo "<pre><plaintext>resultado:  $web_contents </pre>" ;
			$salida = $web_contents ;
		}		
		return $salida ;	
	}
  
	function leer_m2_back( $ciudad, $tipo, $ca, $pd, $ph, $out, $zona, $max, $estado, $tiempo ){
		
		if ( $ca == "Compra" ){
			if (( $pd == "0" ) && ( $ph=="0")){
				$stringprecios = "&mvalorventarango=0" ;
			}else{
				$stringprecios = "&mvalorventa=$pd;$ph" ;
			}
		}
		if ( $ca == "Arriendo" ){
			if (( $pd == "0" ) && ( $ph=="0")){
				$stringprecios = "&mvalorarriednorango=0" ;
			}else{
				$stringprecios = "&mvalorarriendo=$pd;$ph" ;
			}
		}
		
		if ( $zona != "Todas" ){
			$url = "http://www.metrocuadrado.com/servlet/co.com.m2.servlet.ajax.MostrarInmueblesFastJson?requestXML=1&mciudad=$ciudad&mtipoinmueble=$tipo$stringprecios&mzona=$zona" ;
		}else{
			$url = "http://www.metrocuadrado.com/servlet/co.com.m2.servlet.ajax.MostrarInmueblesFastJson?requestXML=1&mciudad=$ciudad&mtipoinmueble=$tipo$stringprecios" ;
		}
		
		$url = $url . "&mestadoinmueble=$estado" ;
		
		if ( $estado == "Nuevo" ){
			$url = $url . "&mtiempoconstruido=En Construcción,Sobre Plano,Para Estrenar" ;
		}
		
		if ( $tiempo != "Todos" ){
			$url = $url . "&mtiempoconstruido=$tiempo" ;
		}
		
		$todas =  ceil($max / 16) + 1 ;
		
		$texto = "" ;
		for ( $n = 1 ; $n < $todas ; $n++ ){
			$texto .= trae_info_m2( $url, $out, $max, $tiempo, $n ) ;
		}
		return $texto ;
		
	}
	
	function myUrlEncode($string) {
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
		return str_replace($entities, $replacements, urlencode($string));
	}
	
	function trae_info_m2_back( $url, $out, $max, $tiempo, $ronda ){
		
		$url .= "&irA=$ronda" ;		
		$url = substr( $url, 7 ) ;
		$url = myUrlEncode( $url ) ;
		$url = 'http://' . $url ;
		echo "<br>\nConsulta_M2: $url <br>\n" ;
		
		$web_contents = file_get_contents( $url ) ; // rubi: web_contents  = open(encoded_url+"&irA=1") {|f| f.read }
		
		//$hash = json_decode( $web_contents , TRUE ) ; // esta función fallap porque las llaves vienen encerradas en comillas
		
		//"UrlDetalle":"\/venta\/apartamento\/bogota\/chapinero\/chapineroalto\/apartamento-chapinerocentral-42mts_MC1267707?idInmueble=MC1267707"
		$regex1 = '@"UrlDetalle":"(.+?)"@';  //not greedy		
		preg_match_all($regex1,$web_contents,$arrmatch1);		
		$regex2 = '@"ValorNegocio":"(.+?)"@';  //not greedy		
		preg_match_all($regex2,$web_contents,$arrmatch2);		
		$regex3 = '@"FechaPublicacion":"(.+?)"@';  //not greedy
		preg_match_all($regex3,$web_contents,$arrmatch3);
		
		
		foreach ( $arrmatch1[1] as $key => $val ){
			$val = str_replace('\\', "", $val ) ;
			$val = "<a href=\\\"http://www.metrocuadrado.com$val\\\" \ntarget=\\\"_blank\\\" itemprop=\\\"url\\\">$key</a>" ;
			$arrmatch1[1][$key] = $val ;
		}
		foreach ( $arrmatch2[1] as $key => $val ){
			$val = "<i itemprop=\\\"price\\\">$val<meta itemprop=\\\"priceCurrency\\\" content=\\\"COP\\\"/></i>" ;
			$arrmatch2[1][$key] = $val ;
		}
		foreach ( $arrmatch3[1] as $key => $val ){
			$val = "<div class=\\\"Publicadohace\\\">$val" . "</div><br>\n" ;
			$arrmatch3[1][$key] = $val ;			
		}
		
		
		$regex = '@"Area":(.+?),@';  //"Area":100.0
		preg_match_all($regex,$web_contents,$arrmatch);
		foreach ( $arrmatch[1] as $key => $val ){
			$val = "<miarea>$val</miarea>" . '<br />' ;
			$arrsale[$key] .= $val ;			
		}
		$regex = '@"NroBanos":"(.+?)"@';  //"NroBanos":"3"
		preg_match_all($regex,$web_contents,$arrmatch);
		foreach ( $arrmatch[1] as $key => $val ){
			$val = "<mibanho>$val</mibanho>" . '<br />' ;
			$arrsale[$key] .= $val ;			
		}		
		$regex = '@"NombreComunBarrio":"(.+?)"@';  //"NombreComunBarrio":"COLINA"
		preg_match_all($regex,$web_contents,$arrmatch);
		foreach ( $arrmatch[1] as $key => $val ){
			$val = "<mibarrio>$val</mibarrio>" . '<br />' ;
			$arrsale[$key] .= $val ;			
		}		
		$regex = '@"Cuartos":"(.+?)"@';  //"Cuartos":"3"
		preg_match_all($regex,$web_contents,$arrmatch);
		foreach ( $arrmatch[1] as $key => $val ){
			$val = "<micuarto>$val</micuarto>" . '<br>' ;
			$arrsale[$key] .= $val ;			
		}
		$regex = '@"IdInmueble":"(.+?)"@';  //"IdInmueble":"2993-1069193"
		preg_match_all($regex,$web_contents,$arrmatch);		
		foreach ( $arrmatch[1] as $key => $val ){
			$val = "<idM2>$val</idM2>" . '<br>' ;
			$arrsale[$key] .= $val ;			
		}
		$regex = '@"ComentarioPublicacion":"(.+?)"@';  //"ComentarioPublicacion":" kkk "
		preg_match_all($regex,$web_contents,$arrmatch);		
		foreach ( $arrmatch[1] as $key => $val ){
			$val = "<micoment>$val</micoment>" . '<br>' ;
			$arrsale[$key] .= $val ;			
		}
		
		ver_arr( $arrmatch1 ) ;
		ver_arr( $arrmatch2 ) ;
		ver_arr( $arrmatch3 ) ;
		ver_arr( $arrsale , "ultimo" ) ;
		
		$salida = "" ;
		foreach ( $arrmatch1[1] as $key => $val ){
			$salida .= $arrmatch1[1][$key] . "<br>" . $arrmatch2[1][$key] . "<br>" . $arrmatch3[1][$key] . $arrsale[$key] . "<br>\n" ;
		}
		
		return $salida ;
		
	}

	
	/*
	def get_info(url,out,max,tiempo)
	begin
	
		# Toca revisar el ciclo de vida del archivo y del proceso, verificar como se llama el ruby, se requiere mas tiempo
		#puts "Dentro de get_info<br>"
		#puts max + "<br>"
		
		
		encoded_url = URI.encode(url)
		parsed_url  = URI.parse(encoded_url)

		web_contents  = open(encoded_url+"&irA=1") {|f| f.read }
		#f_prb = File.new("prueba_datps",  "w+")
		#web_contents  = open(encoded_url+"&irA=1") {|f_prb| f_prb.read }
		
		#puts encoded_url+"&irA=1"
		#puts web_contents
		
		#puts "hola1<br>"
		#puts "hola11<br>"
		json_web = JSON.pretty_generate(JSON.load(web_contents))
		
		#puts "hola2<br>"
		hash = JSON.parse(json_web)
		#puts "hola3<br>"
		p = 1	#Contador de Paginas
		inmueblesHTML = Array.new
		prop = 0
 		max_inm = max.to_i
 		
		totalinmuebles = hash["encontrados"]

		pag_total = totalinmuebles / 16
		
		while ((p < (pag_total+1)) && ( prop < max_inm))   do		
		
			hash["inmuebles"].each do |inmueble|
				if(out == "TEXT")
					inmueble_txt(inmueble)
				else
					inmueblesHTML[prop]=inmueble_html(inmueble)
				end	
				prop = prop + 1
			end
			p = p + 1
			web_contents  = open(encoded_url+"&irA="+p.to_s) {|f| f.read }
			
			puts encoded_url+"&irA="+p.to_s
			puts "<br>Iteracion<br>"
			puts web_contents + "<br>Iteracion<br>"
			
			json_web = JSON.pretty_generate(JSON.load(web_contents))
			hash = JSON.parse(json_web)
			
		end


		if(out == "TEXT")
			#puts "URL: "+url+"&traeresultados=</br></br></br>"				
			puts "Total de propiedades: "+prop.to_s+" de "+totalinmuebles.to_s+"</br></br></br>"
		end

			  # "filtrosHTML": [
				# "<div class=\"rb_contFiltro\"><h4>Ciudad</h4><select lang=\"es\" class=\"chzn-select\" onchange=\"procesarFiltroBarraLateral(this , \"Ciudad\" , \"\" );enviarEventoOmniture(\"venta\", this , \"Ciudad\");\"  name=\"ciudad\" id=\"mciudad\" ><option  value=\"-1\">Todas las ciudades</option><option id=\"Bogotá D.C.\" value=\"Bogotá D.C.\"selected=\"selected\" >Bogotá D.C. (539) </option><option id=\"Medellín\" value=\"Medellín\" >Medellín (146) </option><option id=\"Barranquilla\" value=\"Barranquilla\" >Barranquilla (103) </option></select><div class=\"clearfix\"></div></div>",
				# "<div class=\"rb_contFiltro\"><h4>Zona</h4><select lang=\"es\"  onchange=\"procesarFiltroBarraLateral(this , \"Zona\" , \"\" );enviarEventoOmniture(\"venta\", this , \"Zona\");\"  name=\"zona\" id=\"mzona\" ><option  value=\"-1\">Todas las zonas</option><option id=\"Noroccidente\" value=\"Noroccidente\"selected=\"selected\" >Noroccidente (539) </option></select><div class=\"clearfix\"></div></div>",
				# "<div class=\"rb_contFiltro\"><h4>Sector</h4><ul class=\"rb_checkFiltro\" id=\"sector\" ><li ><i> 226 </i><a name=\"sector\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Sector\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Sector\" );\" >Colina y Alrededores</a></li><li ><i> 133 </i><a name=\"sector\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Sector\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Sector\" );\" >Altos de Suba y Cerros de San Jorge</a></li><li ><i> 110 </i><a name=\"sector\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Sector\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Sector\" );\" >Niza Alhambra</a></li><li ><i> 45 </i><a name=\"sector\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Sector\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Sector\" );\" >Cortijo-Autopista Medellín</a></li><li ><i> 25 </i><a name=\"sector\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Sector\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Sector\" );\" >170 y Alredores</a></li></ul></div>",
				# "<div class=\"rb_contFiltro\"><h4>Tipo inmueble</h4><select lang=\"es\"  onchange=\"procesarFiltroBarraLateral(this , \"Tipo inmueble\" , \"\" );enviarEventoOmniture(\"venta\", this , \"Tipo inmueble\");\"  name=\"tipoinmueble\" id=\"mtipoinmueble\" ><option  value=\"-1\">Todos los inmuebles</option><option id=\"Apartamento\" value=\"Apartamento\"selected=\"selected\" >Apartamento (539) </option></select><div class=\"clearfix\"></div></div>",
				# "<div class=\"rb_contFiltro\"><h4>Estado</h4><ul class=\"rb_radioFiltro\"><p>&#171; <a name=\"estadoinmueble\" href=\"javascript:void(0);\" onclick=\"eliminarFiltro(this , \"Estado\" , \"\");\" > Todas las opciones </a> </p></ul></div>",
				# "<div class=\"rb_contFiltro\"><h4>Precio venta (Millones)</h4><ul class=\"rb_radioFiltro\"><p>&#171; <a name=\"valorventarango\" href=\"javascript:void(0);\" onclick=\"eliminarFiltroRangosPrecios(this , \"Precio venta (Millones)\" , \"\");\" > Todos los precios </a> </p><form lang=\"es\" class=\"rb_sliderRange\" action=\"javascript:void(0);\" method=\"get\"><label class=\"rb_precioDesde\">Desde $<input type=\"text\" onkeyup=\"this.value=formatNumber( this.value )\" size=\"3\" value=\"0\" id=\"valorventarangoDesde\"></label><label class=\"rb_precioHasta\">Hasta $<input type=\"text\" onkeyup=\"this.value=formatNumber( this.value )\" size=\"3\" value=\"350\" id=\"valorventarangoHasta\"></label><button type=\"button\" onclick=\"procesarRangoAbierto(\"venta\" , \"valorventarango\" , \"\"  );s_objectID=\"precioventa\";\" title=\"Filtrar\">Ir</button></form></div>",
				# "<div class=\"rb_contFiltro\"><h4>Área m<sup>2</sup></h4><ul class=\"rb_radioFiltro\"><li class=\"\" name=\"arearango\" ><i>187</i><a name=\"arearango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroBarraLateral(this , \"Área m<sup>2</sup>\" , \"\"); enviarEventoOmniture(\"venta\" , this , \"Área m<sup>2</sup>\" );\" >Hasta 60</a></li><li class=\"\" name=\"arearango\" ><i>312</i><a name=\"arearango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroBarraLateral(this , \"Área m<sup>2</sup>\" , \"\"); enviarEventoOmniture(\"venta\" , this , \"Área m<sup>2</sup>\" );\" >60 a 100</a></li><li class=\"\" name=\"arearango\" ><i>40</i><a name=\"arearango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroBarraLateral(this , \"Área m<sup>2</sup>\" , \"\"); enviarEventoOmniture(\"venta\" , this , \"Área m<sup>2</sup>\" );\" >100 a 200</a></li></ul><form lang=\"es\" class=\"rb_sliderRange\" action=\"javascript:void(0);\" method=\"get\"><label class=\"\">De<input type=\"text\" onkeyup=\"this.value=formatNumber( this.value )\" size=\"3\" value=\"\" id=\"arearangoDesde\"></label><label class=\"\"> a <input type=\"text\" onkeyup=\"this.value=formatNumber( this.value )\" size=\"3\" value=\"\" id=\"arearangoHasta\"></label><button type=\"button\" onclick=\"procesarRangoAbierto(\"venta\" , \"arearango\" , \"\"  );s_objectID=\"aream2\";\" title=\"Filtrar\">Ir</button></form></div>",
				# "<div class=\"rb_contFiltro\"><h4>Habitaciones</h4><ul class=\"rb_checkFiltro\" id=\"nrocuartosrango\" ><li ><i> 11 </i><a name=\"nrocuartosrango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Habitaciones\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Habitaciones\" );\" >1 habitación</a></li><li ><i> 128 </i><a name=\"nrocuartosrango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Habitaciones\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Habitaciones\" );\" >2 habitaciones</a></li><li ><i> 389 </i><a name=\"nrocuartosrango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Habitaciones\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Habitaciones\" );\" >3 habitaciones</a></li><li ><i> 10 </i><a name=\"nrocuartosrango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Habitaciones\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Habitaciones\" );\" >4 habitaciones</a></li><li ><i> 1 </i><a name=\"nrocuartosrango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Habitaciones\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Habitaciones\" );\" >5 o más habitaciones</a></li></ul></div>",
				# "<div class=\"rb_contFiltro\"><h4>Baños</h4><ul class=\"rb_checkFiltro\" id=\"nrobanosrango\" ><li ><i> 120 </i><a name=\"nrobanosrango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Baños\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Baños\" );\" >1 baño</a></li><li ><i> 334 </i><a name=\"nrobanosrango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Baños\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Baños\" );\" >2 baños</a></li><li ><i> 78 </i><a name=\"nrobanosrango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Baños\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Baños\" );\" >3 baños</a></li><li ><i> 6 </i><a name=\"nrobanosrango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Baños\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Baños\" );\" >4 baños</a></li><li ><i> 1 </i><a name=\"nrobanosrango\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Baños\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Baños\" );\" >5 o más baños</a></li></ul></div>",
				# "<div class=\"rb_contFiltro\"><h4>Fotos</h4><ul class=\"rb_radioFiltro\"><li><i>490</i><a name=\"multimediafoto\" href=\"javascript:void(0);\" onclick=\"procesarFiltroBarraLateral(this , \"Fotos\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Fotos\" );\" >Con Fotos</a></li><li><i>49</i><a name=\"multimediafoto\" href=\"javascript:void(0);\" onclick=\"procesarFiltroBarraLateral(this , \"Fotos\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Fotos\" );\" >Sin Fotos</a></li></ul></div>",
				# "<div class=\"rb_contFiltro\"><h4>Muebles</h4><ul class=\"rb_radioFiltro\"><li><i>536</i><a name=\"conmuebles\" href=\"javascript:void(0);\" onclick=\"procesarFiltroBarraLateral(this , \"Muebles\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Muebles\" );\" >Sin Muebles</a></li><li><i>3</i><a name=\"conmuebles\" href=\"javascript:void(0);\" onclick=\"procesarFiltroBarraLateral(this , \"Muebles\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Muebles\" );\" >Con Muebles</a></li></ul></div>",
				# "<div class=\"rb_contFiltro\"><h4>Antigüedad</h4><ul class=\"rb_checkFiltro\" id=\"tiempoconstruido\" ><li ><i> 16 </i><a name=\"tiempoconstruido\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Antigüedad\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Antigüedad\" );\" >Sobre Plano</a></li><li ><i> 11 </i><a name=\"tiempoconstruido\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Antigüedad\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Antigüedad\" );\" >En Construcción</a></li><li ><i> 230 </i><a name=\"tiempoconstruido\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Antigüedad\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Antigüedad\" );\" >Para Estrenar</a></li><li ><i> 52 </i><a name=\"tiempoconstruido\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Antigüedad\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Antigüedad\" );\" >Remodelado</a></li><li ><i> 638 </i><a name=\"tiempoconstruido\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Antigüedad\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Antigüedad\" );\" >Entre 0 y 5 años</a></li><li ><i> 366 </i><a name=\"tiempoconstruido\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Antigüedad\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Antigüedad\" );\" >Entre 5 y 10 años</a></li><li  class=\"rb_checkActivo\" ><i> 539 </i><a name=\"tiempoconstruido\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Antigüedad\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Antigüedad\" );\" >Entre 10 y 20 años</a></li><li ><i> 261 </i><a name=\"tiempoconstruido\" href=\"javascript:void(0);\" onclick=\"procesarFiltroCheck(this ,  \"Antigüedad\" , \"\");enviarEventoOmniture(\"venta\" , this , \"Antigüedad\" );\" >Más de 20 años</a></li></ul></div>"
			  # ],

		
 		if(out == "JSON")
 			json_hash = Hash.new
			#json_hash["object"]=nil
			#json_hash["token"]= ""	
			#json_hash["status"] = "SUCCESS"
			#json_hash["seccionPautaDinamica"]= nil	
			#json_hash["esPrimeraPagina"] = true
			json_hash["mostrandoHasta"] = prop+1
			#json_hash["numeroUltimoPaginador"] = "2"
			#json_hash["numeroPrimerPaginador"] = "1"
			#json_hash["esBusquedaAlternativa"] = false
			#json_hash["numeroPaginaActual"] = "1"
			#json_hash["esUltimaPagina"] = false
			#json_hash["mostrarOpcionesVenta"] = true
			#json_hash["numeroPaginas"] = "2"
			#json_hash["tituloResultados"] = " Apartamentos en Venta en Bogota, Estado: Usado, Precio Venta (millones): 0 A 350.000.000, Antigüedad: "+tiempo
			#json_hash["breadcrumbHTML"] = "\t<ul id=\"horiList\" itemprop=\"breadcrumb\">\t\t<li class=\"start\"><a target=\"_self\" itemprop=\"breadcrumb\" title=\"Inicio\" href=\"/\">Inicio</a></li>\t\t<li><a title=\"Buscar\" itemprop=\"breadcrumb\" href=\"/\">Buscar</a></li>\t\t<li><a href=\"/apartamentos/\"  itemprop=\"breadcrumb\" >Apartamentos</a></li>\t\t<li><a href=\"/apartamentos/venta/\"  itemprop=\"breadcrumb\" >Venta</a></li>\t\t<li><a href=\"/apartamentos/venta/bogota/\"  itemprop=\"breadcrumb\" >Bogota</a></li>\t\t<li><a href=\"/apartamentos/venta/bogota/usados\"  itemprop=\"breadcrumb\" >Usados</a></li>\t</ul>"
			#json_hash["seccionDestacados"] = "resultados;idtipoinmueble=apartamento;idtiponegocio=venta;idciudad=bogotadc;idzona=noroccidente;"
			#json_hash["mostrandoDesde"] = "1"
			#json_hash["numeroResultadosPorPagina"] = "500"
			json_hash["numeroResultados"] = totalinmuebles							
			json_hash["inmueblesHTML"] = inmueblesHTML
			
 			puts JSON.pretty_generate(json_hash)
 		end
		if((out == "TEXT") && (prop == 0))
			puts "No se encontraron resultados ...<br><br>"
  		end	

	end
end
*/

?>