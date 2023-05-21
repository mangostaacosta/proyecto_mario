<?php
 /****************************************************************************
  20150218  >> informe_baratos.php
  Proyecto: Obsevatorio Mario
  Este codigo debe generar un listado de los inmuebles que dan una buena tasa de negociación al comparar el precio de compra con el posible precio de venta de la zona 
  ******************************************************************************/

	require_once 'header.php' ;
	
	$manual['tit'] = 'Ofertas' ;
	$manual['tex'] = '
	Esta opción presenta un listado de inmuebles que están vigentes a la fecha escogida y que presentan oportunidades de negocio al comparar el precio de venta actual, con los precios de venta potenciales del mercado y de la tablas estandarizadas de metrocuadrado.com. 
	Los criterios de oportunidad se fundamentan en el cálculo de un "Precio de Venta Rentable", el cual se estima con base en el precio al cual está publicado el inmueble. Este precio se compara con tres indicadores:
	1. El valor del M2 de los inmuebles nuevos (con un descuento del 20%)
	2. El valor del M2 acorde al valor ponderado de los inmuebles de 0 a 10 años y los inmuebles de 2 a 8 años de la tabla estandarizada de metrocuadrado.com
	3. El valor del M2 acorde al valor de los inmuebles de 9 a 15 años de la tabla estandarizada de metrocuadrado.com
	
	En primer lugar se presenta un formulario donde se pueden establecer las fechas a comparar:
	<b>Fecha Proceso (obligatorio)</b>: se debe escoger la fecha del análisis. 
	<b>Ver todos los activos? (opcional)</b>: se debe escoger se se listan todos los inmuebles vigentes en la fecha (si) ó si se restringe únicamente a los inmuebles ingresados en la fecha de análisis (no).
	
	A continuación se muestra la tabla de resultados para la fecha elegida. La tabla incluye las siguientes columnas (valores monetarios se expresados en millones de pesos):	
	<b>Relev:</b> Indica la relevancia del inmueble. 2 -> cumple los dos criterios de precio, 1->cumple sólo un criterio de precio, 0->unicamente cumple el criterio de la comparación contra la franja de 9 a 15 años
	<b>Inmueble:</b> Identificador de búsqueda del inmueble.
	<b>Tipo:</b> tipología del inmueble (apartamentos|casas)
	<b>Fecha Ini:</b> Fecha de de ingreso del inmueble al sistema.
	<b>Fecha:</b> Fecha de la última actualización del inmueble.
	<b>Barrio:</b> es el id_barrio identificador único del barrio.
	<b>Estrato:</b> estrato de la fila correspondiente.
	<b>Antig:</b> rango de antiguedad.
	<b>Atrib:</b> listado de algunos atributos del inmueble que son el número de Piso (P:) , cantidad de ascensores (A:) , cantidad de garajes (G:) . Si el indicador está vacío quiere decri que aún no se ha identificado NO significa que el valor sea 0.
	<b>Area:</b> área privada del inmueble.
	<b>PM2:</b> valor del precio/M2 del inmueble en mil 	lones de pesos.	
	<b>pm2_venta:</b> valor del precio/M2 que el algoritmo calcula al cual se debería vender el inmueble generando un margen de ganancia adecuado. (30% sobre la compra)
	<b>pm2_merc:</b> valor del precio/M2 que el algoritmo calcula al cual se podría vender el inmueble acorde con los valores de metrocuadrado y el valor de la franja de mercado entre 0 y 10 años. (PrecioMercado(0a10)*0.75 + PrecioMCuadrado(2a8)*0.25) * 90%
	<b>pm2_new:</b> valor del precio/M2 del mercado de nuevo.
	<b>pm2_mc_2a8:</b> valor del precio/M2 de la franja de de 2 a 8 años de las tablas estandarizadas de metrocuadrado.com.
	<b>pm2_mc_9a15:</b> valor del precio/M2 de la franja de de 9 a 15 años de las tablas estandarizadas de metrocuadrado.com.
	<b>pm2_0a10:</b> valor del precio/M2 de la franja de 0 a 10 años acorde con los datos de oferta de mercado del sector.
	<b>Precio:</b> precio de venta anunciado del inmueble.
	<b>p_venta:</b> precio de venta calculado acorde con el valor de pm2_venta.
	<b>Diferencia:</b> Diferencia por M2 entre el posible Valor de Venta según los datos de zona (pm2_merc) y el posible Valor de Compra (pm2_venta)
	<b>Absoluto:</b> Diferencia absoluta entre el Valor de Venta Rentable y el posible Valor de Compra (Precio-p_venta).
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Operación > Seguimiento > Ofertas' ) ;
	
	//global $g_fecha ;
	global $g_conexion ;
	
	//crear el formulario para ingresar parámetros
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->Insertar('decision','Ver todos los activos? (si/no)','si') ;
	$forma->siempre = TRUE ;
	$forma->Imprimir() ;
	$f_fecha = $forma->Sacar('f_fecha') ;
	$f_decision = $forma->Sacar('decision') ;
	msj("fecha del form: $f_fecha") ;
	
	
	//validar si se muestran inmeubles de un rango de días o de la fecha puntual
	if ( $f_decision == 'si' ){
		//activar registros en BD vigentes a la fecha
		ActivarFechaBD_Rango( $f_fecha ) ;
	}else{
		//Activar solo los que inician en la fecha
		$sql = "UPDATE t_inmueble SET b_activo = '0' WHERE 1" ;
		$g_conexion->execute ($sql) ;		
		$sql = "UPDATE t_inmueble SET b_activo = '1' WHERE fecha_ini = '$f_fecha'" ;
		$g_conexion->execute( $sql ) ;
	}
	
	
	//Query para extraer datos de precio de los inmuebles
	$sql = "
		SELECT 
		id_inmueble,
		tipo_inm,
		fecha_ini,
		fecha,
		fecha_fin,
		url,
		idM2_ini,
		id_barrio,		
		estrato, 
		antiguedad_rg, 
		area_privada, 
		precio,
		precio_metro, 
		piso,
		ascensor,
		garajes,
		comentario,
		b_activo
		FROM t_inmueble
		WHERE b_activo = 1 and b_normal = 1 
		ORDER BY b_activo DESC, id_barrio, estrato, precio_metro
	" ;

	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	$arr_inm = $arr_salidabd ;
	
	//Query para buscar la fecha de informe de barrios más actualizada
	$sql = "
			SELECT DISTINCT fecha FROM t_hist_zona ORDER BY fecha
		" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	$arr_fechas = $arr_salidabd ;
	
	$linea = end( $arr_fechas ) ;
	$fecha1 = $linea['fecha'] ;
	
	//Query para buscar los datos estadísticos históricos almacenados en la BD 
	$sql = "SELECT MAX(fecha) AS maxfecha FROM t_hist_zona" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = $g_conexion->fetch() ;
	$maxfecha = $arr_salidabd['maxfecha'] ;
	
	$sql = "
		SELECT
			fecha,
			id_barrio_hist,
			tipo,
			estrato,
			antiguedad_rg,
			conteo,
			pm2_prom,
			pm2_min,
			pm2_max,
			p_prom
		FROM t_hist_zona
		WHERE fecha ='$maxfecha' ;
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	$arr_barrios = $arr_salidabd ;
	foreach ( $arr_barrios as $val ){
		$arr_barrios2[$val['id_barrio_hist']][$val['estrato']][$val['antiguedad_rg']] = $val ;
	}
	
	//Query para buscar info de tablas MC
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
	$arr_mc = $arr_salidabd ;
	foreach ( $arr_mc as $val ){
		$arr_mc2[$val['tipo_inmueble']][$val['id_barrio']][$val['Estrato']] = $val ;
	}
		
	
	//Sección para calcular posibles precios de venta en cada inmueble	
	$last_barrio = '' ;
	$last_estrato = '' ;
	$i = 0 ;
	foreach ( $arr_inm as $key=>$val ){
		$i = $key ;
		if ( ($last_barrio == $val['id_barrio']) AND ( $last_estrato == $val['estrato']) AND ( $ok == 0 ) ){
		//Validar si la información para el correspondiente barrio/estrato ya está completa para no repetir
			continue ;
		}
		if ( isset( $val['precio_metro'] )){
		//si hay información de precio_metro se calcula el precio de venta
			$val['pm2_venta'] = $val['precio_metro'] * FACTOR_VENTA ;
		}else{
			MsjE("precio_metro no definido en {$val['id_inmueble']} código MC: {$val['idM2_ini']}") ;
			continue ;
		}
		
		$arr_temp = $arr_barrios2[$val['id_barrio']][$val['estrato']] ;
		
		if ( isset( $arr_temp['new'] )){
		//si hay información del mercado para nuevos se asigna
			$val['pm2_new'] = $arr_temp['new']['pm2_prom'] ;
		}else{
			$val['pm2_new'] = 'NA' ;
			MsjE("Precio Nuevo no definido en {$val['id_inmueble']} código MC: {$val['idM2_ini']} para el barrio {$val['id_barrio']}") ;		
		}
		
		$p = PESO_MERCADO ;
		if ( isset(  $arr_mc2['Apartamentos'][$val['id_barrio']] )){
		//si hay información estandarizada de metrocuadrado de 2a8 se asigna
			$val['pm2_mc_2a8'] = $arr_mc2['Apartamentos'][$val['id_barrio']][$val['estrato']]['ValorEdad2a8'] ;
			$val['pm2_mc_9a15'] = $arr_mc2['Apartamentos'][$val['id_barrio']][$val['estrato']]['ValorEdad9a15'] ;
		}else{
		//no hay información estandarizada de metrocuadrado de 2a8 el peso $p de la ponderación se asigna al otro factor
			$val['pm2_mc_2a8'] = 'NA' ;
			$val['pm2_mc_9a15'] = 'NA' ;
			$p = 1 ;
		}
		
		if ( isset( $arr_temp['0a10'] )){
		//si hay información de mercado 0a10 se asigna
			$val['pm2_0a10'] = $arr_temp['0a10']['pm2_prom'] ;
		}else{
		//no hay información de mercado 0a10 el peso $p de la ponderación se asigna al otro factor
			$val['pm2_0a10'] = 'NA' ;
			$p = 0 ;
		}
		
		
		if ( ($val['pm2_0a10'] == 'NA') AND ( $val['pm2_mc_2a8'] == 'NA')){
		//si no está ninguna de las variables para estimar el precio de mercado de venta se asigna NA
			MsjE("No se puede comparar MERCADO en {$val['id_inmueble']} código MC: {$val['idM2_ini']} para el barrio {$val['id_barrio']}") ;
			$val['pm2_merc'] = 'NA' ;
		}else{
			$val['pm2_merc'] = ( $val['pm2_0a10'] * $p + $val['pm2_mc_2a8']*( 1-$p ) ) * FACTOR_DCTO ;
		}
		
		$temp9 = 0 ; //variable temporal que se activa con la condición de que el precio sea menor que 9a15 		
		if ( $val['pm2_venta'] < ( $val['pm2_new'] * FACTOR_NUEVO )){
		//validar si el precio de venta rentable está a un margen coherente del precio del nuevo
			$val['OK_nuevo'] = 1 ;
		}else{
			$val['OK_nuevo'] = 0 ;
		}
		$ok = $val['OK_nuevo'] ;
		if ( $val['pm2_venta'] < $val['pm2_merc'] ){
		//validar si el precio de venta rentable está debajo del precio del mercado
			$val['OK_merc'] = 1 ;
		}else{
			$val['OK_merc'] = 0 ;
			//20150409: comparar contra el precio de 9 a 15 años por sugerencia de max
			if ( $val['pm2_venta'] < $val['pm2_mc_9a15'] ){
				//$arr_inm4[$val['id_inmueble']] = $val ;
				$temp9 = 1 ;
			}
		}
		$ok = $ok + $val['OK_merc'] ;
		$last_barrio = $val['id_barrio'] ;
		$last_estrato = $val['estrato'] ;
		if ( $ok == 0 ){
			if ( $temp9 == 0 ){
				continue ;
			}else{
				$arr_inm4[$val['id_inmueble']] = $val ;
			}			
		}elseif ( $ok == 1 ) {
		//asignar en el informe de finalistas los que cumplan con alguna de las dos condiciones de precio
			$arr_inm2[$val['id_inmueble']] = $val ;
			//ver_arr($val, "Fin analisis $i" ) ;			
		}elseif ( $ok == 2 ){
		//asignar en el informe de elegidos los que cumplan con las dos condiciones de precio
			$arr_inm3[$val['id_inmueble']] = $val ;
		}		
	}
	
	//Se crean variables adicionales de comparación del precio de venta y el mercado para dar criterios de decisión
	foreach ( $arr_inm2 as $key => $val ){
		$arr_inm2[$key]['p_venta'] = $val['pm2_venta'] * $val['area_privada'] ;
		//$arr_inm2[$key]['diferencial'] = $arr_inm2[$key]['p_venta'] - $val['precio']  ;
		$arr_inm2[$key]['diferencial'] = $arr_inm2[$key]['pm2_merc'] - $val['pm2_venta']  ;
		$arr_inm2[$key]['absoluto'] = $arr_inm2[$key]['p_venta'] - $val['precio']  ;
		$arr_inm2[$key]['cat'] = 1  ;
	}
	
	foreach ( $arr_inm3 as $key => $val ){
		$arr_inm3[$key]['p_venta'] = $val['pm2_venta'] * $val['area_privada'] ;
		//$arr_inm2[$key]['diferencial'] = $arr_inm2[$key]['p_venta'] - $val['precio']  ;
		$arr_inm3[$key]['diferencial'] = $arr_inm3[$key]['pm2_merc'] - $val['pm2_venta']  ;
		$arr_inm3[$key]['absoluto'] = $arr_inm3[$key]['p_venta'] - $val['precio']  ;
		$arr_inm3[$key]['cat'] = 2  ;
	}
	
	foreach ( $arr_inm4 as $key => $val ){
		$arr_inm4[$key]['p_venta'] = $val['pm2_venta'] * $val['area_privada'] ;
		//$arr_inm2[$key]['diferencial'] = $arr_inm2[$key]['p_venta'] - $val['precio']  ;
		$arr_inm4[$key]['diferencial'] = $arr_inm4[$key]['pm2_merc'] - $val['pm2_venta']  ;
		$arr_inm4[$key]['absoluto'] = $arr_inm4[$key]['p_venta'] - $val['precio']  ;
		$arr_inm4[$key]['cat'] = 0  ;
	}
	
	//Ordenamiento basado en el diferencial entre precio mercado y precio venta rentable
	function comp( $arr1 , $arr2 ){
		if ( $arr1['diferencial'] < $arr2['diferencial'] ){
			return 1 ;
		}else{
			return -1 ;
		}		
	}
	
	//se ordenan los dos arreglos y se fusionan en uno
	uasort( $arr_inm2 , 'comp' ) ;
	uasort( $arr_inm3 , 'comp' ) ;
	uasort( $arr_inm4 , 'comp' ) ;
	$arr_inm2 = array_merge( $arr_inm3 , $arr_inm2 ) ;
	$arr_inm2 = array_merge( $arr_inm2 , $arr_inm4 ) ;
	ver_arr( $arr_inm2 , '$arr_inm despues del bucle de cálculos') ;	

	//se crea el arreglo formateado para presetnación de la tabla final
	$arr_pintar = array() ;	
	foreach ( $arr_inm2 as $val ){
		$linea = array() ;
		$linea['Relev'] = $val['cat'] ;
		$linea['Inmueble'] = "<a href=\"{$val['url']}\" target=\"_blank\">{$val['idM2_ini']}</a>" ;	
		$linea['Tipo'] = $val['tipo_inm'] ;		
		$linea['Fecha Ini'] = $val['fecha_ini'] ;
		$linea['Fecha Fin'] = $val['fecha_fin'] ;
		$linea['Fecha'] = $val['fecha'] ;
		$linea['Barrio'] = $val['id_barrio'] ;
		$linea['Estrato'] = $val['estrato'] ;
		$linea['Antig'] = $val['antiguedad_rg'] ;
		$linea['Atrib'] = "P:{$val['piso']}_A:{$val['ascensor']}_G:{$val['garajes']}" ;
		$linea['Area'] = formato_n( $val['area_privada'], 0 ) ;		
		//$linea['Precio'] = formato_n( $val['precio'] / 1000000 , 1 ) ;
		$linea['PM2'] = formato_n( $val['precio_metro'] / 1000000 , 2 ) ;		
		
		$linea['pm2_venta'] = formato_n( $val['pm2_venta'] / 1000000 , 2 ) ;
		$linea['pm2_merc'] = formato_n( $val['pm2_merc'] / 1000000 , 2 ) ;
		$linea['pm2_new'] = formato_n( $val['pm2_new'] / 1000000 , 2 ) ;
		$linea['pm2_mc_2a8'] = formato_n( $val['pm2_mc_2a8'] / 1000000 , 2 ) ;
		$linea['pm2_mc_9a15'] = formato_n( $val['pm2_mc_9a15'] / 1000000 , 2 ) ;
		$linea['pm2_0a10'] = formato_n( $val['pm2_0a10'] / 1000000 , 2 ) ;
		
		$linea['Precio'] = formato_n( $val['precio'] / 1000000 , 1 ) ;
		$linea['p_venta'] = formato_n( $val['p_venta'] / 1000000 , 1 ) ;
		$linea['Diferencia'] = formato_n( $val['diferencial'] / 1000000 , 1 ) ;	
		$linea['Absoluto'] = formato_n( $val['absoluto'] / 1000000 , 1 ) ;	
		
		if ( $val['comentario'] == '' ){
			$val['comentario'] = 'comentario:' ;
		}
		$linea['Comentario_____________________________'] = "<a href=\"edita_inm.php?f_idinmueble={$val['id_inmueble']}\">{$val['comentario']}</a>" ;
		
		$arr_pintar[] = $linea ;	
	}
	
	
	$n = array() ;
	$g_html = '' ;
		
	$g_html .= html_arreglo_bi( $arr_pintar , 1 , $n , "Inmuebles en Seguimiento Activos al $f_fecha") ;
	$g_html .= '<br>' ;
	echo $g_html ;  	
  
?>