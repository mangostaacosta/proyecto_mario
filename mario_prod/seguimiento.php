<?php
 /****************************************************************************
  20150121  >> seguimiento.php
  Proyecto: Obsevatorio Mario
  Este codigo debe mostrar un resultado similar al de vecinos.php, sin  embargo en este caso
  no se centra en un barrio determiando, sino en todos los apartamentos que están siendo "seguidos" que a hoy equivale a que tienen
  un registro asociado en t_matevaluacion. Y seguramente también habrá que tener en cuenta que estén activos?
  
  ******************************************************************************/

	require_once 'header.php' ;
	
	$manual['tit'] = 'Inmuebles' ;
	$manual['tex'] = 'Esta opción presenta un listado de inmuebles que se encuentran marcados para SEGUIMIENTO en el sistema. La información se divide en dos tablas. La primera muestra los inmuebles que están vigentes en la fecha de proceso y la segunda tabla presenta los que no están vigentes. En general los inmuebles en seguimiento son aquellos que tienen matriz de evaluación.  
	En primer lugar se presenta un formulario donde se ingresa la fecha del reporte:
	<b>Fecha Proceso (obligatorio)</b>: se debe escoger la fecha del análisis, el formato es AAAA-MM-DD
	
	A continuación se muestran las tablas de resultados. Incluyen las siguientes columnas (los valores monetarios se expresan en millones de pesos):	
	<b>Ubicación:</b> presenta la Zona, Sector e id_barrio donde está ubicado el inmueble.
	<b>Inmueble:</b> Identificador de búsqueda del inmueble. Al hacer click en el enlace se despliega el URL externo del inmueble.
	<b>Tipo:</b> tipología del inmueble (apartamentos|casas).
	<b>Barrio:</b> Presenta la información de barrio digitada en la página externa, la cual no siempre conicide con el id_barrio generado a partir de la información catastral.
	<b>Fecha Ini:</b> Fecha de ingreso del inmueble al sistema.
	<b>Fecha Fin:</b> Fecha de posible pérdida de vigencia del inmueble en el sistema.
	<b>Estrato:</b> estrato de la fila correspondiente.
	<b>Antig:</b> rango de antiguedad de la fila correspondiente.	
	<b>Atrib:</b> listado de algunos atributos del inmueble que son el número de Piso (P:) , cantidad de ascensores (A:) , cantidad de garajes (G:) . Si el indicador está vacío significa que aún no se ha identificado, NO significa que el valor sea 0.
	<b>Area:</b> área privada del inmueble.
	<b>Precio:</b> precio de venta anunciado del inmueble.	
	<b>PM2:</b> valor del precio/M2 del inmueble.
	<b>Comentario:</b> texto alfanumérico ingresado manualmente por el usuario mediante la funcionalidad de edición del inmueble. Al hacer click en el enlace se despliega la opción de edición del inmueble.
	<b>Puntuaje:</b> puntaje total obtenido por el inmueble acorde con los valores ingresados en su matriz de evaluación.	
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Operación > Seguimiento > Inmuebles' ) ;
	
	//global $g_fecha ;
	global $g_conexion ;
	
	
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	//$forma->Insertar('f_idbarrio','ID Barrio:','') ;
	//$forma->Insertar('decision','Ver todos los activos? (si/no)','si') ;
	$forma->siempre = TRUE ;
	$forma->Imprimir() ;
	$f_fecha = $forma->Sacar('f_fecha') ;
	//$id_barrio = $forma->Sacar('f_idbarrio') ;
	//$f_decision = $forma->Sacar('decision') ;
	
	ActivarFechaBD_Rango( $f_fecha ) ;
	
	//query para los inmuebles que tienen seguimiento debido a que tienen matriz de evaluación
	$sql = "
		SELECT 
		id_inmueble,
		tipo_inm,
		fecha_ini,
		fecha_fin,
		idM2_ini,
		url,
		telefono,
		t_inmueble.id_barrio,
		t_inmueble.barrio,
		t_barrio.zona,
		t_barrio.sector,
		estrato, 
		antiguedad_rg, 
		area_privada,		
		precio,
		precio_metro, 
		b_duda_barrio,
		piso,
		ascensor,
		garajes,
		t_inmueble.comentario,
		punt_total,
		b_activo
		FROM t_matevaluacion
		LEFT JOIN t_inmueble ON id_inmueble=id_evaluado
		LEFT JOIN t_barrio ON t_inmueble.id_barrio=t_barrio.id_barrio		
		ORDER BY b_activo DESC, t_barrio.id_barrio, precio_metro
	" ;		
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	
	//query para los que tienen segumiento manual
	$sql = "
		SELECT 
		id_inmueble,
		tipo_inm,
		fecha_ini,
		fecha_fin,
		idM2_ini,
		url,
		telefono,
		t_inmueble.id_barrio,
		t_inmueble.barrio,
		t_barrio.zona,
		t_barrio.sector,
		estrato, 
		antiguedad_rg, 
		area_privada,		
		precio,
		precio_metro, 
		b_duda_barrio,
		piso,
		ascensor,
		garajes,
		t_inmueble.comentario,
		b_activo
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio=t_barrio.id_barrio
		WHERE b_sgtosimp = 1
		ORDER BY b_activo DESC, t_barrio.id_barrio, precio_metro
	" ;
	$g_conexion->execute( $sql ) ;
	$arr_salidabd2 = array() ;
	while ($arr_salidabd2[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd2 ) ;
	
	$arr_pintar = array() ;
	$arr_pintar2 = array() ;
	foreach ( $arr_salidabd as $val ){
		$linea['Ubicación'] = $val['zona'] . '>>' . $val['sector'] . '>>' . $val['id_barrio'] ;
		$linea['Inmueble'] = "<a href=\"{$val['url']}\" target=\"_blank\">{$val['idM2_ini']}</a>" ;	
		$linea['Tipo'] = $val['tipo_inm'] ;	
		
		if ( $val['b_duda_barrio'] == 1 ){
			$linea['Barrio_________'] = $val['barrio'] . '(d)' ;
		}else{
			$linea['Barrio_________'] = $val['barrio'] ;
		}
		$linea['Fecha Ini'] = $val['fecha_ini'] ;
		$linea['Fecha Fin'] = $val['fecha_fin'] ;
		$linea['Estrato'] = $val['estrato'] ;
		$linea['Antig'] = $val['antiguedad_rg'] ;
		$linea['Atrib'] = "P:{$val['piso']}_A:{$val['ascensor']}_G:{$val['garajes']}" ;
		$linea['Area'] = formato_n( $val['area_privada'], 0 ) ;
		$linea['Precio'] = formato_n( $val['precio'] / 1000000 , 1 ) ;
		$linea['PM2'] = formato_n( $val['precio_metro'] / 1000000 , 2 ) ;
		
		if ( $val['comentario'] == '' ){
			$val['comentario'] = 'comentario:' ;
		}
		$linea['Comentario_____________________________'] = "<a href=\"edita_inm.php?f_idinmueble={$val['id_inmueble']}\">{$val['comentario']}</a>" ;
		$linea['Puntaje'] = $val['punt_total'] ;
		$linea['Sgto'] = 'matriz' ;
		
		if ( $val['b_activo'] == 1 ){
			$arr_pintar[] = $linea ;
		}else{
			$arr_pintar2[] = $linea ;
		}		
	}
	
	foreach ( $arr_salidabd2 as $val ){
		$linea['Ubicación'] = $val['zona'] . '>>' . $val['sector'] . '>>' . $val['id_barrio'] ;
		$linea['Inmueble'] = "<a href=\"{$val['url']}\" target=\"_blank\">{$val['idM2_ini']}</a>" ;	
		$linea['Tipo'] = $val['tipo_inm'] ;	
		
		if ( $val['b_duda_barrio'] == 1 ){
			$linea['Barrio_________'] = $val['barrio'] . '(d)' ;
		}else{
			$linea['Barrio_________'] = $val['barrio'] ;
		}
		$linea['Fecha Ini'] = $val['fecha_ini'] ;
		$linea['Fecha Fin'] = $val['fecha_fin'] ;
		$linea['Estrato'] = $val['estrato'] ;
		$linea['Antig'] = $val['antiguedad_rg'] ;
		$linea['Atrib'] = "P:{$val['piso']}_A:{$val['ascensor']}_G:{$val['garajes']}" ;
		$linea['Area'] = formato_n( $val['area_privada'], 0 ) ;
		$linea['Precio'] = formato_n( $val['precio'] / 1000000 , 1 ) ;
		$linea['PM2'] = formato_n( $val['precio_metro'] / 1000000 , 2 ) ;
		
		if ( $val['comentario'] == '' ){
			$val['comentario'] = 'comentario:' ;
		}
		$linea['Comentario_____________________________'] = "<a href=\"edita_inm.php?f_idinmueble={$val['id_inmueble']}\">{$val['comentario']}</a>" ;
		$linea['Puntaje'] = 'NA' ;
		$linea['Sgto'] = 'obser' ;
		
		if ( $val['b_activo'] == 1 ){
			$arr_pintar[] = $linea ;
		}else{
			$arr_pintar2[] = $linea ;
		}		
	}
	
	//ver_arr( $arr_pintar, '$arr_pintar' ) ;
	
	/*
	//query con la informacion del barrio
	$f_campo = 'precio_metro' ;
	$sql = "
		SELECT t_barrio.zona, t_barrio.sector, t_inmueble.id_barrio, estrato, antiguedad_rg, COUNT($f_campo) AS cuenta, AVG($f_campo) AS prom, MIN($f_campo) AS mini, MAX($f_campo) AS maxi
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE 
		t_inmueble.id_barrio = '$id_barrio' AND 
		t_inmueble.fecha = '$f_fecha' AND 
		b_activo = 1 
		AND b_normal = 1
		GROUP BY id_barrio, estrato, antiguedad_rg
		ORDER BY id_barrio, estrato, antiguedad_rg
	" ;
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
		
	while ($linea = $g_conexion->fetch()){
		$arr_barrio[$linea['estrato']][$linea['antiguedad_rg']] = $linea ;
	}
	
	
	$arr_temp[0]['nom_fila'] = 'VALOR PROMEDIO' ;
	$arr_temp[1]['nom_fila'] = 'Conteo' ;
	$arr_temp[2]['nom_fila'] = 'Mínimo' ;
	$arr_temp[3]['nom_fila'] = 'Máximo' ;
	
	foreach ( $arr_barrio as $estrato => $val ){
		foreach ( $val as $antig => $val1 ){
			$campo = $estrato . '_' . $antig ;
			$arr_temp[0][$campo] = '<b>' . formato_n( $val1['prom'] / 1000000, 2 ) . '</b>' ; 
			$arr_temp[1][$campo] = $val1['cuenta'] ; 			
			$arr_temp[2][$campo] = formato_n( $val1['mini'] / 1000000, 2 ) ; 
			$arr_temp[3][$campo] = formato_n( $val1['maxi'] / 1000000, 2 ) ; 
		}
	}
	*/
	
	/*
	//query con la información de t_mc
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
		WHERE
		id_barrio = '$id_barrio'
		ORDER BY Estrato
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	foreach ( $arr_salidabd as $key => $val ){		
		$arr_mc[$val['tipo_inmueble']][$val['Estrato']] = $val ;		
	}
	
	
	foreach ( $arr_mc as $tipo => $val ){
		foreach ( $val as $estrato => $val1 ){
			$arr_salemc[$tipo]['sectorMC'] = $val1['sector'] ;
			$arr_salemc[$tipo]['valoriz'] = $val1['Valorizacion'] ;
			$campo = "mc_$estrato" . '_2a8' ;
			$arr_salemc[$tipo][$campo] = formato_n( $val1['ValorEdad2a8'] / 1000000, 2 )  ;
		}
	}
	//*/
	
	$n = array() ;
	$g_html = '' ;
	
	//$g_html .= html_arreglo_bi( $arr_temp , 1 , $n , "Estadísticas de Mercado de $id_barrio") ;
	//$g_html .= '<br>' ;	
	//$g_html .= html_arreglo_bi( $arr_salemc , 1 , $n , "Estadísticas de metrocuadrado para $id_barrio") ;
	//$g_html .= '<br>' ;
	
	$g_html .= html_arreglo_bi( $arr_pintar , 1 , $n , "Inmuebles en Seguimiento Activos al $f_fecha") ;
	$g_html .= '<br>' ;
	$g_html .= html_arreglo_bi( $arr_pintar2 , 1 , $n , "Inmuebles en Seguimiento INACTIVOS") ;
	$g_html .= '<br>' ;
  	echo $g_html ;  	
  
?>