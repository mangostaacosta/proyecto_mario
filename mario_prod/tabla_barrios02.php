<?php
/****************************************************************************
  20150120  >> tabla_barrios02.php
  Proyecto: Obsevatorio Mario
  Realiza un filtro sobre los resultados de indicadores grupo y un reordeordenamiento
  para presentar unicamente los barrios más relevantes de seguimiento. Calula indicadores adicionales
  por diferencia contra nuevo y ordena acorde a este nuevo criterio.  
******************************************************************************/

	require_once 'header.php' ;
	require_once 'indicadores_grupo.php' ; 
	
	$manual['tex'] = 'Esta opción muestra la información de los barrios ordenados según un criterio de relevancia de valor relativo. El criterio se basa un valor ponderado de los precios de inmuebles usados vs. el precio de los inmuebles nuevos. El valor ponderado se presenta en la columna al final de la tabla.
	<b>valor_rel:</b> Presenta el valor del indicador de valor relativo. A mayor valor el diferencial es mayor y por tanto existe un potencial de mayor valor. 
	La información incluye únicamente los inmuebles que están ubicados en barrios con datos mínimos para hacer comparaciones en la fecha escogida. 
	La tabla presenta el promedio del campo elegido (e.g. precio_metro). Entre parentésis la cantidad de datos disponibles para calcular el valor promedio. A mayor cantidad de datos mayor confianza.	
	En la parte inferior se podrá encontrar una explicación detallada de las columnas de la tabla.
	' . $manual['tex'] ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Operación > Tablas > Seleccionar' ) ;
	
	$lista = array('4_10a20', '5_10a20' , '6_10a20' , '4_20a' , '5_20a' , '6_20a') ;
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
		$dif_rel = 100 - $dif_rel ; //20150316 nueva linea para que el valor quede entre más mejor 
		
		//introduzco los nuevos valores en la tabla de presentacion $arr_salida
		//$arr_salida[$key]['delta_abs'] = $dif_abs ;
		//$arr_salida[$key]['valor_rel'] = formato_n ($dif_rel , 0) . '%' ;		
		$arr_salida[$key]['valor_rel'] = $dif_rel ;
	}
	
	
	function comp( $arr1 , $arr2 ){
		if ( $arr1['valor_rel'] < $arr2['valor_rel'] ){ //20150316 invertí el orden porque invertí el significado del valor relativo
			return 1 ;
		}else{
			return -1 ;
		}		
	}
	
	uasort( $arr_salida , 'comp' ) ;
	
	$arr_filtro = array() ;
	foreach ( $arr_salida as $val ){
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
			//if ( $val['valor_rel'] > 150 ){ //20150316 invertí porque invertí el significado del valor relativo
			if ( $val['valor_rel'] < -50 ){ 
				$val['valor_rel'] = 'NA' ;
			}else{
				$val['valor_rel'] = formato_n( $val['valor_rel'] , 0 ) . '%' ;
			}			
			$arr_filtro[] = $val ;
		}
	}
	
	$n = array() ;
	$g_html = '' ;	
	$g_html .= html_arreglo_bi( $arr_filtro , 1 , $n , "Tabla por Barrios") ;
	$g_html .= '<br>' ;	
		
	
	echo $g_html ;

?>