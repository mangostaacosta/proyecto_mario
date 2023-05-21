<?php
  /****************************************************************************
  20141012  >> funciones.php
  Proyecto: GENERICO
  La idea es que contiene las funciones generales que son útiles en cualquier aplicativo    
  ******************************************************************************/
  
   function msj( $texto ){
    // anexa texto a $g_html
    global $g_html ;
    global $g_depura ;
    
    if ( $g_depura == 1 ){
      $g_html .= $texto ;
      echo "$texto<br>" ;
    }    
   }
	
	function msjP( $texto ){
		// texto en version codigo
		global $g_html ;
		global $g_depura ;
		
		msj("<pre>$texto</pre>") ;		
	}
	
	function MsjE( $texto ){    
		echo "OJO: $texto<br>" ;		
  }
  
  function ver_arr( $arreglo , $nombre = "Arreglo" ){
    // anexa texto a $g_html
    global $g_html ;
    global $g_depura ;
    
    if ( $g_depura == 1 ){
      echo "<pre>$nombre \n" ;
      print_r( $arreglo );
      echo "</pre>" ;      
    }    
  }


	//20160126: se modifica esta función para que también procese guiones
	//20160217: se elimina el ajuste anterior de los guiones
	function CodificaRaros( $texto ){		
		//$search = array( 'á' , 'é' , 'í' , 'ó' , 'ú' , 'ñ' , '-' ) ;
		//$replace = array ( 'a' , 'e' , 'i' , 'o' , 'u' , 'n' , ' ' ) ;
		$search = array( 'á' , 'é' , 'í' , 'ó' , 'ú' , 'ñ' ) ;
		$replace = array ( 'a' , 'e' , 'i' , 'o' , 'u' , 'n' ) ;
		$texto = str_replace($search,$replace,$texto) ;
		return $texto ;	
	}
	
	function PrecioADecimal( $texto , $punto_miles = '.' ){		
		//msj ("en precio a decimal: $texto ") ;
		$replace = array ('') ;
		$search = array( '$' , $punto_miles ) ;
		//$texto = str_replace($search,$replace,$texto) ;		
		$texto = str_replace($search,$replace,$texto) ;
		return $texto ;
	}
	
	function show_html( $texto ){
    // anexa texto a $g_html
		global $g_html ;
    
		$g_html .= $texto ;        
	}
	//cambio 
  
  function href_menu( $pregunta = 0 , $indice = "index.php" , $f_tema = 'NN' ){
		if ( $f_tema === 'NN' ) {
			$texto = "$indice?f_preg=$pregunta&f_destino=$indice" ;
		}else{
			$texto = "$indice?f_preg=$pregunta&f_tema=$f_tema&f_destino=$indice" ;
		}
    
    return $texto ; 
  }
  
  function href_menu1( $pregunta = 0 , $indice = "index.php" ){
    $texto = "$indice?f_grupo=$pregunta&f_destino=$indice" ;
    return $texto ; 
  }
  
  function html_arreglo_bi( $arreglo , $keys = 0 , $titulos = array() , $encabezado = "" , $ancho = 500 ){
  // imprime un arreglo bidimensional con la primera fila de $titulos. 
  //si $keys = 1 los titulos son las llaves del arreglos , si 2 el arreglo de titulos, si 0 nada
  
    
    $texto_html = "<div class=\"center\"><table border=0 width=$ancho>" ;
    
    if ( $encabezado != "" ){
      $cols = sizeof(current($arreglo)) ;
      $texto_html .= "<tr class='h'><th colspan='$cols'>$encabezado</th></tr>" ;
    }
        
    if ( $keys == 1 ){    
      $texto_html .= "<tr class='h'>" ;
      reset( $arreglo ) ;
      foreach ( current($arreglo) as $key=>$val ){
        $texto_html .= "<th>$key</th>" ;
      }
      $texto_html .= "</tr>" ;
    }elseif ( $keys == 2 ){
      $texto_html .= "<tr class='h'>" ;
      foreach ( $titulos as $key=>$val ){
        $texto_html .= "<th>$val</th>" ;
      }
      $texto_html .= "</tr>" ;
    }else{  //si es 0 o cualquier otra cosa sin títulos
      //nada
    }    
    $texto_html .= "\n" ;
    
    $x = 1 ;
    foreach ( $arreglo as $key=>$linea ){  //Atención el arreglo debe ser bidimensional
      $x = -$x ;
      if ( $x == 1 ){ //cambio de color
        $texto_html .= "<tr class='e'>" ;        
      }else{
        $texto_html .= "<tr class='v'>" ;
      }      
      foreach ( $linea as $val ){
				//$val = formato_n($val) ;
        $texto_html .= "<td>$val</td>" ;
      }
      $texto_html .= "</tr>\n" ;
    }
    $texto_html .= "</table></div>" ;
	
		return $texto_html ;
	
  }
	
	
	function html_arreglo_uni( $arreglo , $keys = 0 , $titulos = array() , $encabezado = "" , $ancho = 300 ){
	// imprime un arreglo unidimensional
	//si $keys = 1 los titulos son las llaves del arreglos , si 2 el arreglo de titulos, si 0 nada       
    
		if ( $keys == 1 ){
			foreach ( $arreglo as $key => $val ){
				$arr_pinta[] = array( $key , $val ) ;				
			}	
		}
		if ( $keys == 2 ){
			foreach ( $arreglo as $key => $val ){
				$arr_pinta[] = array ( $titulos[$key] , $val ) ;				
			}	
		}
		
		$n = array() ;
		if ( $keys == 0 ){
			//Lo hago en algún momento que lo necesite	
		}else{
			$texto_html = html_arreglo_bi( $arr_pinta , 0 , $n , $encabezado , $ancho ) ;
		}
		return $texto_html ;
	}	
	
	
  function impri_arre_tabla_string( $arreglo ,  $prearreglo , $nivel = 0 ){
  //imprime un arreglo multidimensional en formato de tabla html, incluye las llaes como columnas con valores repetidos en la tabla
  //es decir es como si el título estuivera al lado y no arriba de cada serie de datos
    $listo = 1 ;
    //echo "entre" ;
    if ( $nivel == 0 ){
      echo ("<table border=1>") ;
    }
    
    foreach ( $arreglo as $val ){
      if ( is_array( $val ) ){
        $listo = 0 ;
      }
    }
    
    if ( $listo == 1 ) {      
      echo("<tr>") ;
      foreach ( $prearreglo as $val ){        
        echo ("<td>$val</td>") ;
      }
      
      foreach ( $arreglo as $key=>$val ){
        echo("<td>$key</td><td>$val</td>") ;        
      }      
      echo("</tr>") ;      
    }else{
      foreach ( $arreglo as $key=>$val ){
        $arrtemp = array_merge( (array)$prearreglo , (array)$key ) ;        
        impri_arre_tabla_string( $val , $arrtemp , $nivel + 1  ) ;
      }
    }
    if ( $nivel == 0 ){
      echo("</table>") ;      
    }
  }
  
  function impri_arre_tabla( $arreglo ,  $prearreglo , $nivel = 0 ){
  //imprime un arreglo multidimensional en formato de tabla html, incluye las llaes como columnas con valores repetidos en la tabla
  //es decir es como si el título estuivera al lado y no arriba de cada serie de datos
  //se ve exacta a la anterior
  
  $listo = 1 ;
    //echo "entre" ;
    if ( $nivel == 0 ){
      echo ("<table border=1>") ;
    }
    
    foreach ( $arreglo as $val ){
      if ( is_array( $val ) ){
        $listo = 0 ;
      }
    }
    
    if ( $listo == 1 ) {      
      echo("<tr>") ;
      foreach ( $prearreglo as $val ){        
        echo ("<td>$val</td>") ;
      }
      
      foreach ( $arreglo as $key=>$val ){
        echo("<td>$key</td><td>$val</td>") ;        
      }      
      echo("</tr>") ;      
    }else{
      foreach ( $arreglo as $key=>$val ){
        $arrtemp = array_merge( (array)$prearreglo , (array)$key ) ;        
        impri_arre_tabla( $val , $arrtemp , $nivel + 1  ) ;
      }
    }
    if ( $nivel == 0 ){
      echo("</table>") ;      
    }
  }
  
  function impri_arre_tabla_sin( $arreglo ,  $prearreglo , $nivel = 0 ){    
  //Lo pone en formato de tabla en HTML
  //imprime un arreglo sin colocar el último nivel de KEYS 
  //en la práctica esto mejora las funciones de graficar arreglos multinivel, ya que no se incluyen los títulos del último nivel de arreglo en la fila de datos
  //por lo que no se crean columnas llenas de información replicada. El asunto es que se "pierden" los títulos a no ser que vengan en otro arreglo
  
    
    $listo = 1 ;
    //echo "entre" ;
    if ( $nivel == 0 ){ // Si hay titulos en la posición 0 se imprimen
      echo("<table border=1><tr>") ;	  
	  //reset( $arreglo ) ;
      //foreach ( current($arreglo) as $key=>$val ){	  
	  if ( !isset( $arreglo[0] )){
		//nada
	  }else{
		foreach ( $arreglo[0] as $key=>$val ){
			echo("<td>$val</td>") ;
		}
	  }      
      echo("</tr>") ;      
    }
    
        
    foreach ( $arreglo as $val ){
      if ( is_array( $val ) ){
        $listo = 0 ;
      }
    }
    
    if ( $listo == 1 ) {     
      echo("<tr>") ;
      foreach ( $prearreglo as $val ){       
        echo("<td>$val</td>") ;
      }
      
      foreach ( $arreglo as $key=>$val ){        
        echo("<td>$val</td>") ;
      }
      echo("</tr>") ;
    }else{
      foreach ( $arreglo as $key=>$val ){
		//if ( ($nivel == 0) and ($key == 0) ){
		//  msj('alo') ;
		//  continue ;
		//}
        $arrtemp = array_merge( (array)$prearreglo , (array)$key ) ;        
        impri_arre_tabla_sin( $val , $arrtemp , $nivel + 1  ) ;
      }
    }
    if ( $nivel == 0 ){      
      echo("</table>") ;
    }
  }
  
  
  
  function impri_arre_titulo_sin( $arreglo ,  $prearreglo , $nivel = 0 ){
    $listo = 1 ;
    //echo "entre" ;
    if ( $nivel == 0 ){
      echo "<table border=1>" ;
    }
    
    foreach ( $arreglo as $val ){
      if ( is_array( $val ) ){
        $listo = 0 ;
      }
    }
    
    if ( $listo == 1 ) {
      echo "<tr>" ;
      foreach ( $prearreglo as $val ){
        echo "<td>$val</td>" ;
      }
      
      foreach ( $arreglo as $key=>$val ){
        echo "<td>$key</td>" ;
      }      
      
      echo "</tr>" ;
    }else{
      $k = 0 ;
      foreach ( $arreglo as $key=>$val ){
		//if ( $nivel == 0 and $key == 0 ){
		//  msj('alo') ;
		//  continue ;
		//}
        if ( $k == 1 ){
          continue ;
        }
        $arrtemp = array_merge( (array)$prearreglo , (array)$key ) ;        
        impri_arre_titulo_sin( $val , $arrtemp , $nivel + 1  ) ;
        $k = 1 ;
      }
    }
    if ( $nivel == 0 ){
      echo "</table>" ;
    }
  }
	
	
	
	
	
	
	function formato_n( $strnumero , $ndec = 2 ){
		if ( !isset ($strnumero)){
			return '' ;
		}elseif (is_numeric($strnumero)){
			return number_format($strnumero, $ndec, '.', ',') ;		
		}else{
			return $strnumero ;
		}
	}
	
	
  function sql_a_graf( $agrup , $arr_graf , $arr_categ ){
    
    foreach ($arr_categ as $val1 ){
      $arr_ceros[$val1] = 0 ;
    }
    $titus = "<XX--XX>" ;
    foreach ( $arr_graf[$agrup] as $key => $val ){
      if ( $val[0] != $titus ){
        $arr_titus[] =  $val[0] ;
        $titus = $val[0] ;        
        $arr_valores[$titus] = $arr_ceros ;
        //$position = count( $arr_valores ) - 1 ;
      }
      $categ = $val[1] ;
      $arr_valores[$titus][$categ] = $val[2] ;         
    }    
        
    foreach ( $arr_valores as $key => $val ){
      $total = 0 ;
      foreach ( $val as $key1 => $val1 ){
        $total += $val1 ;
      }      
      $arr_totales[$key] = $total ; 
    }
    
    foreach ( $arr_valores as $key => $val ){
      foreach( $val as $key1 => $val1 ){
        $arr_cosito[$key1][$key] = $val1 ;
      }
    }
    //ver_arr($arr_cosito) ;
    //20141027: Para corregir masivamente todos los NAs a NA/NR 
		msj("en sql_a_graf") ;
    foreach ( $arr_cosito as $key => $val ){
      if ( !($key === 'NA') and !($key === '') ){		//20141201: Nuevo condicional para ''
        $arr_cosito2[$key] = $val ;
      }else{
        $arr_cosito2['NA/NR'] = $val ;
				msj( "Entré al arreglador de NAs, el key es: $key") ;
      }       
    }
    
    $arr_salida['titulos'] = $arr_titus ;
    $arr_salida['valores'] = $arr_cosito2 ;
    $arr_salida['totales'] = $arr_totales ;
    
    
        
    return $arr_salida ;
  }
  
  function sql_a_graf_prom( $agrup , $arr_graf ){    
    //$titus = "" ;
    $titus = "<XX--XX>" ;  // 20141026 esto logra que el título "" sea graficable aunque no debería suceder pero se mete para evitar grafs nanranjas
    foreach ( $arr_graf[$agrup] as $key => $val ){
      if ( $val[0] != $titus ){
        $titus = $val[0] ;
        $arr_titus[] =  $titus ;
        $arr_valores['Promedio'][$titus] = 0 ;
      }      
      $arr_valores['Promedio'][$titus] = $val[2] ;
      $arr_totales[$titus] = $val[3] ;
    }    
    $arr_salida['titulos'] = $arr_titus ;
    $arr_salida['valores'] = $arr_valores ;
    $arr_salida['totales'] = $arr_totales ;    
    return $arr_salida ;
  }  
 
  
  function java_grafica( $unique = 1 , $cual = "esp" ){
    global $g_html ;
    global $dirxml ;
    
    $texto = "
<script language=\"JavaScript\" type=\"text/javascript\">
<!--
if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	alert('This page requires AC_RunActiveContent.js.');
} else {
	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if(hasRightVersion) { 
		AC_FL_RunContent(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,45,2',
			'width', '660',
			'height', '400',
			'scale', 'noscale',
			'salign', 'TL',
			'bgcolor', '#FFFFFF',
			'wmode', 'opaque',
			'movie', 'charts',
			'src', 'charts',
			'FlashVars', 'library_path=charts_library&xml_source=$dirxml/sample_$cual.xml?unique_id=$unique', 
			'id', 'my_chart',
			'name', 'my_chart',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'quality', 'high',
			'align', 'middle',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'play', 'true',
			'devicefont', 'false'
			); 
	} else { 
		var alternateContent = 'This content requires the Adobe Flash Player. '
		+ '<u><a href=http://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
		document.write(alternateContent); 
	}
}
// -->
</script>
<noscript>
	<P>This content requires JavaScript.</P>
