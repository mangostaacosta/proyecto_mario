<?php
/****************************************************************************
  20150120  >> tabla_barrios03.php
  Proyecto: Obsevatorio Mario
  Realiza un filtro sobre los resultados de indicadores grupo y un reordeordenamiento
  para presentar unicamente los barrios más relevantes de seguimiento. Calula indicadores adicionales
  por diferencia contra nuevo y ordena acorde a este nuevo criterio.
  Además trae los datos de t_mc y los presenta al lado
  PRE: 
******************************************************************************/

	require_once 'header.php' ; 
	require_once 'indicadores_grupo.php' ; 
	
	$manual['tex'] = 'Esta opción muestra la información de los barrios ordenados según un criterio de relevancia de valor relativo <b>valor_rel</b>. Los valores se complementan adicionalmente con los datos de las tablas estadarizadas de metrocuadrado.com con las siguientes columnas:
	<b>mc4_2a8:</b> Valor en las tablas estandarizadas en metrocuadrado.com para el estrato 4 en el rango de antigüedad de 2 a 8 años. 
	<b>mc5_2a8:</b> Valor en las tablas estandarizadas en metrocuadrado.com para el estrato 5 en el rango de antigüedad de 2 a 8 años. 
	<b>mc6_2a8:</b> Valor en las tablas estandarizadas en metrocuadrado.com para el estrato 6 en el rango de antigüedad de 2 a 8 años. 
	
	Adicionalmente se incluyen las columnas:
	<b>val_new:</b> Valor del precio de m2 de referencia (es decir el valor por metrocuadrado más alto de mercado para el barrio correspondiente).  
	<b>valor_rel:</b> Presenta el valor del indicador de valor relativo. A menor valor el diferencial es mayor y por tanto existe un potencial de mayor valor. 
	La información incluye únicamente los inmuebles que están ubicados en barrios con datos mínimos para hacer comparaciones en la fecha escogida. 
	Entre parentésis redondos se presenta la cantidad de datos disponibles para calcular el valor promedio; a mayor cantidad de datos mayor confianza.
	Entre paréntésis cuadrados se presenta la diferencia del promedio calculado frente al vaor de referencia señalado en la columna <b>val_new</b>; a mayor valor mayor diferencial.
	En la parte inferior se podrá encontrar una explicación detallada de las columnas de la tabla.
	' . $manual['tex'] ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Operación > Tablas > Comparativo MC' ) ;
	
	//Primero traigo los datos de la MD t_mc para agregarlos a arr_salida
	//SOLO ESTOY INCLUYENDO EL VALOR DE 2 A 8 dado el descubrimiento que los demas valores son decrementos porcentuales iguales
	$arr_pintar['mc4_2a8'] = '' ;
	//$arr_pintar['mc4_9a15'] = '' ;
	//$arr_pintar['mc4_16a30'] = '' ;
	//$arr_pintar['mc4_31a'] = '' ;
	
	$arr_pintar['mc5_2a8'] = '' ;
	//$arr_pintar['mc5_9a15'] = '' ;
	//$arr_pintar['mc5_16a30'] = '' ;
	//$arr_pintar['mc5_31a'] = '' ;
	
	$arr_pintar['mc6_2a8'] = '' ;
	//$arr_pintar['mc6_9a15'] = '' ;
	//$arr_pintar['mc6_16a30'] = '' ;
	//$arr_pintar['mc6_31a'] = '' ;
	
	//query para sacar la información de todos los barrios de la tablas estandarizadas de metrocuadrado archivadas en la BD t_mc
	$sql = "
		SELECT 
		`tipo_inmueble`, 
		`zona`, 
		`sector`, 
		`Barrio`, 
		`id_barrio`, 
		`Estrato`, 
		`Valorizacion`, 
		`ValorEdad2a8`, 
		`ValorEdad9a15`, 
		`ValorEdad16a30`, 
		`ValorEdadmasde31`
		FROM `t_mc`
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	foreach ( $arr_salidabd as $key => $val ){
		$arr_mc[$val['id_barrio']][$val['tipo_inmueble']][$val['Estrato']] = $val ;		
	}
	
	//se recorre el arreglo para llenar los valores correspondeintes a al edad 2a8 en cada estrato
	foreach ( $arr_mc as $id_barrio => $val ){
		$linea = $arr_pintar ;
		
		
		$linea['mc4_2a8'] = $val['Apartamentos'][4]['ValorEdad2a8'] ;
		//$linea['mc4_9a15'] = $val['Apartamentos'][4]['ValorEdad9a15'] ;
		//$linea['mc4_16a30'] = $val['Apartamentos'][4]['ValorEdad16a30'] ;
		//$linea['mc4_31a'] = $val['Apartamentos'][4]['ValorEdadmasde31'] ;
		
		$linea['mc5_2a8'] = $val['Apartamentos'][5]['ValorEdad2a8'] ;
		//$linea['mc5_9a15'] = $val['Apartamentos'][5]['ValorEdad9a15'] ;
		//$linea['mc5_16a30'] = $val['Apartamentos'][5]['ValorEdad16a30'] ;
		//$linea['mc5_31a'] = $val['Apartamentos'][5]['ValorEdadmasde31'] ;
		
		$linea['mc6_2a8'] = $val['Apartamentos'][6]['ValorEdad2a8'] ;
		//$linea['mc6_9a15'] = $val['Apartamentos'][6]['ValorEdad9a15'] ;
		//$linea['mc6_16a30'] = $val['Apartamentos'][6|]['ValorEdad16a30'] ;
		//$linea['mc6_31a'] = $val['Apartamentos'][6]['ValorEdadmasde31'] ;
		
		//ver_arr( $linea ) ;
		$linea2 = $linea ;
		
		//ciclo para recorrer los valores y convertir a miles o dejar en blanco si no hay valor
		foreach ( $linea as $key => $val2 ){
			if ( $val2 != '' ){
				$linea[$key] = formato_n( $val2 / 1000000 , 2 ) ;
			}else{
				$linea[$key] = '' ;
			}			
		}		
		$arr_mclinea[$id_barrio] = $linea ;
		$arr_mclinea1[$id_barrio] = $linea2 ;
	}
	
	//ciclo para recorrer el arreglo de barrio con información en t_inmueble y anexarle la información de t_mc
	foreach ( $arr_salida as $key => $val ){
		
		if ( isset( $arr_mclinea[$key] )){
			$arr_salida[$key] = array_merge( $val , $arr_mclinea[$key] ) ;
		}else{
			$arr_salida[$key] = array_merge( $val , $arr_pintar ) ;
		}
	}
	
	// Ahora se calcula un indicador de valor promedio del barrio		
	$lista = array('4_10a20' , '5_10a20' , '6_10a20' , '4_20a' , '5_20a' , '6_20a') ;
	$lista2 = array('4_new' , '5_new' , '6_new' , '4_10a20', '5_10a20' , '6_10a20' , '4_20a' , '5_20a' , '6_20a' ) ;
	foreach ( $arr_salida1 as $key => $val ){
		$max_nuevo = 0 ;
		$max_nuevo = max($val['4_new'] , $val['5_new'] , $val['6_new']) ;
		$suma = 0 ;
		$n = 0 ;
		foreach ( $lista as $val1 ){
			if ( $val[$val1] != '' ){
				$suma += $val[$val1] ;
				$n++ ;
			}
		}
		if ( $n == 0 ){
			$promedio = 0 ;
		}else{
			$promedio = $suma / $n ;
		}
		//$dif_abs = $max_nuevo - $promedio ;
		$dif_rel = ($promedio * 100 ) / ($max_nuevo + 0.000001) ;
		
		//introduzco los nuevos valores en la tabla de presentacion $arr_salida
		//$arr_salida[$key]['delta_abs'] = $dif_abs ;
		//$arr_salida[$key]['valor_rel'] = formato_n ($dif_rel , 0) . '%' ;	
		foreach ( $lista2 as $val1 ){
			if ( $val[$val1] != '' ){
				$diferencia =  $max_nuevo -  $arr_salida1[$key][$val1] ;
				$diferencia = formato_n( $diferencia / 1000000 , 2 ) ;
				$arr_salida[$key][$val1] .= "[$diferencia]" ;
			}
		}
		
		//$arr_salida[$key]['val_new'] = formato_n( $max_nuevo / 1000000 ,2 ) ;
		$arr_salida[$key]['val_new'] = $max_nuevo ;
		$arr_salida[$key]['valor_rel'] = $dif_rel ;
	}
	
	$lista3 = array( 'mc4_2a8' , 'mc5_2a8' , 'mc6_2a8' ) ;
	foreach ( $arr_salida as $key => $val ){
		$max_nuevo = $val['val_new'] ;
		foreach ( $lista3 as $val1 ){
			if ( $arr_mclinea1[$key][$val1] != '' ){
				$diferencia =  $max_nuevo -  $arr_mclinea1[$key][$val1] ;
				$diferencia = formato_n( $diferencia / 1000000 , 2 ) ;
				$arr_salida[$key][$val1] .= " [$diferencia]" ;
			}
		}
		$arr_salida[$key]['val_new']  = formato_n( $max_nuevo / 1000000 , 2 ) ;
	}
	
	
	
	// Y se ordena según el indicdor de valor promedio	
	function comp( $arr1 , $arr2 ){
		if ( $arr1['valor_rel'] < $arr2['valor_rel'] ){
			return -1 ;
		}else{
			return 1 ;
		}		
	}
	
	uasort( $arr_salida , 'comp' ) ;
	
	//Posteriormenete se filtran los barrios que tengan diligenciado un número de columnas relevante
	
	$arr_filtro = array() ;
	foreach ( $arr_salida as $key => $val ){
		$i = 0 ;
		if ( ($val['4_10a20'] + $val['5_10a20'] + $val['5_10a20'])> 0 ){
			$i++ ;
		}
		if ( ($val['4_20a'] + $val['5_20a'] + $val['6_20a'])> 0 ){
			$i++ ;
		}
		if ( ($val['4_new'] + $val['5_new'] + $val['6_new'])> 0 ){
			$i++ ;
		}
		
		if ( $i > 1 ){
			if ( $val['valor_rel'] > 150 ){
				$val['valor_rel'] = 'NA' ;
			}else{
				$val['valor_rel'] = formato_n( $val['valor_rel'] , 0 ) . '%' ;
			}			
			$arr_filtro[$key] = $val ;
		}
	}
	
	$n = array() ;
	$g_html = '' ;
	
	$g_html .= html_arreglo_bi( $arr_filtro , 1 , $n , "Tabla por Barrios") ;
	$g_html .= '<br>' ;	
	
	
	
	echo $g_html ;

?>