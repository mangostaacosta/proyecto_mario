<?php
/****************************************************************************
  20150113  >> metedatos.php
  Proyecto: Obsevatorio Mario
  Esta es una versión que NO ES FUNCIONAL Y NO ESTÁ EN PRODUCCIÓN ya que no tieine tan buen control de fecas con las más reciente
  Carga archivo que se baja de pagina web a la base de datos.   
  Se cargan dos tablas: t_datosweb y t_inmueble  
  Podría ser bueno ubicar un script previo que revise si hay datos en la fecha y advierta al usuario antes de sobreescribirlos
******************************************************************************/
	
	require_once 'header.php' ; 
	global $g_MiIndice ;      //esta global se usa para guardar un arreglo indexado que resume el query de t_datosweb en metedatos.php, se debería camabiar por un OBJETO 
	global $g_MiBarrios ;
	global $g_MiNoGemelo ;
	CreaIndiceM2() ;						//esta función inicializa la ariable global
	CreaBarriosBD() ;
	CreaNoGemeloBD() ;
	
	//ver_arr ( $g_MiIndice , '$g_MiIndice' ) ;

		
	if (isset( $_GET['archivo'] )){
		$elarchivo = $_GET['archivo'] ;
	}else{
		echo "Falta el nombre de archivo" ;
		die() ;
	}
	
	$archivo = "descargas/$elarchivo" ; 
	$arch_listado = $archivo ;

	if (file_exists( $arch_listado )){    
		$handle = fopen($arch_listado, "r");
		$datain = fread($handle, filesize($arch_listado));
		fclose($handle);    
		$arr_list = unserialize($datain);
	}else{
		echo "/n<br>OJO: no se encuentra archivo base de listado: $arch_listado" ;
	}

	if ( is_array($arr_list) ){		//arreglar los textos ´para evitar errores de identificación y caracteres
		//ver_arr ($arr_list, "Max" );
		foreach($arr_list as $key1 => $val1 ){
			foreach($val1 as $key2 => $val2 ){
				$val2 = trim( $val2 ) ;
				$val2 = CodificaRaros( $val2 ) ;			
				$arr_list2[$key1][$key2] = $val2 ;
			}		
		}
		
		$fallas = 0 ;
		$i_insertos = 0 ;
		$i_gemelos = 0 ;
		$i_fallas = 0 ;
		$i_cambios = 0 ;
		$i_precio = 0 ;
		$i_igual = 0 ;		
		$i_update_web = 0 ;
		$i_prec_cambiado = 0 ;
		$i_no_cambio = 0 ;		
		$i_insdatos = 0 ;
		
		foreach($arr_list2 as $key => $value){
			$fuente = 'metrocuadrado' ;
			if ( isset( $value['tipo'] )){
				$tipo = $value['tipo'] ;
			}else{
				$tipo = 'apartamento' ;  // para archivos viejos
			}			
			$fecha = $value ['fecha'] ;
			$url = $value ['url'] ;
			$idM2 = $value ['id'] ;
			$search = array('<b>','</b>') ;
			$replace = array ('','') ;
			$time_publicado = str_replace($search,$replace,trim($value ['time'] ));
			
			$direccion = $value ['direcc'] ;
			$telefono = $value ['telef']  ;
			$zona = $value ['zona'] ;
			$barrio = $value ['barrio'] ;
			$catastro = $value ['catastro'] ;
			$estrato = $value ['estrato'] ;			
			$antig = $value ['antig'] ;			
			$area = $value ['area'] ;
			$construida = $value ['construida'] ;
			$habit = ($value ['habit'] ) ;
			$banhos = $value ['banhos'] ;
			$garaj = $value ['garaj'] ;
			
			$admon = PrecioADecimal($value ['admon'] ) ;
			$precio = PrecioADecimal( $value ['precio'] ) ;			
			
			$t_new = "$direccion>$telefono>$zona>$barrio>$catastro>$estrato>$antig>$area>$construida>$habit>$banhos>$garaj>$admon>$precio"   ;
			$t_new2 = "$zona>$barrio>$catastro>$estrato>$antig>$area>$construida>$habit>$banhos>$garaj>$admon"   ;
			
			
			//Primero hay que mirar cual es el id_barrio adecuado que esté en la tabla t_barrio (depronto no hay que hacerlo siempre)
			//esta sección identifica el barrio y el id_barrio de la tabla t_barrio
			$id_barrio = CodificaRaros( strtolower( trim( $catastro ))) ;	
			
			$search = array(' ') ;
			$replace = array ('_') ;
			$id_barrio = str_replace($search,$replace, $id_barrio ) ;
			msj ( $id_barrio ) ;
			$con_barrio = 1 ;
			if ( BuscaBarrio( $id_barrio ) != 0 ){
				//nada $id_barrio OK
			}else{
				$temp =  CodificaRaros( strtolower( trim( $barrio ))) ;
				if ( $temp == '' ){
					//no hay catastro, usar el nombre del barrio
					$con_barrio = 0 ;
				}else{
					$id_barrio = $temp ;
					$id_barrio = str_replace($search,$replace, $id_barrio ) ;					
					if ( BuscaBarrio( $id_barrio ) != 0 ){
						//nada $id_barrio OK
					}else{
						$con_barrio = 0 ;
						msj ( "$id_barrio no está" ) ;
						//$id_barrio = 'NN' ;
						$id_barrio = "ZNN_$id_barrio" ;
						$fallas++ ;
					}
				}			
			}
			
			
			
			//Proceder a buscar el papá en t_inm
			
			if ( ($mid = BuscaIDM2( $idM2 )) != 0 ){  // el idM2 ya existe, luego el IM ya está creado no se requiere insertar fila nueva en t_inm
				// se procede a verificar si alguno de los campos se ha modificado
				$llave = $mid ;
				MsjE("$idM2 ya estaba creado") ;
				$chg = 0 ;
				$chgp = 0 ;
				$sql = "
					SELECT 
					identif1, identif2, precio, b_manual
					FROM t_inmueble
					WHERE id_inmueble = '$mid'  " ;
				$g_conexion->execute ($sql) ;	
				$linea = $g_conexion->fetch() ;
				ver_arr( $linea , '$linea con resultado del query' ) ;
				//$t_new = "$direccion>$telefono>$zona>$barrio>$catastro>$estrato>$antig>$area>$construida>$habit>$banhos>$garaj>$admon>$precio"   ;
				$t_old = $linea['identif1'] ; 				
				if ( strcmp( $t_old , $t_new ) != 0 ){
					MsjE( "$idM2 SI cambio en los datos:<br>OLD:$t_old<br>NEW:$t_new" ) ;
					$p_new = $precio ;
					$p_old = $linea['precio'] ; 
					if ( $linea['precio'] != $precio ) {
						MsjE( "$idM2 SI cambio en PRECIO:<br>OLD:$p_old<br>NEW:$p_new" ) ;						
						$i_precio++ ;
						$chgp = 1 ;
					}					
					if ( $linea['b_manual'] == 1 ){
						MsjE("NO se alteran datos en t_inmueble ya que tiene bandera MANUAL") ;
						$i_no_cambio++ ;
					}else{					
						$chg =1 ;
					}					
					$i_cambios++ ;
				}else{
					//nada no hay cambios perceptibles no se requiere hacer update
					$i_igual++ ;
				}
				
				if ( $chg == 1 ){		//hay modificaciones se hace el update
					$sql = "
						UPDATE t_inmueble
						SET							
							fecha = '$fecha',
							direccion = '$direccion',
							telefono = '$telefono',
							zona = '$zona',
							barrio = '$barrio',
							catastro= '$catastro',
							estrato = '$estrato',
							antiguedad_rg = '$antig',
							area_privada = '$area',
							area_construida = '$construida',
							habitaciones = '$habit',
							banhos = '$banhos',
							garajes = '$garaj',
							admon = '$admon',
							precio = '$precio'
						WHERE id_inmueble = '$mid' ";
					$g_conexion->execute ($sql) ;
					$i_update_web++ ;
					if ( $chgp == 1 ){$i_prec_cambiado++ ;}					
				}else{																	
				//no hay cambios, ó tiene la bandera manual: sólo actualizar la fecha
					$sql = "
						UPDATE t_inmueble
						SET							
							fecha = '$fecha'
						WHERE id_inmueble = '$mid' ";
					$g_conexion->execute ($sql) ;
					
				}
			}else{																		
			// el idM2 no existe, puede que exista un papa gemeleado hay que buscarlo, pero se posible que toque insertar nuevo en t_inm
				$pseudo_gemelos = 0 ;
				$sql = "
					SELECT 
						id_inmueble, idM2_ini
					FROM t_inmueble
					WHERE b_activo = 1 AND identif1='$t_new'
					" ;
				$g_conexion->execute ($sql) ;
				if ( $linea = $g_conexion->fetch() ){					
				//parece existir un papá exacto OJO: se asume que este campo es 'semi' único
				//no se requiere insertar en t_inm sólo se requiere la llave
					$llave = $linea['id_inmueble'] ;
					MsjE("$idM2 es gemelo de " . $linea['idM2_ini'] ) ;
					$i_gemelos++ ;
				}else{
					//$t_new2 = "$zona>$barrio>$catastro>$estrato>$antig>$area>$construida>$habit>$banhos>$garaj>$admon"   ;
					// se buscan posibles papas parecidos
					$sql = "
						SELECT 
							id_inmueble, idM2_ini
						FROM t_inmueble
						WHERE b_activo = 1 AND identif2='$t_new2'
						" ;
					$g_conexion->execute ($sql) ;
					$arrsalidabd = array() ;
					$arr_llaves = array() ;					
					while ( $arrsalidabd[] = $g_conexion->fetch() ) ;
					ver_arr( $arrsalidabd , '$arrsalidabd con datos de busqueda sobre identif2' ) ;
					array_pop( $arrsalidabd ) ;
					if (sizeof( $arrsalidabd ) > 0 ){  //parece existir al menos un papá posible
						//array_pop( $arrsalidabd ) ;
						foreach ( $arrsalidabd as $key => $linea ){ 
							$arr_llaves[] = array ( 'id'=>$linea['id_inmueble'] , 'M2'=>$linea['idM2_ini'] );							
						}
						$pseudo_gemelos = 1 ;
					}
					
					// Acá se procede a hacer la inserción en t_inmueble para ya tener la llave del IM					
					
					
					// ahora si el insert en t_inm
					$sql = "
						INSERT INTO t_inmueble (
						tipo_inm, 
						fecha_ini, 
						fecha, 
						direccion,						
						telefono,
						id_barrio,
						idM2_ini,
						url,
						identif1,
						identif2,
						zona,						
						barrio,
						catastro,
						admon,
						estrato,
						antiguedad_rg,
						area_privada,
						area_construida,
						habitaciones,
						banhos,
						garajes,
						precio_ini,
						precio,
						b_conbarrio
						) VALUES (
						'$tipo', 
						'$fecha',
						'$fecha',						
						'$direccion',
						'$telefono', 
						'$id_barrio',
						'$idM2',
						'$url',
						'$t_new',
						'$t_new2',
						'$zona',
						'$barrio',
						'$catastro',						
						'$admon',
						'$estrato',
						'$antig',
						'$area',
						'$construida',
						'$habit',
						'$banhos',
						'$garaj',
						'$precio',
						'$precio',
						'$con_barrio') 
						" ;
					$g_conexion->execute ($sql) ;
					$llave = $g_conexion->ultimoID () ;
					$i_insertos++ ;
					if ( $con_barrio == 0 ){
						$i_fallas++ ;
					}
					
					if ( $pseudo_gemelos == 1){
						// IF POSIBLES GEMELOS se procede a rellenar una arreglo asociativo auxiliar con los posibles papas gemelos, esté deberá ser depurado por el usuario después
						foreach ( $arr_llaves as $key=>$val ){
							//pero primero se verifica que los pseudo gemelos no estén en la lista de NO gemelos preidentificados
							$needle1 = $idM2 ;
							$needle2 = $val['M2'] ;
							//hacer la busqueda en NO gemelos
							$i = BuscaNoGemelo( $needle1,$needle2 ) ;
							
							if ( $i == 1 ){
							// la parejita está en la lista de no gemelos: no hay que incluira
								MsjE("$needle1 y $needle2 en la lista de no gemelos") ;
							}else{
							// la parejita no está en la lista, se incluye en las sospechosas
								$id_inm = $val['id'] ;
								$sql = "
									INSERT  INTO ta_pseudogemelos (
									inm_1, 
									inm_2
									) VALUES (
									$llave,
									$id_inm)						
								" ;
								$g_conexion->execute ($sql) ;							
							}							
						}
					}else{
						//nada no hay que actualizar la tabla de pseudogeemlos
					}					
				} //fin del análisis de los gemelos y su posible insercion en t_inm
			} //fin del analisis cuando $idM2 no existía en BD	
			
			// ahora si el INSERT en t_dw
			
			$sql = "
				INSERT INTO t_datosweb (
				fecha, 
				fuente, 
				url,
				idM2,
				time_publicado, 
				tipo_inm,				
				id_barrio,
				estrato,
				precio,				
				id_inmueble
				) VALUES (
				'$fecha', 
				'$fuente', 
				'$url', 
				'$idM2', 
				'$time_publicado', 
				'$tipo', 				
				'$id_barrio', 
				'$estrato', 
				'$precio', 
				'$llave') ";								
			$g_conexion->execute ($sql) ;
			$i_insdatos++ ;
		} // fin del foreach para cada registro				
	}	
	
	MsjE( "Nuevos registros en la BD t_inmueble: " . $i_insertos ) ;
	MsjE( "Gemelos no ingresados en BD t_inmueble: " . $i_gemelos ) ;
	MsjE( "Inmuebles con datos Modificados: " . $i_cambios ) ;
	MsjE( "Inmuebles con Precio Modificados: " . $i_precio ) ;
	MsjE( "Inmuebles sin modificaciones: $i_igual" ) ;
	MsjE( "Modificaciones de datos en la BD t_inmueble: " . $i_update_web ) ;
	MsjE( "Modificaciones de Precio en la BD t_inmueble: " . $i_prec_cambiado ) ;
	MsjE( "Modificaciones no realizadas en la BD: $i_no_cambio" ) ;
	MsjE( "Registros totales insertados en BD t_datosweb: $i_insdatos" ) ;
	MsjE( "Registros con Barrio sin equivalente estandarizado: $i_fallas" ) ;
?>