</noscript>    
    " ;
    $g_html .= $texto ;
  }
  
  /// modifica java_grafica para que retorne el texto en vez de pegarlo al html global
  
    function java_grafica_ret( $unique = 1 , $cual = "esp" , $fils = 17 , $ancho = 700 ){
      global $g_html ;
      global $dirxml ;
      
      $alt = 180 + 27*$fils ;
      if ( $cual == 'todo' and $fils == 17 ){
        $alt = 200 ;
      }    
      $texto = "  
<script language=\"JavaScript\" type=\"text/javascript\">
<!--
if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	alert('This page requires AC_RunActiveContent.js.');
} else {
	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if(hasRightVersion) { 
		AC_FL_RunContent(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,45,2',
			'width', '$ancho',
			'height', '$alt',
			'scale', 'noscale',
			'salign', 'TL',
			'bgcolor', '#EEEEEE',
			'wmode', 'opaque',
			'movie', 'charts',
			'src', 'charts',
			'FlashVars', 'library_path=charts_library&xml_source=$dirxml/sample_$cual.xml?unique_id=$unique', 
			'id', 'my_chart',
			'name', 'my_chart',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'quality', 'high',
			'align', 'middle',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'play', 'true',
			'devicefont', 'false'
			); 
	} else { 
		var alternateContent = 'This content requires the Adobe Flash Player. '
		+ '<u><a href=http://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
		document.write(alternateContent); 
	}
}
// -->
</script>
<noscript>
	<P>This content requires JavaScript.</P>
