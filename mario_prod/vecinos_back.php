<?php
 /****************************************************************************
  20150116  >> vecinos.php
  Proyecto: Obsevatorio Mario
  La variable de entrada es el nombre de un id_barrio
  el código realiza la búsqueda de todos los aptos activos del sector (asi como algunas estadísticas grupales y de la tabla de MC_MAX
  y los muestra ordenados acorde con su valor de precio_metro. Adicionalmente incluye un campo editable, donde se pueden escribir
  comentarios de cada inmueble.
  Adicionalmente se puede clickear inmueble para que lleve a detalles más específicos de seguimiento como la matriz
  
  ******************************************************************************/

	require_once 'header.php' ;
	  	
	//global $g_fecha ;
	global $g_conexion ;
	
	//$id_barrio = 'pardo_rubio' ;
	
	if (isset( $_GET['f_idbarrio'] )){
		$id_barrio = $_GET['f_idbarrio'] ;		
	}else{
		//nada
	}
	if (isset( $_GET['f_fecha'] )){
		$f_fecha = $_GET['f_fecha'] ;		
	}else{
		//nada
	}
	
	/* 20150117:Cambio del sql porque movi el campo de comentario a la tabla inicial
	$sql = "
		SELECT 
		id_inmueble,
		tipo_inm,
		idM2_ini,
		url,
		telefono,
		barrio, 		
		estrato, 
		antiguedad_rg, 
		area_privada,		
		precio,
		precio_metro, 
		b_duda_barrio,
		piso,
		ascensor,
		garajes,
		comentario		
		FROM t_inmueble 
		LEFT JOIN t_estudio ON id_inmueble = id_visitado 
		WHERE id_barrio = '$id_barrio'
		ORDER BY precio_metro DESC		
	" ; */
	
	$sql = "
		SELECT 
		id_inmueble,
		tipo_inm,
		idM2_ini,
		url,
		telefono,
		barrio, 		
		estrato, 
		antiguedad_rg, 
		area_privada,		
		precio,
		precio_metro, 
		b_duda_barrio,
		piso,
		ascensor,
		garajes,
		comentario
		FROM t_inmueble
		WHERE id_barrio = '$id_barrio' 
		AND fecha = '$f_fecha
		ORDER BY precio_metro		
	" ;
	
	
	
	//la idea no es mostrar todos los campos sino los más de mercado y los geométricos usarlos para "calcular" un indicador de calidad
	//depronto también es bueno tener el url para que se pueda hacer link
	
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	
	$arr_pintar = array() ;
	foreach ( $arr_salidabd as $val ){
		$linea['Inmueble'] = "<a href=\"{$val['url']}\" target=\"_blank\">{$val['idM2_ini']}</a>" ;	
		$linea['Tipo'] = $val['tipo_inm'] ;		
		$linea['Estrato'] = $val['estrato'] ;
		if ( $val['b_duda_barrio'] == 1 ){
			$linea['Barrio_________'] = $val['barrio'] . '(d)' ;
		}else{
			$linea['Barrio_________'] = $val['barrio'] ;
		}				
		$linea['Antig'] = $val['antiguedad_rg'] ;
		$linea['Area'] = formato_n( $val['area_privada'], 0 ) ;
		$linea['Precio'] = formato_n( $val['precio'] / 1000000 , 1 ) ;
		$linea['PM2'] = formato_n( $val['precio_metro'] / 1000000 , 2 ) ;
		$linea['Atrib'] = "P:{$val['piso']}_A:{$val['ascensor']}_G:{$val['garajes']}" ;
		if ( $val['comentario'] == '' ){
			$val['comentario'] = 'comentario:' ;
		}
		$linea['Comentario_____________________________'] = "<a href='edita_inm.php?f_idinmueble={$val['id_inmueble']}&f_fecha=$f_fecha'>{$val['comentario']}</a>" ;
		$arr_pintar[] = $linea ;
	}
	
	//ver_arr( $arr_pintar, '$arr_pintar' ) ;
	
	
	//query con la informacion del barrio
	$f_campo = 'precio_metro' ;
	$sql = "
		SELECT t_barrio.zona, t_barrio.sector, t_inmueble.id_barrio, estrato, antiguedad_rg, COUNT($f_campo) AS cuenta, AVG($f_campo) AS prom, MIN($f_campo) AS mini, MAX($f_campo) AS maxi
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE 
		t_inmueble.id_barrio = '$id_barrio' AND 
		t_inmueble.fecha = '$f_fecha' AND 
		b_activo = 1 AND b_normal = 1
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
			/*
			$campo = "mc_$estrato" . '_9a15' ;
			$arr_salemc[$tipo][$campo] = $val1['ValorEdad9a15'] ;
			$campo = "mc_$estrato" . '_16a30' ;
			$arr_salemc[$tipo][$campo] = $val1['ValorEdad16a30'] ;
			$campo = "mc_$estrato" . '_31a' ;
			$arr_salemc[$tipo][$campo] = $val1['ValorEdadmasde31'] ;
			*/
		}
	}
	
	$n = array() ;
	$g_html = '' ;
	
	$g_html .= html_arreglo_bi( $arr_temp , 1 , $n , "Estadísticas de Mercado de $id_barrio") ;
	$g_html .= '<br>' ;
	
	$g_html .= html_arreglo_bi( $arr_salemc , 1 , $n , "Estadísticas de metrocuadrado para $id_barrio") ;
	$g_html .= '<br>' ;
	
	$g_html .= html_arreglo_bi( $arr_pintar , 1 , $n , "Inmuebles del $id_barrio") ;
  	echo $g_html ;  	
  
?>