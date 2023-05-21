<?php
/****************************************************************************
  20150106  >> indicadores_grupo.php
  Proyecto: Obsevatorio Mario
  Genera los primeros cálculos por grupo para un corte determinado, sien embargo no imprime (depronto puede ser una función?
  OJO este archivo debe tener dos versiones, una que hace el cálculo puntual a una fecha (o la última fecha) y otro que hace un cálculo
  sobre el periode de tiempo definido (que está entre 7 y 15 días segun mi mismo, pero para esto toca ya haber cuadrado la tabla que lleva el
  sequimiento de los precios en el tiempo)
  POST: 
	$arr_salidabd
	$arr_tabla
	$arr_salida
	$arr_salida1
	$arr_salida2
	$arr_cuenta 
	$conteo_total
	$arr_tot_barrios
******************************************************************************/
	
	require_once 'header.php' ; 
	
	$manual['tit'] = 'Tablas de Información' ;
	$manual['tex'] = 'Esta opción permite presentar resúmenes estadísticos del mercado inmobiliario para los barrios de la ciudad. Adicionalmente permite que el usuario seleccione un barrio para profundizar en el detalle de la información de éste. 
	La página presenta un formulario inicial en donde se deben diligenciar los siguientes campos:
	<b>Fecha proceso (obligatorio):</b> corresponde a la fecha que se quiere revisar en la base de datos, los datos que se presentan en las tablas corresponden a los inmuebles que estaban vigentes en las fechas cercanas a la ingresada en este campo.
	<b>Escoja campo (obligatorio):</b> corresponde al tipo de infomración que se quiere visualizar, las opciones incluidas son:
	precio > valor de los inmuebles
	precio_metro > valor por metro cuadrado de los inmuebles
	area_privada > datos del área privada de los inumbeles, generalmente es más fiable que el dato de área construida
	area_construida > datos del área construida de los inmuebles
	administración > valor de la administración	
	<b>Ver todos los activos? (opcional):</b> Generalmente se debe utilizar el valor si, ya que permite ver los inmuebles vigentes alrededor de la fecha. La opción "no" restringe únicamente a los inmuebles cuyo último dato corresponde a la fecha ingresada en el primer campo.
	
	Posteriormente se presentan tablas que incluye los siguientes campos:
	<b>Ubicación:</b> corresponde a la zona y sector del barrio
	<b>id_barrio:</b> es el identificador único del barrio, por este motivo se escribe enteramente en minpuscular y con raya al piso "_" para separa las palabras (e.g. la_castellana)
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
	
	La tabla muestra únicamente los datos para los que se tiene información en la fecha solicitada. Por lo que los espacios vacíos significa que no había oferta para el barrio en la columna de estrato y antigüedad correspondiente.
	Al presionar el enlace que identifica cada barrio, se pasa a la opción "Listado barrio" que muestra la información del detalle de los inmuebles vigentes en la fecha.	
	' ;
	
	//echo VentanaHelp( $manual ) ;
	
	//Se crea y utiliza el formulario para capturar las variables de entrada a la página
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->Insertar('f_campo','Escoja campo (e.g. precio / precio_metro):','precio_metro') ;
	$forma->Insertar('decision','Ver todos los activos? (si/no)','si') ;
	$forma->siempre = TRUE ;
	$forma->Imprimir() ;
	$f_fecha = $forma->Sacar('f_fecha') ;
	$f_campo = $forma->Sacar('f_campo') ;
	$f_decision = $forma->Sacar('decision') ;
	
	$min_conteo = 0 ;
	
	
	//Para decidir si se hace la búsqueda por fecha puntual o por los "activos"
	if ( $f_decision == 'no'){	
	// inmuebes que tengan datos actualizados a la fecha puntual
		$sql = "
			SELECT t_barrio.zona, t_barrio.sector, t_inmueble.id_barrio, estrato, antiguedad_rg, COUNT($f_campo) AS cuenta, AVG($f_campo) AS prom, MIN($f_campo) AS mini, MAX($f_campo) AS maxi
			FROM t_inmueble
			LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
			WHERE b_normal = 1
			AND t_inmueble.fecha = '$f_fecha' 
			GROUP BY id_barrio, estrato, antiguedad_rg
			ORDER BY id_barrio, estrato, antiguedad_rg DESC
		" ;
	}else{
	// inmuebles vigentes
		ActivarFechaBD( $f_fecha ) ;
		
		//$where = " AND (fecha_ini <= '$f_fecha') AND ((fecha_fin > '$f_fecha') OR (fecha_fin IS NULL)) " ;
		$sql = "
			SELECT t_barrio.zona, t_barrio.sector, t_inmueble.id_barrio, estrato, antiguedad_rg, COUNT($f_campo) AS cuenta, AVG($f_campo) AS prom, MIN($f_campo) AS mini, MAX($f_campo) AS maxi
			FROM t_inmueble
			LEFT JOIN t_barrio ON t_inmueble.id_barrio = t_barrio.id_barrio
			WHERE b_activo = 1 AND b_normal = 1  			
			GROUP BY id_barrio, estrato, antiguedad_rg
			ORDER BY zona DESC,sector,id_barrio, estrato, antiguedad_rg
		" ;
	}
	//20171007: cambio del order by para que los ZNN aparezcan al final
	
	$g_conexion->execute ($sql) ;
	$arr_salidabd = array() ;
	while ($arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	
	//inicialización de los arreglos donde se guardan los datos de resultados para mpresetnar al usuario
	$arr_nada['prom'] = '' ; 
	$arr_nada['cuenta'] = '' ; 
	
	$arr_antig['new'] = $arr_nada ;
	$arr_antig['0a10'] = $arr_nada ;
	$arr_antig['10a20'] = $arr_nada ;
	$arr_antig['20a'] = $arr_nada ;
	$arr_estrato['4'] = $arr_antig ;
	$arr_estrato['5'] = $arr_antig ;
	$arr_estrato['6'] = $arr_antig ;
	$arr_estrato['zona'] = '' ;
	$arr_estrato['sector'] = '' ;
	
	$arr_temp = array( 'new' =>  0 , '0a10' =>  0 , '10a20' =>  0 , '20a' =>  0 ) ;
	for ( $i = 0 ; $i<7 ; $i++ ){
		$arr_cuenta[$i] = array_merge( array('Estrato' => $i ) , $arr_temp ) ;
	}
	ver_arr( $arr_cuenta , 'arr_cuenta') ;
	//$arr_cuenta = array( '0' =>  $array('Estrato' => 0 ) , '1' =>  $arr_temp , '2' =>  $arr_temp , '3' =>  $arr_temp , '4' =>  $arr_temp , '5' =>  $arr_temp , '6' =>  $arr_temp ) ;
	//$arr_cuenta = array( '0' =>  $arr_temp , '1' =>  $arr_temp , '2' =>  $arr_temp , '3' =>  $arr_temp , '4' =>  $arr_temp , '5' =>  $arr_temp , '6' =>  $arr_temp ) ;
	$arr_tot_barrios = array() ;
	$conteo_total = 0 ;
	
	//recorrer resultados de la base de datos para crear un arreglo asociativo agrupado acorde con barrio,estrato, antigüedad y con los datos del barrio inicializados
	foreach ( $arr_salidabd as $key => $val ){
		$zona = $val['zona'] ;
		$sector = $val['sector'] ;
		$id_barrio = $val['id_barrio'] ;
		$estrato = $val['estrato'] ;
		$antig = $val['antiguedad_rg'] ;
		$cuenta = $val['cuenta'] ;
		$prom = $val['prom'] ;
		$mini = $val['mini'] ;
		
		//verificar si ya se inicializó la información para el barrio correspondiente
		if ( !(isset( $arr_tabla[$id_barrio] ))){
			$arr_tabla[$id_barrio] = $arr_estrato ;
			$arr_tabla[$id_barrio]['zona'] = $val['zona'] ;
			$arr_tabla[$id_barrio]['sector'] = $val['sector'] ;
		}else{
			//nada  //ya está incializado hay más elementos
		}
		
		//actualizar los conteos
		if ( $val['cuenta'] > $min_conteo ) {
			$arr_tabla[$id_barrio][$estrato][$antig] = $val ;
			$arr_cuenta[$estrato][$antig] += $cuenta ;
			$arr_tot_barrios[$id_barrio] += $cuenta ;
			$conteo_total += $cuenta ;
		}		
	}
	
	//Recorrer el arreglo de salida de la BD para extraer y formatear la informción de la BD más relevante que es estrato >= 4
	//POST: $arr_salida contiene la información por cada barrio incluyendo el promedio y los conteos 
	$arr_salida = array() ;
	foreach ( $arr_tabla as $id_barrio => $val1 ){
		//$linea['id_barrio'] = $id_barrio ;		
		$linea['______________Ubicacion______________'] = $arr_tabla[$id_barrio] ['zona'] . ' >> ' . $arr_tabla[$id_barrio] ['sector'] ;
		$linea['id_barrio'] = "<a href=\"vecinos.php?f_idbarrio=$id_barrio&f_fecha=$f_fecha&f_decision=$f_decision\">$id_barrio</a>" ;
		$linea['4_new'] = $arr_tabla[$id_barrio] ['4']['new']['prom'] / 1000000 ;
		$linea['4_0a10'] = $arr_tabla[$id_barrio] ['4']['0a10']['prom'] / 1000000 ;
		$linea['4_10a20'] = $arr_tabla[$id_barrio] ['4']['10a20']['prom'] / 1000000 ;
		$linea['4_20a'] = $arr_tabla[$id_barrio] ['4']['20a']['prom'] / 1000000 ;
		$linea['5_new'] = $arr_tabla[$id_barrio] ['5']['new']['prom'] / 1000000 ;
		$linea['5_0a10'] = $arr_tabla[$id_barrio] ['5']['0a10']['prom'] / 1000000 ;
		$linea['5_10a20'] = $arr_tabla[$id_barrio] ['5']['10a20']['prom'] / 1000000 ;
		$linea['5_20a'] = $arr_tabla[$id_barrio] ['5']['20a']['prom'] / 1000000 ;
		$linea['6_new'] = $arr_tabla[$id_barrio] ['6']['new']['prom'] / 1000000 ;
		$linea['6_0a10'] = $arr_tabla[$id_barrio] ['6']['0a10']['prom'] / 1000000 ;
		$linea['6_10a20'] = $arr_tabla[$id_barrio] ['6']['10a20']['prom'] / 1000000 ;
		$linea['6_20a'] = $arr_tabla[$id_barrio] ['6']['20a']['prom'] / 1000000 ;
		//ver_arr( $linea ) ;
		foreach ( $linea as $key => $val2 ){
			$linea2[$key] =  formato_n( $val2 , 2 ) ;
		}
		$linea2['4_new'] .= "({$arr_tabla[$id_barrio] ['4']['new']['cuenta']})" ;
		$linea2['4_0a10'] .= "({$arr_tabla[$id_barrio] ['4']['0a10']['cuenta']})" ;
		$linea2['4_10a20'] .= "({$arr_tabla[$id_barrio] ['4']['10a20']['cuenta']})" ;
		$linea2['4_20a'] .= "({$arr_tabla[$id_barrio] ['4']['20a']['cuenta']})" ;
		$linea2['5_new'] .= "({$arr_tabla[$id_barrio] ['5']['new']['cuenta']})" ;
		$linea2['5_0a10'] .= "({$arr_tabla[$id_barrio] ['5']['0a10']['cuenta']})" ;
		$linea2['5_10a20'] .= "({$arr_tabla[$id_barrio] ['5']['10a20']['cuenta']})" ;
		$linea2['5_20a'] .= "({$arr_tabla[$id_barrio] ['5']['20a']['cuenta']})" ;
		$linea2['6_new'] .= "({$arr_tabla[$id_barrio] ['6']['new']['cuenta']})" ;
		$linea2['6_0a10'] .= "({$arr_tabla[$id_barrio] ['6']['0a10']['cuenta']})" ;
		$linea2['6_10a20'] .= "({$arr_tabla[$id_barrio] ['6']['10a20']['cuenta']})" ;
		$linea2['6_20a'] .= "({$arr_tabla[$id_barrio] ['6']['20a']['cuenta']})" ;
		foreach ( $linea2 as $key => $val2 ){
			if ( $val2 == '0.00()' ){
				$linea2[$key] =  '' ;
			}			
		}
		//ver_arr( $linea2 ) ;
		$arr_salida[$id_barrio] = $linea2 ;
	}

	//$n = array() ;
	//$g_html .= html_arreglo_bi( $arr_salida , 1 , $n , "Estadísticas por Barrio Version HORIZ") ;
	//$g_html .= '<br>' ;
	
	
	//ahora promedios puros
	//POST: $arr_salida1 contiene la información por cada barrio solo de promedios 	
	$arr_salida1 = array() ;
	foreach ( $arr_tabla as $id_barrio => $val1 ){
		$linea['______________Ubicacion______________'] = $arr_tabla[$id_barrio] ['zona'] . ' >> ' . $arr_tabla[$id_barrio] ['sector'] ;
		$linea['id_barrio'] = $id_barrio ;
		$linea['4_new'] = $arr_tabla[$id_barrio] ['4']['new']['prom'] ;
		$linea['4_0a10'] = $arr_tabla[$id_barrio] ['4']['0a10']['prom'] ;
		$linea['4_10a20'] = $arr_tabla[$id_barrio] ['4']['10a20']['prom'] ;
		$linea['4_20a'] = $arr_tabla[$id_barrio] ['4']['20a']['prom'] ;
		$linea['5_new'] = $arr_tabla[$id_barrio] ['5']['new']['prom'] ;
		$linea['5_0a10'] = $arr_tabla[$id_barrio] ['5']['0a10']['prom'] ;
		$linea['5_10a20'] = $arr_tabla[$id_barrio] ['5']['10a20']['prom'] ;
		$linea['5_20a'] = $arr_tabla[$id_barrio] ['5']['20a']['prom'] ;
		$linea['6_new'] = $arr_tabla[$id_barrio] ['6']['new']['prom'] ;
		$linea['6_0a10'] = $arr_tabla[$id_barrio] ['6']['0a10']['prom'] ;
		$linea['6_10a20'] = $arr_tabla[$id_barrio] ['6']['10a20']['prom'] ;
		$linea['6_20a'] = $arr_tabla[$id_barrio] ['6']['20a']['prom'] ;
		
		$linea2 = $linea;
		/*
		foreach ( $linea as $key => $val2 ){
			$linea2[$key] =  formato_n( $val2 , 0 ) ;
		}
		*/
		$arr_salida1[$id_barrio] = $linea2 ;
	}

	//$g_html .= html_arreglo_bi( $arr_salida1 , 1 , $n , "Conteos por Barrio Version HORIZ") ;
	//$g_html .= '<br>' ;	
	
	
	//ahora conteos
	//POST: $arr_salida1 contiene la información por cada barrio solo de conteos 	
	$arr_salida2 = array() ;
	foreach ( $arr_tabla as $id_barrio => $val1 ){
		$linea['______________Ubicacion______________'] = $arr_tabla[$id_barrio] ['zona'] . ' >> ' . $arr_tabla[$id_barrio] ['sector'] ;
		$linea['id_barrio'] = $id_barrio ;
		$linea['4_new'] = $arr_tabla[$id_barrio] ['4']['new']['cuenta'] ;
		$linea['4_0a10'] = $arr_tabla[$id_barrio] ['4']['0a10']['cuenta'] ;
		$linea['4_10a20'] = $arr_tabla[$id_barrio] ['4']['10a20']['cuenta'] ;
		$linea['4_20a'] = $arr_tabla[$id_barrio] ['4']['20a']['cuenta'] ;
		$linea['5_new'] = $arr_tabla[$id_barrio] ['5']['new']['cuenta'] ;
		$linea['5_0a10'] = $arr_tabla[$id_barrio] ['5']['0a10']['cuenta'] ;
		$linea['5_10a20'] = $arr_tabla[$id_barrio] ['5']['10a20']['cuenta'] ;
		$linea['5_20a'] = $arr_tabla[$id_barrio] ['5']['20a']['cuenta'] ;
		$linea['6_new'] = $arr_tabla[$id_barrio] ['6']['new']['cuenta'] ;
		$linea['6_0a10'] = $arr_tabla[$id_barrio] ['6']['0a10']['cuenta'] ;
		$linea['6_10a20'] = $arr_tabla[$id_barrio] ['6']['10a20']['cuenta'] ;
		$linea['6_20a'] = $arr_tabla[$id_barrio] ['6']['20a']['cuenta'] ;
		$linea2 = array();
		foreach ( $linea as $key => $val2 ){
			$linea2[$key] =  formato_n( $val2 , 0 ) ;
		}
		$arr_salida2[$id_barrio] = $linea2 ;
	}

	//$g_html .= html_arreglo_bi( $arr_salida2 , 1 , $n , "Conteos por Barrio Version HORIZ") ;
	//$g_html .= '<br>' ;	
	
	
	//$g_html .= html_arreglo_bi( $arr_salidabd , 1 , $n , "Estadísticas por Barrio") ;
	//$g_html .= '<br>' ;	
	
	//echo $g_html ;  	
?>