</noscript>    
    <br><br>" ;
       
    return($texto) ;
  }
  
    function java_grafica1( $unique = 1 , $cual = "esp" , $fils = 17 ){
      global $g_html ;
      global $dirxml ;
     //cambio de color espero linea 168. sample_G_4 linea 172
     
      $alt = 120 + 45*$fils ;
      $texto = "
<script language=\"JavaScript\" type=\"text/javascript\">
<!--
if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	alert('This page requires AC_RunActiveContent.js.');
} else {
	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if(hasRightVersion) { 
		AC_FL_RunContent(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,45,2',
			'width', '700',
			'height', '$alt',
			'scale', 'noscale',
			'salign', 'TL',
			'bgcolor', '#FFFFFF',  
			'wmode', 'opaque',
			'movie', 'charts',
			'src', 'charts',
			'FlashVars', 'library_path=charts_library&xml_source=$dirxml/sample_$cual.xml?unique_id=$unique', 
			'id', 'my_chart',
			'name', 'my_chart',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'quality', 'high',
			'align', 'middle',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'play', 'true',
			'devicefont', 'false'
			); 
	} else { 
		var alternateContent = 'This content requires the Adobe Flash Player. '
		+ '<u><a href=http://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
		document.write(alternateContent); 
	}
}
// -->
</script>
<noscript>
	<P>This content requires JavaScript.</P>
