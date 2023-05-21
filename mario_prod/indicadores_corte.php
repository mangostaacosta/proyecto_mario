<?php
/****************************************************************************
  20150203  >> indicadores_corte.php
  Proyecto: Obsevatorio Mario
  Realiza los conasultas para sacar todos los indicadores estadísticos relevantes en una fecha de corte determinada
  adicionalmente inserta los campos correspondientes en las tablas de seguimiento histórico. Adicionalmente hace un doble chequeo para confirmar que no se
  realice una sobreecritura de datos. Puede haber dos formas de sacar los datos de precios de la tabla t_precios o de la tabla t_inmuebles, siendo la primera
  la más exacta, pero por ahora se tomará el dato de t_inmueble, por lo que se debe correr la consuta en una fecha cercana y si se repite el código con psterioridad
  los resultados cambiaran dada la actualización de precios de t_inmueble
  POST: 
	$arr_salidabd
	$arr_tabla
	$arr_salida
	$arr_salida1
	$arr_salida2	
******************************************************************************/
	
	
	require_once 'header.php' ; 
	
	$manual['tit'] = 'Corte Mensual' ;
	$manual['tex'] = 'Esta opción permite generar un corte de datos estadísticos del mercado para ser almacenados en la Base de Datos del sistema. Antes de ejecutar la funcionalidad es necesario asegurar que los datos de los inmuebles han sido completamente depurados para la fecha de corte ya que dichos datos quedan archivados en el sistema y sobreescribirlos posteriormente puede generar problemas de integridad de la información.
	
	Es recomendable generar al menos un corte al mes, idealmente con corte el día 15 del mes correspondiente. No obstante se pueden generar cortes en fechas adicionales sin alterar la funcionalidad del sistema.
	
	La página presenta un formulario para ingresar la fecha de corte y un código de usuario (esta funcionalidad sólo puede ser ejecutada por un usuario autorizado).	
	<b>Fecha Proceso (obligatorio)</b>: se debe escoger la fecha del análisis, el formato es AAAA-MM-DD
	<b>Usuario Autorizado (obligatorio)</b>: se debe ingresar el código del usuario autorizado
	
	En caso de que el código de usuario no sea correcto, la página despliega el resultado de la consulta, pero no archiva la información en el sistema. Por este motivo es conveniente que antes de archivar el resultado a la fecha de corte, se ejecute el proceso dejando el espacio del Usuario Autorizado vacío. De esta forma se puede validar que la información generada sea consistente y posteriormente sí se ejecuta la consulta ingresando el Usuario Autorizado, garantizando así la calidad de la información ya depurada.  
	
	Los resultados del corte se pueden consultar mediante la opción con la ruta <b>Operación > Intertemporal > Comparativo Mensual</b>
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Administración > Corte Mensual' ) ;
	
	MsjE('Este procedimiento sobreescribe información histórica de forma irreversible. Por favor valide con su nombre de usuario') ;
	
	//Se crea y utiliza el formulario para captruar las variables de entrada a la página
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;
	$forma->Insertar('f_usuario','Usuario Autorizado:') ;
	$forma->siempre = FALSE ;
	$forma->Imprimir() ;
	$f_fecha = $forma->Sacar('f_fecha') ;
	$usuario = $forma->Sacar('f_usuario') ;
	
	
	
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
	$tituls = array('tipo','estrato','antig','conteo','p_prom','p_min','p_max','pm2_prom','pm2_min','pm2_max','a_prom','habit_prom' ) ;	
	$arr_temp['barrio'] = $tituls ;
	$arr_result = array_merge( $arr_temp , $arr_result ) ;
	//$arr_result['barrio'] = $tituls ;
	
	ver_arr( $arr_result , 'arr_result' ) ;
	
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
	
	
?>