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
	
	$manual['tit'] = 'Listado Barrio' ;
	$manual['tex'] = 'Esta opción permite visualizar los inmuebles vigentes en una fecha determinada para un idBarrio específico.
	
	En primer lugar se presenta un formulario donde se ingresan los parámetros del reporte:
	<b>Fecha Proceso (obligatorio)</b>: se debe escoger la fecha del análisis, el formato es AAAA-MM-DD
	<b>ID Barrio (obligatorio)</b>: se debe ingresar el idBarrio específico del barrio que se desea consultar. En caso de que se acceda a esta opción a través de un enlace interno, este campo estará diligenciado con el barrio de referencia.
	<b>Ver todos los activos? (opcional):</b> Generalmente se debe utilizar el valor si, ya que permite ver los inmuebles vigentes alrededor de la fecha. La opción "no" restringe únicamente a los inmuebles cuya última actualización corresponde a la fecha ingresada en el primer campo.
	
	El reporte se presenta en cuatro tablas. La primera tabla muestra un resumen estadístico del sector, la segunda tabla presenta la información estandarizada de metrocuadrado.com. La tercera tabla muestra la información de los inmuebles vigentes y la cuarta tabla presenta la información de inmuebles que no están vigentes.
	
	La primera tabla, con el resumen estadístico del barrio, incluye los siguientes campos:	
	<b>nom_fila:</b> identifica el tipo de información que muestra la fila (Promedio, Mínimo, Maximo, Conteo de Datos)	
	<b>4_new:</b> información de inmuebles para estrenar en estrato 4
	<b>4_0a10:</b> información de inmuebles de hasta 10 años de antigüedad en estrato 4	
	<b>4_10a20:</b> información de inmuebles de 10 hasta 20 años de antigüedad en estrato 4
	<b>4_20a:</b> información de inmuebles de más de 20 años de antigüedad en estrato 4
	<b>5_new:</b> información de inmuebles para estrenar en estrato 5
	<b>5_0a10:</b> información de inmuebles de hasta 10 años de antigüedad en estrato 5
	<b>5_10a20:</b> información de inmuebles de 10 hasta 20 años de antigüedad en estrato 5
	<b>5_20a:</b> información de inmuebles de más de 20 años de antigüedad en estrato 5
	<b>6_new:</b> información de inmuebles para estrenar en estrato 6
	<b>6_0a10:</b> información de inmuebles de hasta 10 años de antigüedad en estrato 6
	<b>6_10a20:</b> información de inmuebles de 10 hasta 20 años de antigüedad en estrato 6
	<b>6_20a:</b> información de inmuebles de más de 20 años de antigüedad en estrato 6	
	Cuando el sistema contiene información de estratos adicionales o inmuebles sin estrato, ésta información se presenta en columnas adicionales. 
	
	La segunda tabla, con la información estadarizada de metrocuadrado.com, incluye los siguientes campos:
	<b>sectorMC:</b> muestra la información del sector acorde con la información estadarizada de metrocuadrado.com.
	<b>valoriz:</b> muestra la información de valorización  acorde con la información estandarizada de metrocuadrado.com.
	<b>4_2a8:</b> información de inmuebles de 2 hasta 8 años de antigüedad en estrato 4
	<b>4_9a15:</b> información de inmuebles de 9 hasta 15 años de antigüedad en estrato 4
	<b>4_16a30:</b> información de inmuebles de 16 hasta 30 años de antigüedad en estrato 4
	<b>4_31a:</b> información de inmuebles de más de 30 años de antigüedad en estrato 4
	<b>5_2a8:</b> información de inmuebles de 2 hasta 8 años de antigüedad en estrato 5
	<b>5_9a15:</b> información de inmuebles de 9 hasta 15 años de antigüedad en estrato 5
	<b>5_16a30:</b> información de inmuebles de 16 hasta 30 años de antigüedad en estrato 5
	<b>5_31a:</b> información de inmuebles de más de 30 años de antigüedad en estrato 5
	<b>6_2a8:</b> información de inmuebles de 2 hasta 8 años de antigüedad en estrato 6
	<b>6_9a15:</b> información de inmuebles de 9 hasta 15 años de antigüedad en estrato 6
	<b>6_16a30:</b> información de inmuebles de 16 hasta 30 años de antigüedad en estrato 6
	<b>6_31a:</b> información de inmuebles de más de 30 años de antigüedad en estrato 6
	Cuando el sistema contiene información de estratos adicionales o inmuebles sin estrato, ésta información se presenta en columnas adicionales. 
	
	La tercera tabla, con la información de los inmuebles vigentes, incluye las siguientes columnas (los valores monetarios se expresan en millones de pesos):	
	
	<b>Inmueble:</b> Identificador de búsqueda del inmueble. Al hacer click en el enlace se despliega el URL externo del inmueble.
	<b>Fecha Ini:</b> Fecha de ingreso del inmueble al sistema.
	<b>Fecha Fin:</b> Fecha de posible pérdida de vigencia del inmueble en el sistema.
	<b>Tipo:</b> tipología del inmueble (apartamentos|casas).
	<b>Estrato:</b> estrato de la fila correspondiente.
	<b>Barrio:</b> Presenta la información de barrio digitada en la página externa, la cual no siempre conicide con el id_barrio generado a partir de la información catastral.
	<b>Antig:</b> rango de antiguedad de la fila correspondiente.	
	<b>Atrib:</b> listado de algunos atributos del inmueble que son el número de Piso (P:) , cantidad de ascensores (A:) , cantidad de garajes (G:) . Si el indicador está vacío significa que aún no se ha identificado, NO significa que el valor sea 0.
	<b>Area:</b> área privada del inmueble.
	<b>Precio:</b> precio de venta anunciado del inmueble.	
	<b>PM2:</b> valor del precio/M2 del inmueble.
	<b>Comentario:</b> texto alfanumérico ingresado manualmente por el usuario mediante la opción de edición del inmueble. Al hacer click en el enlace se despliega la opción de edición del inmueble.
	
	La cuarta tabla incluye las mismas columnas de la tabla anterior. En este caso se incluye el listado de inmuebles que no se encuentran vigentes.	
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Shortcuts > Listado Barrio' ) ;
	
	//global $g_fecha ;
	global $g_conexion ;
	
	//$id_barrio = 'pardo_rubio' ;
	/*
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
	if (isset( $_GET['f_decision'] )){
		$f_decision = $_GET['f_decision'] ;
	}else{
		$f_decision = 'no' ;
	}
	*/
	
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->Insertar('f_idbarrio','ID Barrio:','') ;
	$forma->Insertar('decision','Ver todos los activos? (si/no)','si') ;
	$forma->siempre = TRUE ;
	$forma->Imprimir() ;
	$f_fecha = $forma->Sacar('f_fecha') ;
	$id_barrio = $forma->Sacar('f_idbarrio') ;
	$f_decision = $forma->Sacar('decision') ;
	
	//inicializar los inmuebles activos a la fecha
	ActivarFechaBD( $f_fecha ) ;
	/*
	$sql = "UPDATE t_inmueble SET b_activo = '0' WHERE 1" ;
	$g_conexion->execute ($sql) ;
	$sql = "UPDATE t_inmueble SET b_activo = '1' WHERE (fecha_ini<='$f_fecha' AND (fecha_fin IS NULL OR fecha_fin>'$f_fecha'))" ;
	$g_conexion->execute ($sql) ;
	*/
	
	//Para decidir si se hace la búsqueda por fecha puntual o por los "activos"
	if ( $f_decision == 'no'){	
	//fecha puntual
		$where = " AND (t_inmueble.fecha='$f_fecha') " ;
	}else{
		//$where = " AND (fecha_ini <= '$f_fecha') AND ((fecha_fin > '$f_fecha') OR (fecha_fin IS NULL)) " ;
		$where = " AND 1" ;
	}	
	
		
	$sql = "
		SELECT 
		id_inmueble,
		tipo_inm,
		fecha_ini,
		fecha,
		fecha_fin,
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
		comentario,
		b_activo,
		b_sgtosimp
		FROM t_inmueble
		WHERE id_barrio = '$id_barrio' 
		$where
		ORDER BY fecha_ini DESC, precio_metro
	" ;
	
	
	
	//la idea no es mostrar todos los campos sino los más de mercado y los geométricos usarlos para "calcular" un indicador de calidad
	//depronto también es bueno tener el url para que se pueda hacer link
	
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	
	$arr_pintar = array() ;
	$arr_pintar2 = array() ;
	foreach ( $arr_salidabd as $val ){
		$linea['Inmueble'] = "<a href=\"{$val['url']}\" target=\"_blank\">{$val['idM2_ini']}</a>" ;	
		$linea['Tipo'] = $val['tipo_inm'] ;
		$linea['Estrato'] = $val['estrato'] ;
		if ( $val['b_duda_barrio'] == 1 ){
			$linea['Barrio_________'] = $val['barrio'] . '(d)' ;
		}else{
			$linea['Barrio_________'] = $val['barrio'] ;
		}
		$linea['Fecha Ini'] = $val['fecha_ini'] ;
		$linea['Fecha Fin'] = $val['fecha_fin'] ;
		$linea['Antig'] = $val['antiguedad_rg'] ;
		//$linea['Atrib'] = "P:{$val['piso']}_A:{$val['ascensor']}_G:{$val['garajes']}" ;
		$linea['Atrib'] = "P:{$val['piso']}_G:{$val['garajes']}" ;  //20190112
		$linea['Area'] = formato_n( $val['area_privada'], 0 ) ;
		$linea['Precio'] = formato_n( $val['precio'] / 1000000 , 1 ) ;
		$linea['PM2'] = formato_n( $val['precio_metro'] / 1000000 , 2 ) ;		
		if ( $val['comentario'] == '' ){
			$val['comentario'] = 'comentario:' ;
		}
		$linea['Comentario_____________________________'] = "<a href='edita_inm.php?f_idinmueble={$val['id_inmueble']}&f_fecha=$f_fecha'>{$val['comentario']}</a>" ;
		
		if ( $val['b_sgtosimp'] == 1 ){
			$linea['Sgto'] = 'obser' ;
		}elseif ( $val['b_sgtosimp'] == 2 ){
			$linea['Sgto'] = 'matriz' ;
		}else{
			$linea['Sgto'] = '' ;
		}
		$linea['Telefono'] = $val['telefono'] ;
		
		
		
		if (  $val['b_activo'] == 1 ){
			$arr_pintar[] = $linea ;
		}else{
			$arr_pintar2[] = $linea ;
		}
		
	}
	
	//ver_arr( $arr_pintar, '$arr_pintar' ) ;
	
	
	//query con la informacion del barrio
	$f_campo = 'precio_metro' ;
	$sql = "
		SELECT t_barrio.zona, t_barrio.sector, t_inmueble.id_barrio, estrato, antiguedad_rg, COUNT($f_campo) AS cuenta, AVG($f_campo) AS prom, MIN($f_campo) AS mini, MAX($f_campo) AS maxi
		FROM t_inmueble
		LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
		WHERE 
		t_inmueble.id_barrio = '$id_barrio' 
		$where
		AND b_activo = 1 AND b_normal = 1
		GROUP BY id_barrio, estrato, antiguedad_rg
		ORDER BY id_barrio, estrato, antiguedad_rg
	" ;
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
		
	while ($linea = $g_conexion->fetch()){
		$arr_barrio[$linea['estrato']][$linea['antiguedad_rg']] = $linea ;
		$mi_sector = $linea['zona'] . ">>" . $linea['sector'] ;
	}
	
	//$arr_plantilla['Ubicación'] = '' ; 
	$arr_plantilla['nom_fila'] = '' ; 
	$arr_plantilla['4_new'] = 'NA' ;	
	$arr_plantilla['4_0a10'] = 'NA' ; 
	$arr_plantilla['4_10a20'] = 'NA' ; 
	$arr_plantilla['4_20a'] = 'NA' ; 
	$arr_plantilla['5_new'] = 'NA' ;	
	$arr_plantilla['5_0a10'] = 'NA' ; 
	$arr_plantilla['5_10a20'] = 'NA' ; 
	$arr_plantilla['5_20a'] = 'NA' ; 
	$arr_plantilla['6_new'] = 'NA' ;	
	$arr_plantilla['6_0a10'] = 'NA' ; 
	$arr_plantilla['6_10a20'] = 'NA' ; 
	$arr_plantilla['6_20a'] = 'NA' ; 	
	
	$arr_temp[0] = $arr_plantilla ;
	$arr_temp[1] = $arr_plantilla ;
	$arr_temp[2] = $arr_plantilla ;
	$arr_temp[3] = $arr_plantilla ;
	
	$arr_temp[0]['nom_fila'] = 'PROMEDIO' ;
	$arr_temp[1]['nom_fila'] = 'Mínimo' ;
	$arr_temp[2]['nom_fila'] = 'Máximo' ;
	$arr_temp[3]['nom_fila'] = 'Conteo' ;
	
	foreach ( $arr_barrio as $estrato => $val ){
		foreach ( $val as $antig => $val1 ){		
			$campo = $estrato . '_' . $antig ;
			$arr_temp[0][$campo] = '<b>' . formato_n( $val1['prom'] / 1000000, 2 ) . '</b>' ; 			
			$arr_temp[1][$campo] = formato_n( $val1['mini'] / 1000000, 2 ) ; 
			$arr_temp[2][$campo] = formato_n( $val1['maxi'] / 1000000, 2 ) ; 
			$arr_temp[3][$campo] = $val1['cuenta'] ; 			
		}
	}
	
	$id_barrio2 = trim( $id_barrio , '*' ) ; 
	
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
		id_barrio LIKE '$id_barrio2%'
		ORDER BY Estrato
	" ;
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	foreach ( $arr_salidabd as $key => $val ){
		$mc_sector = $val['zona'] . '>' . $val['sector'] ;
		$arr_mc[$val['tipo_inmueble']][$mc_sector][$val['Estrato']] = $val ;		
	}
	
	$arr_plantilla = array() ;	
	//$arr_plantilla['valoriz'] = '' ;
	$arr_plantilla['4_2a8'] = 'NA' ;
	$arr_plantilla['4_9a15'] = 'NA' ;
	$arr_plantilla['4_16a30'] = 'NA' ;
	$arr_plantilla['4_31a'] = 'NA' ;
	$arr_plantilla['5_2a8'] = 'NA' ;
	$arr_plantilla['5_9a15'] = 'NA' ;
	$arr_plantilla['5_16a30'] = 'NA' ;
	$arr_plantilla['5_31a'] = 'NA' ;
	$arr_plantilla['6_2a8'] = 'NA' ;
	$arr_plantilla['6_9a15'] = 'NA' ;
	$arr_plantilla['6_16a30'] = 'NA' ;
	$arr_plantilla['6_31a'] = 'NA' ;
	
	foreach ( $arr_mc as $tipo => $val ){
		foreach ( $val as $mc_sector => $val0 ){
			$arr_salemc[$tipo][$mc_sector] = $arr_plantilla ;
			foreach ( $val0 as $estrato => $val1 ){
				//$arr_salemc[$tipo][$mc_sector]['valoriz'] = $val1['Valorizacion'] ; OJO porque hay valorización en cada estrato
				$campo = "$estrato" . '_2a8' ;
				$arr_salemc[$tipo][$mc_sector][$campo] = formato_n( $val1['ValorEdad2a8'] / 1000000, 2 ) ;
				$campo = "$estrato" . '_9a15' ;
				$arr_salemc[$tipo][$mc_sector][$campo] = formato_n( $val1['ValorEdad9a15'] / 1000000, 2 ) ;
				$campo = "$estrato" . '_16a30' ;
				$arr_salemc[$tipo][$mc_sector][$campo] = formato_n( $val1['ValorEdad16a30'] / 1000000, 2 ) ;
				$campo = "$estrato" . '_31a' ;
				$arr_salemc[$tipo][$mc_sector][$campo] = formato_n( $val1['ValorEdadmasde31'] / 1000000, 2 ) ;
			}			
		}		
	}
	ver_arr( $arr_salemc , '$arr_salemc') ;
	
	foreach ( $arr_salemc as $tipo => $val ){		
		foreach ( $val as $mc_sector => $val1 ){
			$linea = array() ;
			$linea['Tipo'] = $tipo ;
			$linea['Ubicacion'] = $mc_sector ;
			$linea = array_merge( $linea , $val1 ) ;
			$arr_printmc[] = $linea ;
			
		}
	}
	
	
	
	$n = array() ;
	$g_html = '' ;
	
	$g_html .= html_arreglo_bi( $arr_temp , 1 , $n , "Estadísticas de Mercado con corte al $f_fecha <br>UBICACION:$mi_sector <br>BARRIO: $id_barrio") ;
	$g_html .= '<br>' ;
	
	$g_html .= html_arreglo_bi( $arr_printmc , 1 , $n , "Estadísticas de metrocuadrado <br>BARRIO: $id_barrio") ;
	$g_html .= '<br>' ;
	
	$g_html .= html_arreglo_bi( $arr_pintar , 1 , $n , "Inmuebles del $id_barrio ACTIVOS al $f_fecha") ;
	$g_html .= '<br>' ;
	
	$g_html .= html_arreglo_bi( $arr_pintar2 , 1 , $n , "INACTIVOS de $id_barrio") ;
	
  	echo $g_html ;  	
  
?>