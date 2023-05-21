require 'rubygems'
require 'open-uri'
require 'json'

def get_info(url,out,max,tiempo)
	begin
	
		encoded_url = URI.encode(url)
		parsed_url  = URI.parse(encoded_url)
	
		web_contents  = open(encoded_url+"&irA=1") {|f| f.read }
		
		json_web = JSON.pretty_generate(JSON.load(web_contents))
		hash = JSON.parse(json_web)
		
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


def inmueble_txt(inmueble)

					zona= 			inmueble["Zona"].to_s
					inmobiliaria=	inmueble["LogindId"].to_s
					img_inmobiliaria= inmueble["LogoResultado"].to_s
					title=			inmueble["TipoNegocio"].to_s+" de "+inmueble["TipoInmueble"].to_s+" en "+inmueble["NombreComunBarrio"].to_s+" - "+inmueble["Ciudad"].to_s
					url_inmueble=	"http://www.metrocuadrado.com"+inmueble["UrlDetalle"].to_s
					img_inmueble=	inmueble["srcImagen"].to_s
					precio=			inmueble["ValorNegocio"].to_s
					currency=		"COP"
					area=			inmueble["Area"].to_s
					habitaciones=	inmueble["Cuartos"].to_s
					banos=			inmueble["Banos"].to_s
					descripcion=	inmueble["ComentarioPublicacion"].to_s
					itemOffered=	inmueble["TipoInmueble"].to_s+" en "+inmueble["TipoNegocio"].to_s
					location=		inmueble["NombreComunBarrio"].to_s+" - "+inmueble["Ciudad"].to_s

					availability=	""

					puts "Zona: "+zona.to_s+"<br>"	
					#puts "Inmobiliaria: "+inmobiliaria+"<br>"		
					#puts "Img Inmobiliaria: "+img_inmobiliaria+"<br>"	
					puts "Title: "+title+"<br>"	
					puts "URL Inmueble: "+url_inmueble+"<br>"	
					#puts "Imagen Inmueble: "+img_inmueble+"<br>"
					puts "Precio: "+precio+"<br>"	
					puts "Moneda: "+currency+"<br>"	
					puts "Area: "+area+"<br>"		
					puts "Habitaciones: "+habitaciones+"<br>"
					puts "Banos: "+banos+"<br>"
					puts "Descripcion: "+descripcion+"<br>"
					puts "itemOffered: "+itemOffered+"<br>"
					puts "location: "+location+"<br>"				
	

					puts "<br><br><br>"
					
end

def inmueble_html(inmueble)

					zona= 			inmueble["Zona"].to_s
					inmobiliaria=	inmueble["LogindId"].to_s
					img_inmobiliaria= inmueble["LogoResultado"].to_s
					title=			inmueble["TipoNegocio"].to_s+" de "+inmueble["TipoInmueble"].to_s+" en "+inmueble["NombreComunBarrio"].to_s+" - "+inmueble["Ciudad"].to_s
					url_inmueble=	"http://www.metrocuadrado.com"+inmueble["UrlDetalle"].to_s
					img_inmueble=	inmueble["srcImagen"].to_s
					precio=			inmueble["ValorNegocio"].to_s
					currency=		"COP"
					area=			inmueble["Area"].to_s
					habitaciones=	inmueble["Cuartos"].to_s
					banos=			inmueble["Banos"].to_s
					descripcion=	inmueble["ComentarioPublicacion"].to_s
					itemOffered=	inmueble["TipoInmueble"].to_s+" en "+inmueble["TipoNegocio"].to_s
					location=		inmueble["NombreComunBarrio"].to_s+" - "+inmueble["Ciudad"].to_s

					availability=	""
					
					inmuebleHTML =
