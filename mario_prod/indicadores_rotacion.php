<?php
/****************************************************************************
  20150218  >> indicadores_rotacion.php
  Proyecto: Obsevatorio Mario
  Compara la cantidad de apartamentos vigentes en dos fechas para establecer cuales son las tasas de rotación respectivas entre las dos fechas
******************************************************************************/
	
	
	require_once 'header.php' ; 
	
	$manual['tit'] = 'Rotación' ;
	$manual['tex'] = '
	Esta opción presenta estadísticas comparativas en cuanto a la cantidad de inmuebles en le mercado entre dos cortes diferentes en el tiempo. La confiabilidad de los datos presentados depende directamente de la calidad de los procesos de depuración de registros, específicamente la identificación de GEMELOS y la adecuada identificación de los inmuebles a FINALIZAR que se haya realizado en el periodo que se esté analizando.
	En primer lugar se presenta un formulario donde se pueden establecer las fechas a comparar:
	<b>Fecha Inicial (obligatorio)</b>: se debe escoger la fecha del primer corte, históricamente la menos reciente. 
	<b>Fecha Final (obligatorio)</b>: se debe escoger la fecha del primer corte, históricamente la más reciente. 
	
	A continuación se muestran 2 tablas de resultados para las fechas elegidas. En la primera tabla los datos están agrupados por barrio y rango de antigüedad. En la segunda tabla los datos están agrupados por barrio, estrato y rango de antigüedad.En las tablas se muestran las siguientes columnas:	
	<b>id_barrio:</b> es el identificador único del barrio.
	<b>estrato:</b> estrato de la fila correspondiente
	<b>antig:</b> rango de antiguedad de la fila correspondiente	
	<b>inic:</b> cantidad de inmuebles vigentes en la fecha inicial
	<b>salid:</b> cantidad de inmuebles que ya NO estaban vigentes en la fecha final
	<b>qued:</b> cantidad de inmuebles que continuaban vigentes en la fecha final
	<b>entr:</b> cantidad de inmuebles que empezaron a estar vigentes antes de la fecha final
	<b>sal_porc:</b> valor porcentual de los inmuebles que dejaron de estar vigentes frente a la cantidad inicial 
	<b>que_porc:</b> valor porcentual de los inmuebles que continuaron vigentes frente a la cantidad inicial 
	<b>ent_porc:</b> valor porcentual de los inmuebles que ingresaron a estar vigentes frente a la cantidad inicial 
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Operación > Intertemporal > Rotación' ) ;
	
	
	
	//MsjE('Este procedimiento sobreescribe información histórica de forma irreversible. Por favor valide con su nombre de usuario') ;
	
	//Se crea y utiliza el formulario para captruar las variables de entrada a la página
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha1','Fecha inicial:',date('Y-m-d')) ;
	$forma->Insertar('f_fecha2','Fecha final:',date('Y-m-d')) ;
	$forma->siempre = FALSE ;
	$forma->Imprimir() ;
	$f_fecha1 = $forma->Sacar('f_fecha1') ;
	$f_fecha2 = $forma->Sacar('f_fecha2') ;
	
	
	function MyQuery( $where , $k = 0 ){
		global $g_conexion ;
		
		$texto = "
			SELECT t_barrio.zona, t_barrio.sector, t_inmueble.id_barrio, tipo_inm AS tipo, estrato, antiguedad_rg, COUNT(precio_metro) AS conteo, AVG(precio_metro) AS prom, MIN(precio_metro) AS mini, MAX(precio_metro) AS maxi
			FROM t_inmueble
			LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
			WHERE $where AND tipo_inm = 'apartamento'
			GROUP BY tipo, id_barrio, estrato, antiguedad_rg
			ORDER BY tipo, id_barrio, estrato, antiguedad_rg DESC
		" ;
		
		$g_conexion->execute($texto) ;
		$arr_salidabd = array() ;
		while ($arr_salidabd[] = $g_conexion->fetch()) ;
		array_pop( $arr_salidabd ) ;
		
		foreach (  $arr_salidabd as $val ){
			$id_barrio = $val['id_barrio'] ;
			$estrato = $val['estrato'] ;
			$antig = $val['antiguedad_rg'] ;
			$linea[$id_barrio][$estrato][$antig] = $val ;
			$arr_result = $linea ;
		}
		
		if ( $k == 0){
			return $arr_result ;
		}else{
			return $arr_salidabd ;
		}		
	}
	
	//la lista base
	$arr1 = MyQuery( "fecha_ini <= '$f_fecha1' AND fecha_fin >= '$f_fecha1'" , 1 ) ;
	
	//los que siguen vigentes	
	$arr2 = MyQuery( "fecha_ini <= '$f_fecha1' AND fecha_fin > '$f_fecha2' AND fecha_fin > '$f_fecha1'" ) ;
	
	//los que finalizaron
	$arr3 = MyQuery( "fecha_ini <= '$f_fecha1' AND fecha_fin < '$f_fecha2' AND fecha_fin > '$f_fecha1'" ) ;
		
	//los que iniciaron
	$arr4 = MyQuery( "fecha_ini > '$f_fecha1' AND fecha_ini < '$f_fecha2' AND fecha_fin >= '$f_fecha2'" ) ;
		
	//los que rotaron interno
	$arr5 = MyQuery( "fecha_ini > '$f_fecha1' AND fecha_fin < '$f_fecha2'" ) ;
		
	foreach ( $arr1 as $key=>$val ){
		$id_barrio = $val['id_barrio'] ;
		$estrato = $val['estrato'] ;
		$antig = $val['antiguedad_rg'] ;
		$iniciales = $val['conteo'] ;
		$salidos = $arr3[$id_barrio][$estrato][$antig]['conteo'] + $arr5[$id_barrio][$estrato][$antig]['conteo'] ;
		$quedados = $arr2[$id_barrio][$estrato][$antig]['conteo'] ;
		$nuevos = $arr4[$id_barrio][$estrato][$antig]['conteo'] ;
		$arr_results[$id_barrio][$estrato][$antig] = array( 'inic' => $iniciales, 'salid' => $salidos, 'qued' => $quedados , 'entr' => $nuevos ) ;
		$arr_otro[] = array( 'id_barrio' => $id_barrio, 'estrato' => $estrato , 'antig' => $antig ,'inic' => $iniciales, 'salid' => $salidos, 'qued' => $quedados , 'entr' => $nuevos ) ;
	}
	
	ver_arr( $arr_results , '$arr_results' ) ;
	ver_arr( $arr_otro , '$arr_otro' ) ;
	
	$campo = 'antig' ;
	foreach ( $arr_otro as $val ){
		$arr_results2[$val['id_barrio']][$val[$campo]]['inic'] += $val['inic'] ;
		$arr_results2[$val['id_barrio']][$val[$campo]]['salid'] += $val['salid'] ;
		$arr_results2[$val['id_barrio']][$val[$campo]]['qued'] += $val['qued'] ;
		$arr_results2[$val['id_barrio']][$val[$campo]]['entr'] += $val['entr'] ;
	}	
	ver_arr( $arr_results2 , '$arr_results2' ) ;
	
	foreach ( $arr_results2 as $id_barrio => $val1 ){
		foreach ( $val1 as $key2 => $val2 ){			
			$lin['id_barrio'] = $id_barrio ;
			$lin[$campo] = $key2 ;
			$linea = array_merge($lin,$val2) ;
			$linea['sal_porc'] = formato_n( $val2['salid'] / $val2['inic'] * 100 , 0 ) . '%' ; 
			$linea['que_porc'] = formato_n( $val2['qued'] / $val2['inic'] * 100 , 0 ) . '%' ; 
			$linea['ent_porc'] = formato_n( $val2['entr'] / $val2['inic'] * 100 , 0 ) . '%' ; 
			$arr_otro2[] = $linea ;
		}
	}
	
	ver_arr( $arr_otro2 , '$arr_otro2' ) ;
	
	$n = array() ;
	$g_html = '' ;	
	$g_html .= html_arreglo_bi( $arr_otro2 , 1 , $n , "Tabla por Barrios") ;
	$g_html .= '<br>' ;	
	$g_html .= html_arreglo_bi( $arr_otro , 1 , $n , "Tabla por Barrios") ;
	$g_html .= '<br>' ;	
	echo $g_html ; 
	
	if ( $usuario != 'macmax' ){	//validación antes de update en bd
		die() ;
	}
	
	array_pop( $arr_result ) ;	
	
	foreach ( $arr_result as $id_barrio => $val1 ){
		foreach ( $val1 as $tipo_inm => $val2 ){
			foreach ( $val2 as $estrato => $val3 ){
				foreach ( $val3 as $antiguedad_rg => $val4 ){
					$sql = "
						INSERT into t_hist_zona (
							fecha,
							id_barrio_hist,
							tipo,
							estrato,
							antiguedad_rg,
							conteo,
							p_prom,
							p_min,
							p_max,
							pm2_prom,
							pm2_min,
							pm2_max,
							a_prom,
							habit_prom
						) VALUES (							
							'$f_fecha',
							'$id_barrio',
							'$tipo_inm',
							'$estrato',
							'$antiguedad_rg',
							'{$val4['conteo']}',
							'{$val4['p_prom']}',
							'{$val4['p_min']}',
							'{$val4['p_max']}',
							'{$val4['pm2_prom']}',
							'{$val4['pm2_min']}',
							'{$val4['pm2_max']}',
							'{$val4['a_prom']}',
							'{$val4['habit_prom']}'
						)					
					" ;	
					$g_conexion->execute ($sql) ;
				}
			}
		}
	}
	
	
?>