</noscript>    
    " ;
    return ( $texto ) ;
//    $g_html .= $texto ;
  }
  
  
  
  
  
  function pinta_uni( $arr_paraxml , $cual = "esp" , $sufi = "" ){
    global $g_xmlhead3 ;
    global $dirxml ;
    
    
//    $handle = fopen("sample_G_XX.xml", "w");
    $handle = fopen("$dirxml/sample_$cual.xml", "w");      
    $xml = $g_xmlhead3 ;
    
    $max = 0 ;
    foreach ($arr_paraxml['valores'] as $key => $val ){ 
      foreach ( $val as $key1 => $val1 ){
        $max = ( $max < $val1 ? $val1 : $max ) ;
      }
    }
    
    $max *= 1.2 ;    
    $max = round($max/10) * 10 ;    
    $xml .= "<axis_value max='$max' steps='5' size ='11' color='000088' suffix = '$sufi' />" ;
          
    $xml .= "<chart_data> \n  <row> \n <null/> \n" ;      
    foreach ($arr_paraxml['titulos'] as $val ){
      $texto = corta_texto( $val ) ;
      $xml .= "<string>$texto</string> \n" ;
    }      
    end( $arr_paraxml['valores'] );
    $last = key ( $arr_paraxml['valores'] ) ;
    $filas = 0 ;      
    foreach ($arr_paraxml['valores'] as $key => $val ){
      $xml .= "</row><row> \n" ;
      $xml .= "<string>$key</string> \n" ;
      if ( $key != $last ){
        foreach ( $val as $key1 => $val1 ){
          $xml .= "<number>$val1</number> \n" ;
        }
      }else{                                              //es el último elemento necesita la "n"
        foreach ( $val as $key1 => $val1 ){
          $note = $arr_paraxml['totales'][$key1] ;
          if ( $note != "" ){
            $xml .= "<number note = 'n=$note'>$val1</number> \n" ;
          }else{
            $xml .= "<number note = '$note'>$val1</number> \n" ;
          }
          $filas++ ; 
        }
      }       
    }
    $xml .= "</row> \n </chart_data> \n </chart>" ;      
    fwrite( $handle , $xml ) ;
    fclose($handle);
        
    $texto = java_grafica1(rand(0,100) , $cual , $filas ) ;
    return $texto ;    
  }
  
  function datos_xml( $arr_paraxml ){
  
    $arr_orden = $arr_paraxml['totales'] ;
    asort($arr_orden);
    
    
    $xml = "<chart_data>\n <row> \n <null/> \n" ;  //20101023: OJO esta linea siempre hay que arreglarla 
    
    /*
    foreach ($arr_paraxml['titulos'] as $val ){
      $texto = corta_texto( $val ) ;
      $xml .= "<string>$texto</string> \n" ;
    }*/
    foreach ($arr_orden as $key => $val ){
      $texto = corta_texto( $key ) ;
      $xml .= "<string>$texto</string> \n" ;
    }
    
    end( $arr_paraxml['valores'] );
    $last = key ( $arr_paraxml['valores'] ) ;
    
    //$filas = 0 ;
    foreach ($arr_paraxml['valores'] as $key => $val ){      
      $xml .= "</row><row> \n" ;
      $texto = $key ;
      $xml .= "<string>$texto</string> \n" ;
      if ( $key != $last ){
        foreach ( $arr_orden as $key0 => $val0 ){
          $val1 = $val[$key0] ;
        //foreach ( $val as $key1 => $val1 ){
          $xml .= "<number>$val1</number> \n" ;
        }
      }else{                                              //es el último elemento necesita la "n"
        //foreach ( $val as $key1 => $val1 ){
        foreach ( $arr_orden as $key0 => $val0 ){
          $val1 = $val[$key0] ;
          $note = $arr_paraxml['totales'][$key0] ;
          $xml .= "<number note = 'n=$note'>$val1</number> \n" ;
          //$filas += 1 ;
        }
      }
    }
    $xml .= "</row> \n </chart_data> \n </chart>" ;
    
    return $xml ; 
  
  }
  
  function busca_idg( $grupo , $arr_menu ){
    foreach ( $arr_menu as $key => $val ){
      if ( $grupo == $val['3'] ){
        return ($key) ;
      }
    }
    return (0) ;
  }

  function corta_texto( $linea ){ 
  //OJO esta función sólo e segura en codificaciones de 256 bits, es decir puede fallar sobre UTF-8 
    $texto = ( $linea ) ;
    
    if ( strlen( $texto ) > 70 ){
      $texto = substr( $texto , 0, 68 ) . '...' ;
    }
    $total = min(count( $texto ) , 44 ) ;
    if ( strlen( $texto ) > 35 ){
      for ( $i = 34 ; $i < $total ; $i++ ){
        if ( $texto[$i] == ' ' ){
          break ;
        }
      }
      if ( $texto[$i] == ' ' ){
        msj('inserción en espacio') ;
        $texto[$i] = "\r" ;
      }else{
        $texto1 = substr( $texto , 0 , $i ) ;
        $texto2 = substr( $texto , $i ) ;
        $texto = "$texto1\r$texto2" ;
      }
    }
    
    return ( $texto ) ;
  }
  
	function limpia_sql( $texto ){
		$texto = trim( $texto ) ;
		$texto = mysql_real_escape_string( $texto ) ;
		return "'$texto'" ;
	}
	
	function limpia_dat( $texto ){
		$texto = trim( $texto ) ;
		$texto = mysql_real_escape_string( $texto ) ;
		return "$texto" ;
	}
	
	function null_sql( $texto ){
		if ( $texto == "''" ){
			return 'NULL' ;
		}else{
			return $texto ;
		}
	}
 
?>