'<dl class="hlisting" itemtype="http://schema.org/Offer" itemscope="" name="#">
<div class="propertyInfo item">
<dd class="logoProy" itemprop="seller" itemscope="" itemtype="http://schema.org/Organization">
<span itemprop="name">'+inmobiliaria+'</span>
<a  target="_blank">
<img src="'+img_inmobiliaria.to_s+'" 
itemprop="logo" 
title="'+title+' - '+inmobiliaria+'" 
alt="'+title+' - '+inmobiliaria+'" 
/>
</a>
</dd>
<a href="'+url_inmueble.to_s+'" 
target="_blank" itemprop="url">        
<dd class="fotoAviso propertyThumb" style="cursor: pointer;" itemprop="image">
<img src="'+img_inmueble+'" 
itemprop="image"  title="'+title.to_s+'" alt="'+title.to_s+'" 
onerror="reemplazarImagen(this, "'+img_inmueble+'");"/>
</dd>
</a>
<a href="'+url_inmueble.to_s+'" 
target="_blank" itemprop="url">
<dt>
<h2 itemprop="name">
<span itemprop="itemOffered">'+itemOffered+'</span>
<span class="location">, '+location.to_s+'</span>
</h2>
</dt>
<dd class="precioAviso"> 
<i itemprop="price">'+precio.to_s+'<meta itemprop="priceCurrency" content="'+currency+'" />
</i><br /> 
<span itemprop="availability">'+availability.to_s+'</span>
</dd>
</a>
<div class="masInfo">
<div class="capacidadPerso" itemprop="itemOffered" itemscope itemtype="http://schema.org/Product">'+area.to_s+' m<sup>2</sup> 
<b title="&Aacute;rea Privada">&Aacute;. Priv</b> 
<span>|</span> '+habitaciones.to_s+' habitaciones<span>|</span> '+banos.to_s+' ba&ntilde;os<!--[if lt IE 8]><div class="clearfix"></div><![endif]-->
</div>
<div class="Publicadohace">Publicado <b>Hoy</b> <br />
<a href="javascript:void(0);" class="linkViewDescription">+ Descripci&oacute;n</a>
</div>
<div class="clear">
</div></div>
<div class="clear"></div>
<p class="descripcionInmueble" itemprop="description">'+descripcion.to_s+'</p> 
</div>
</dl>
'	
					return inmuebleHTML


end

		if ARGV.empty?
		 puts "Faltan Parametros"
		else
			ciudad = ARGV.shift
			tipo = ARGV.shift
			ca = ARGV.shift
			pd = ARGV.shift
			ph = ARGV.shift
			out = ARGV.shift
			zona = ARGV.shift
			max = ARGV.shift
			estado = ARGV.shift
			tiempo = ARGV.shift

			#http://www.metrocuadrado.com/servlet/co.com.m2.servlet.ajax.MostrarInmueblesFastJson?mvalorventarango=40%20a%2070%20millones&addfiltro=mvalorventarango&remove=mvalorventa&requestXML=1&traeresultados=1
			#mtipoinmueble=Oficina
			#mvalorventarango=70%20a%20100%20millones
			#mvalorarriendo=300000;900000
			
			if(ca == "Compra")
				if((pd == "0" ) && (ph=="0"))
					stringprecios = "&mvalorventarango=0"
				else
					stringprecios = "&mvalorventa="+pd+";"+ph
				end		
			end
			
			if(ca == "Arriendo")
				if((pd == "0" ) && (ph=="0"))
					stringprecios = "&mvalorarriendorango=0"
				else
					stringprecios = "&mvalorarriendo="+pd+";"+ph
				end	
			end			
			
			
			if(zona!="Todas")
				url="http://www.metrocuadrado.com/servlet/co.com.m2.servlet.ajax.MostrarInmueblesFastJson?requestXML=1&mciudad="+ciudad+"&mtipoinmueble="+tipo+stringprecios+"&mzona="+zona
			else
				url="http://www.metrocuadrado.com/servlet/co.com.m2.servlet.ajax.MostrarInmueblesFastJson?requestXML=1&mciudad="+ciudad+"&mtipoinmueble="+tipo+stringprecios
			end		
			
			url=url+"&mestadoinmueble="+estado
			
			if(estado=="Nuevo")
				url=url+"&mtiempoconstruido=En Construcción,Sobre Plano,Para Estrenar"
			end
			
			if(tiempo!="Todos")
				url=url+"&mtiempoconstruido="+tiempo
			end
				
			get_info(url,out,max,tiempo)
end