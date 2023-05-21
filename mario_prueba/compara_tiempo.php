<?php
/****************************************************************************
  20150212  >> compara_tiempo.php
  Proyecto: Obsevatorio Mario
  Realiza las consultas sobre t_hist_zona para sacar un informe comparativo de fluctuaciones de estadístivos entre dos periodos
  PRE: hay cortes de datos históricos en tabla t_hist_zona
  POST: 
	
******************************************************************************/	
	
	require_once 'header.php' ;
	
	$manual['tit'] = 'Comparativo mensual' ;
	$manual['tex'] = '
	
	Esta opción presenta estadísticas comparativas entre dos cortes diferentes en el tiempo. En primer lugar se presenta un formulario donde se pueden personalizar los indicadores a mostrar. El formulario tiene los siguientes campos:
	<b>Campo Solicitado (obligatorio)</b>: se debe escoger el indicador a mostrar. 
	pm2_prom: valor promedio del metro cuadrado en el barrio
	pm2_min: valor mínimo del metro cuadrado en el barrio
	pm2_max: valor máximo del metro cuadrado en el barrio
	p_prom: valor promedio de los inmuebles en el barrio
	p_min: valor mínimo de los inmuebles en el barrio
	p_max: valor máximo de los inmuebles en el barrio
	a_prom: área promedio de los inmuebles en el barrio
	habit_prom:	cantidad promedio de habitaciones en el barrio
	<b>Fecha Inicial (obligatorio)</b>: se debe escoger la fecha del primer corte, históricamente la más lejana. 
	<b>Fecha Final (obligatorio)</b>: se debe escoger la fecha del primer corte, históricamente la más reciente. 
	
	IMPORTANTE tener en cuenta que las fechas disponibles dependen de los cortes que se hayan realizado mediante la opción de menú: Administración > Corte Mensual
	
	A continuación se muestra la tabla de resultados que incluye los siguientes campos:	
	<b>id_barrio:</b> es el identificador único del barrio.
	<b>Tipo:</b> tipología del inmueble (apartamentos|casas)
	<b>Estrato:</b> estrato de la fila correspondiente
	<b>Antig:</b> rango de antiguedad de la fila correspondiente
	<b>Fecha_1:</b> valor del indicador seleccionado en la Fecha Inicial seleccionada en el formulario superior
	<b>n1:</b> cantidad de datos vigentes en la fecha inicial
	<b>Fecha_2:</b> valor del indicador seleccionado en la Fecha Final seleccionada en el formulario superior
	<b>n2:</b> cantidad de datos vigentes en la fecha final
	<b>delt:</b> diferencia del indicador seleccionado entre las dos fechas elegidas. Las filas de la tabla se ordenan con base en esta columna.
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Operación > Intertemporal > Comparativo mensual' ) ;
	
	//die() ;

	//Sacar menú con las fechas disponibles
	//Sacar menú con los indicadores disponibles	
	//Sacar una tabla con todos los barrios y el valor del indicador, así como el delta ordenado por delta
	//incluir un resumen de zona, sector, estrato

	if (isset( $_GET['pag'] )){
		$pag = $_GET['pag'] ;
	}else{
		$pag = 0 ;
	}
	
	
	if ( $pag == 0){
	//primera entrada a la pagina mostrar fechas para escoger e indicadores
	
		$sql = "
			SELECT DISTINCT fecha FROM t_hist_zona ORDER BY fecha
		" ;
		$g_conexion->execute ($sql) ;
		$arr_salidabd = array() ;
		while ($arr_salidabd[] = $g_conexion->fetch()) ;
		array_pop( $arr_salidabd ) ;
		foreach ( $arr_salidabd as $val ){
			$arr_fechas[$val['fecha']] = $val['fecha'] ; 
		}
		ver_arr( $arr_fechas ) ;

	
		
		//Se crea y utiliza el formulario para capturar las variables de entrada a la página
		$form1 = new FormDespl() ;
		$form1->InsertarDespl('f_fecha1','Fecha inicial:','', $arr_fechas ) ;
		$form1->InsertarDespl('f_fecha2','Fecha final:','', $arr_fechas ) ;
		$form1->Insertar('f_campo','Campo solicitado:','p_prom') ;
		$form1->siempre = TRUE ;
		$form1->Imprimir() ;
		$f_fecha1 = $form1->SacarDespl('f_fecha1') ;
		$f_fecha2 = $form1->SacarDespl('f_fecha2') ;
		$f_campo = $form1->Sacar('f_campo') ;
		//*/
		
		msj ("Llegamos acá") ;
		
		$sql = "
			SELECT				
				t1.id_barrio_hist,
				t1.tipo,
				t1.estrato,
				t1.antiguedad_rg,
				t1.fecha AS f1,
				t1.conteo AS c1,
				t1.$f_campo AS v1,
				t2.fecha AS f2,
				t2.conteo AS c2,
				t2.$f_campo AS v2,
				t2.$f_campo-t1.$f_campo AS delt
			FROM t_hist_zona t1			
			LEFT JOIN
				(SELECT
					fecha,
					id_barrio_hist,
					tipo,
					estrato,
					antiguedad_rg,
					conteo,
					$f_campo
				FROM t_hist_zona) t2				
			ON 	t1.id_barrio_hist = t2.id_barrio_hist
			AND t1.tipo = t2.tipo
			AND t1.estrato = t2.estrato
			AND t1.antiguedad_rg = t2.antiguedad_rg
			WHERE t1.fecha = '$f_fecha1'
			AND t2.fecha = '$f_fecha2' 
			ORDER BY delt DESC, t1.id_barrio_hist			
		" ;
		
		$g_conexion->execute ($sql) ;
		$arr_salidabd = array() ;
		while ($arr_salidabd[] = $g_conexion->fetch()) ;
		array_pop( $arr_salidabd ) ;
		foreach ( $arr_salidabd as $val ){
			
			if ( $f_campo[0] == 'p'){
			//si es un campo de precio, dado que la primera letra es "p"
				$val['v1'] = $val['v1'] / 1000000 ;
				$val['v2'] = $val['v2'] / 1000000 ;
				$val['delt'] = $val['delt'] / 1000000 ;
			}
			
			$val['v1'] = formato_n( $val['v1'] , 2 ) ;
			$val['v2'] = formato_n( $val['v2'] , 2 ) ;
			$val['delt'] = formato_n( $val['delt'] , 2 ) ;			
			
			$linea['id_barrio'] = $val['id_barrio_hist'] ;
			$linea['Tipo'] = $val['tipo'] ;
			$linea['Estrato'] = $val['estrato'] ;
			$linea['Antig'] = $val['antiguedad_rg'] ;
			$linea[$f_fecha1] = $val['v1'] ;
			$linea['n1'] = $val['c1'] ;
			$linea[$f_fecha2] = $val['v2'] ;
			$linea['n2'] = $val['c2'] ;			
			$linea['delt'] = $val['delt'] ;
			
			$arr_pinta[] = $linea ;
		}
				
		$n = array() ;
		$g_html = '' ;			
		$g_html .= html_arreglo_bi( $arr_pinta , 1 , $n , "Comparación de Indicador $f_campo") ;
		$g_html .= '<br>' ;	
			
		echo $g_html ;
		
		//ACA VOY OJO OJO OJO !!!
		
		
	}elseif ( $pag == 1 ){
	// mostrar la comparación general
		
		
	}
	
	/*
	
	//inicializar la BD para activar los registros a ser utilizados
	ActivarFechaBD( $f_fecha ) ;
	
	$lin['conteo'] = 0 ;
	$lin['p_prom'] = 0 ;
	$lin['p_min'] = 0 ;
	$lin['p_max'] = 0 ;
	$lin['pm2_prom'] = 0 ;
	$lin['pm2_min'] = 0 ;
	$lin['pm2_max'] = 0 ;
	$lin['a_prom'] = 0 ;
	$lin['habit_prom'] = 0 ; 
	
	$f_campo = 'precio' ;	
	$sql = "
		SELECT t_barrio.zona, t_barrio.sector, t_inmueble.id_barrio, tipo_inm AS tipo, estrato, antiguedad_rg, COUNT($f_campo) AS conteo, AVG($f_campo) AS prom, MIN($f_campo) AS mini, MAX($f_campo) AS maxi
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE b_activo = 1 AND b_normal = 1  		
		GROUP BY tipo, id_barrio, estrato, antiguedad_rg
		ORDER BY tipo, id_barrio, estrato, antiguedad_rg DESC
	" ;	
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;	
	foreach ( $arr_salidabd as $val ){
		$linea = $lin ;
		$linea['conteo'] = $val['conteo'] ;
		$linea['p_prom'] = $val['prom'] ;
		$linea['p_min'] = $val['mini'] ;
		$linea['p_max'] = $val['maxi'] ;		
		$arr_result[$val['id_barrio']][$val['tipo']][$val['estrato']][$val['antiguedad_rg']] = $linea ;
	}
	//ver_arr ( $arr_salidabd , 'arrsalidabd primera tanda con precio' ) ;
	//ver_arr( $arr_result , 'arr_result') ;
	
	$f_campo = 'precio_metro' ;	
	$sql = "
		SELECT t_barrio.zona, t_barrio.sector, t_inmueble.id_barrio, tipo_inm AS tipo, estrato, antiguedad_rg, COUNT($f_campo) AS conteo, AVG($f_campo) AS prom, MIN($f_campo) AS mini, MAX($f_campo) AS maxi
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE b_activo = 1 AND b_normal = 1  		
		GROUP BY tipo, id_barrio, estrato, antiguedad_rg
		ORDER BY tipo, id_barrio, estrato, antiguedad_rg DESC
	" ;	
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	foreach ( $arr_salidabd as $val ){
		//$linea['conteo'] = $val['conteo'] ;
		$linea = $arr_result[$val['id_barrio']][$val['tipo']][$val['estrato']][$val['antiguedad_rg']] ;
		$linea['pm2_prom'] = $val['prom'] ;
		$linea['pm2_min'] = $val['mini'] ;
		$linea['pm2_max'] = $val['maxi'] ;		
		$arr_result[$val['id_barrio']][$val['tipo']][$val['estrato']][$val['antiguedad_rg']] = $linea ;	
	}
		
	$sql = "
		SELECT t_barrio.zona, t_barrio.sector, t_inmueble.id_barrio, tipo_inm AS tipo, estrato, antiguedad_rg, AVG(area_privada) as a_prom, AVG(habitaciones) AS habit_prom 
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE b_activo = 1 AND b_normal = 1  		
		GROUP BY tipo, id_barrio, estrato, antiguedad_rg
		ORDER BY tipo, id_barrio, estrato, antiguedad_rg DESC
	" ;	
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	foreach ( $arr_salidabd as $val ){
		//$linea['conteo'] = $val['conteo'] ;
		$linea = $arr_result[$val['id_barrio']][$val['tipo']][$val['estrato']][$val['antiguedad_rg']] ;
		$linea['a_prom'] = $val['a_prom'] ;
		$linea['habit_prom'] = $val['habit_prom'] ;		
		$arr_result[$val['id_barrio']][$val['tipo']][$val['estrato']][$val['antiguedad_rg']] = $linea ;	
	}
	
	//ver_arr( $arr_result , 'arr_result') ; 	
	
	//$g_html .= html_arreglo_bi( $arr_result , 1 , $n , "Corte estadístico al $f_fecha") ;
	
	//impri_arre_tabla( $arr_result , $n ) ;
	
	
	echo "Fecha de Corte: $f_fecha<br>" ;
	
	$n = [] ;
	$tituls = array('barrio','tipo','estrato','antig','conteo','p_prom','p_min','p_max','pm2_prom','pm2_min','pm2_max','a_prom','habit_prom' ) ;
	$arr_result[0] = $tituls ;
	impri_arre_tabla_sin( $arr_result , $n ) ;
	echo '<br>' ;
	
	
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
	*/
?>