<?php
/****************************************************************************
  20150120  >> tabla_barrios01.php
  Proyecto: Obsevatorio Mario
  Realiza un filtro sobre los resultados de indicadores grupo y un reordeordenamiento
  para presentar unicamente los barrios más relevantes de seguimiento
  además realiza unos SELECT agrupados de la BD para mostrar indicadores más gloables
******************************************************************************/

	require_once 'header.php' ;
	require_once 'indicadores_grupo.php' ;
	
	$manual['tex'] = 'Esta opción presetna información que incluye únicamente los inmuebles que están ubicados en barrios con datos mínimos para comparaciones en la fecha escogida. 
	La tabla presenta el promedio del campo elegido (e.g. precio_metro). Entre parentésis la cantidad de datos disponibles para calcular el valor promedio. A mayor cantidad de datos mayor confianza.	
	En la parte inferior se podrá encontrar una explicación detallada de las columnas de las tablas.
	' . $manual['tex'] ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Operación > Tablas > Datos Mínimos' ) ;
	
	
	/*
	$form1 = new Formulario() ;
	$form1->Insertar('decision','Ver todos los activos? (si/no)','si') ;	
	$form1->siempre = FALSE ;
	$form1->Imprimir() ;
	$f_decision = $form1->Sacar('decision') ;
	*/

	/*// Innecesario?	
	function comp( $arr1 , $arr2 ){
		//BLA BLA  falta definir esta vaina
		return 1 ;
	}	
	uasort( $arr_salida , 'comp' ) ;
	//*/
	
	$arr_filtro = array() ;
	//ver_arr( $arr_salida , 'arr_salida') ;
	
	/*
	foreach ( $arr_salida as $val ){
		$i = 0 ;
		if ( ($val['4_new'] + $val['5_new'] + $val['6_new'])> 0 ){
			$i++ ;
		}
		if ( ($val['4_0a10'] + $val['5_0a10'] + $val['6_0a10'])> 0 ){
			$i++ ;
		}
		if ( ($val['4_10a20'] + $val['5_10a20'] + $val['6_10a20'])> 0 ){
			$i++ ;
		}
		if ( ($val['4_20a'] + $val['5_20a'] + $val['6_20a'])> 0 ){
			$i++ ;
		}		
		
		if ( $i > 1 ){	
			$arr_filtro[] = $val ;
			$conteo_parcial += $arr_tot_barrios[$val[]]
		}
	}
	*/
	$conteo_parcial = 0 ;
	foreach ( $arr_salida2 as $key => $val ){
		$i = 0 ;
		if ( ($val['4_new'] + $val['5_new'] + $val['6_new'])> 0 ){
			$i++ ;
		}
		if ( ($val['4_0a10'] + $val['5_0a10'] + $val['6_0a10'])> 0 ){
			$i++ ;
		}
		if ( ($val['4_10a20'] + $val['5_10a20'] + $val['6_10a20'])> 0 ){
			$i++ ;
		}
		if ( ($val['4_20a'] + $val['5_20a'] + $val['6_20a'])> 0 ){
			$i++ ;
		}		
		
		if ( $i > 1 ){	
			$arr_filtro[] = $arr_salida[$key] ;
			$conteo_parcial += $arr_tot_barrios[$val['id_barrio']] ;
		}
	}
	
	
	
	$n = array() ;
	
	MsjE("Inmuebles incluidos en la búsqueda: $conteo_parcial ") ;
	$g_html = '' ;	
	$g_html .= html_arreglo_bi( $arr_filtro , 1 , $n , "Tabla por Barrios") ;
	$g_html .= '<br>' ;	
		
	echo $g_html ;

?>