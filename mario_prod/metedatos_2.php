<?php
/****************************************************************************
  20150129  >> metedatos_2.php
  Proyecto: Obsevatorio Mario
  Carga archivo que se baja de pagina web a la base de datos. Es una mejora rspecto a metedatos.php dado que acá se trata de lograr consistencia en el tiempo 
  Se cargan dos tablas: t_datosweb y t_inmueble y t_precios
  Podría ser bueno ubicar un script previo que revise si hay datos en la fecha y advierta al usuario antes de sobreescribirlos
******************************************************************************/
	
	require_once 'header.php' ; 
	include "manual_captura_autom.php" ;
	echo TituloPagina( 'Operación > Captura Automática > Insertar a BD' ) ;
	
	global $g_MiIndice ;		//variable global se usa para guardar un arreglo indexado que resume el query de t_datosweb en metedatos.php
	global $g_MiBarrios ;		//variable global con el listado de barrios del aplicativo
	global $g_MiNoGemelo ;		//variable global con listado de parejas de barrios que no son gemelos
	global $g_MiSiGemelo ;		//variable global con listado de parejas de barrios que no son gemelos
	
	CreaIndiceM2() ;			//inicializa $g_MiIndice
	CreaBarriosBD() ;			//inicializa $g_MiBarrios
	CreaNoGemeloBD() ;			//inicializa $g_MiNoGemelo
	CreaSiGemeloBD() ;			//inicializa $g_MiSiGemelo
	
	//ver_arr ( $g_MiIndice , '$g_MiIndice' ) ;
	
	//creación del formualrio
	$forma = new Formulario() ;	
	$forma->Insertar('f_archivo','Archivo a procesar:','resultM2.dat') ;	
	$forma->siempre = FALSE ;
	$forma->Imprimir() ;
	$elarchivo = $forma->Sacar('f_archivo') ;	
	
	//buscar, abrir y extraer info del archivo que contiene el listado de inmuebles externos a insertar en la BD
	$archivo = "$g_ruta/$elarchivo" ; 
	$arch_listado = $archivo ;

	if (file_exists( $arch_listado )){    
		$handle = fopen($arch_listado, "r");
		$datain = fread($handle, filesize($arch_listado));
		fclose($handle);     
		$arr_list = unserialize($datain);
	}else{
		echo "/n<br>OJO: no se encuentra archivo base de listado: $arch_listado" ;
	}	
	
/*
Lógica del asincrónico:
f1 es la fecha del inmuebel que estoy metiendo, fi es la fecha_ini en el registro de t_inm, ft es la fecha en t_inm, fpf es la última fecha en t_precios 
> 1. Se busca IDM2 en la t_inm "activos", ESTA?
{{>> SI: f1>ft? (es nueva información?)
>>>> SI: Se revisan, avisan y actualizan los datos geométricos. Se compara Pt con P1 si cambian se avisa y se inserta registro en t_precios, en todo caso se actualiza Pt 
>>>> NO: f1>fi? (es un dato ancestral)
				SI: ¿¿Se compara Pt con Pi NO se actualiza geometría, si Pi cambia se avisa y actualiza se inserta registro en t_precios en todo caso??
				NO: entonces fi<f1<ft (es un dato intermedio): NO se actualiza geometría, se compara f1 con las fechas de t_precio
					f1>fpf: si Ppf <> P1 (NO se actualiza en Pt, porque se asume que Pt corresponde a una actualizacion poterior que se hizo en ft) y se inserta registro en t_precios 
					f1<fpf: si Ppf <> P1 se inserta registro en t_precios (si hay para la fecha, se actualiza)}} : : FUNCTION ActualizarInmueble()

>> NO: 2. Se busca gemelo de datos geométricos+precio(gemelo exacto), ESTÁ?
>>>> SI: ActualizarInmueble()
>>>> NO: 3. Se busca en el listado de gemelos manuales, ESTÁ?
>>>>>> SI: ActualizarInmueble()
>>>>>> NO: 4. Se busca un pseudogemelo, HAY?
>>>>>>>> SI: InsertarInmueble en t_inm y se actuliza la tabla de posibles_gemelos para verificacion manual, también se crea registro en t_precio
>>>>>>>> NO: 5. Se inserta nuevo resgistro en t_inm con fecha_ini=fecha. Se crea registo en t_precio	
*/	
	
	//$arr_campos['texto'] = ;
	//$arr_campos['numeros'] = ;

	//inicialización de arreglo con contadores de diferentes situaciones al hacer la inserción en la BD
	$arr_cont['ins_inm'] = 0 ;		//no. de registros insertados en la tabla t_inmueble
	$arr_cont['ins_dw'] = 0 ;		//no. de registros insertados en la tabla t_datosweb
	$arr_cont['yaestaba'] = 0 ;		//no. de registros que no se insertan porque ya estban en la BD
	$arr_cont['gemelo'] = 0 ;		//no. de registros que no se insertan porque son gemelos de uno en la BD
	$arr_cont['gemmanual'] = 0 ;	//no. de registros que no se insertan porque han sido marcados manualmente como gemelos de uno en la BD
	$arr_cont['pseudgem'] = 0 ;		//no. de registros insertados, identificados como similares a otros en BD 'pseudogemelos'
	$arr_cont['sinbarr'] = 0 ;		//no. de registros que no tiene barrio equivalente en la tabla t_barrios
	$arr_cont['prmodif'] = 0 ;		//no. de registros que estaban en BD y se detectó cambio de precio
	$arr_cont['prFmodif'] = 0 ;		//no. de registros que estaban en BD y se detectó cambio de precio y la fecha es la última actualización
	$arr_cont['datmodif'] = 0 ;		//no. de registros que estaban en BD y se detectó cambio en datos distintos a precio
	$arr_cont['datrech'] = 0 ;		//no. de registros que tienen cambios pero no se actualizan en BD, normalmente los que están marcados como manuales (b_manual = 1)
	$arr_cont['urlmodif'] = 0 ;		//no. de registros que estaban en BD y se detectó cambio en la URL
	$arr_cont['prrech'] = 0 ;		//no. de registros que tienen cambios en precio pero no se actualizan en BD, normalmente los que están marcados como manuales (b_manual = 1)
	$arr_cont['nomodif'] = 0 ;		//no. de registros que estaban en BD y no tiene cambios
	$arr_cont['nomodifpr'] = 0 ;	//no. de registros que estaban en BD y no tiene cambios en precio
	$arr_cont['actfecha'] = 0 ;		//no. de registros que estaban en BD y no tiene cambios y por lo tanto sólo se actualizó la fecha
	$conteo = 0 ;					//no. de registros total que se trataron de insertar
	
	//inicialización de caontadores (versión vieja ya no se usan)
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
	
	$psfecha = $arr_list[0]['fecha'] ;	//extrae la fecha del primer registro (se asume que todas las demás son iguales)
	ActivarFechaBD_Rango( $psfecha ) ;	//arreglar formato de fecha
			
	if ( is_array($arr_list) ){		//arreglar los textos para evitar errores de identificación y caracteres
		
		//se invierte el arreglo para que la ultima actualiización en la BD sea de los datos más recientes
		$arr_list = array_reverse( $arr_list ) ;
		
		foreach($arr_list as $key1 => $val1 ){
			foreach($val1 as $key2 => $val2 ){
				$val2 = trim( $val2 ) ;
				$val2 = CodificaRaros( $val2 ) ;
				if ( $key2 =='barrio' ){					//20160217: se intoruduce esta línea para quitar los guiones únicamente del barrio	
					$val2 = str_replace('-',' ', $val2) ;
				}
				$arr_list2[$key1][$key2] = $val2 ;
			}		
		}
		
		//recorrer el arreglo haciendo todas las revisiones preliminares para insertar o actualizar la informatión en t_inmueble 
		foreach($arr_list2 as $key => $value){
			MsjE("<br>Analizando registro $conteo") ;
			ver_arr( $value , 'registro serializado' ) ;
			$conteo++ ;
			$fuente = 'metrocuadrado' ;		//OJO: revisar cuando haya mas fuentes
			
			//crear los nombres de los campos en el arreglo para identificarlos correctamente en al inserción
			//y limpiar los datos númericos y con caracteres especiales para que tengan formato adecuado para la BD
			if ( isset( $value['tipo'] )){
				$tipo = $value['tipo'] ;
			}else{
				$tipo = 'apartamento' ;  // para archivos viejos
			}
			$value['tipo_inm'] = $tipo ;
			
			$fecha = $value ['fecha'] ;			
			$idM2 = $value ['id'] ;
			$value['idM2_ini'] = $idM2 ;
			
			$search = array('<b>','</b>') ;
			$replace = array ('','') ;
			$time_publicado = str_replace($search,$replace,trim($value ['time'] ));
			$value['time'] = $time_publicado ;
			
			$direccion = $value ['direcc'] ;
			$value['contacto'] = $direccion ;	//la dirección se asigna a contacto porque generalmente es la dirección de la inmobiliaria
			$telefono = $value ['telef']  ;
			$value['telefono'] = $telefono ;
			$url = $value ['url'] ;
			$zona = $value ['zona'] ;			
			$barrio = $value ['barrio'] ;		//20170725: retorno normalidad
			$catastro = $value ['catastro'] ;   //20170725: retorno normalidad
			//$barrio = $value ['barrio'] ;		//20160126: como catastro ya no está en M2 se invierte la funcionalidad
			//$catastro = $value ['catastro'] ;   //20160126: como catastro ya no está en M2 se invierte la funcionalidad
			//$catastro = $value ['barrio'] ;		//20160126: como catastro ya no está en M2 se invierte la funcionalidad
			//$barrio = $value ['barrio'] ;   //20160704: como catastro ya no está en M2 pues barrio es barrio porque se estaba perdiendo
			//$barrio = $value ['catastro'] ;   //20160126: como catastro ya no está en M2 se invierte la funcionalidad			
			$estrato = $value ['estrato'] ;
			$antig = $value ['antig'] ;
			$value['antiguedad_rg'] = $antig ;
			$area = $value ['area'] ;
			$value['area_privada'] = $area ;
			$construida = $value ['construida'] ;
			$value['area_construida'] = $construida ;
			$habit = $value ['habit'] ;
			$value['habitaciones'] = $habit ;
			$banhos = $value ['banhos'] ;
			$garaj = $value ['garaj'] ;
			$value['garajes'] = $garaj ;
			$ascensor = $value ['ascensor'] ;	//20160704
			$value['ascensor'] = $ascensor ;	//20160704		
			
			$admon = PrecioADecimal($value ['admon'] ) ;
			$value ['admon'] = $admon ;
			$precio = PrecioADecimal( $value ['precio'] ) ;			
			$value ['precio'] = $precio ;
			
			//Primero hay que mirar cual es el id_barrio adecuado que esté en la tabla t_barrio
			//esta sección identifica el barrio y el id_barrio de la tabla t_barrio
			
			$id_barrio = CodificaRaros( strtolower( trim( $catastro ))) ; //20160126: ya no viene la información de catastro

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
			$value['id_barrio'] = $id_barrio ;
			
			// se crean dos variables con conjunto de datos que permiten comparar que tan parecidos son los registros a los que ya están en la BD
			$t_new = "$id_barrio>$zona>$barrio>$catastro>$estrato>$antig>$area>$construida>$habit>$banhos>$garaj>$admon>$direccion>$telefono" ;
			$t_new2 = "$id_barrio>$zona>$barrio>$catastro>$estrato>$antig>$area>$construida>$habit>$banhos>$garaj>$admon" ;
			$value['identif1'] = $t_new ;
			$value['identif2'] = $t_new2 ;
			
			ver_arr( $value , '$value después de arreglar caracteres de barrio/catastro' ) ;
			
			//Proceder a buscar gemelos en t_inm
			
			//1. Se busca IDM2 en la t_inm "activos", ESTA?
			//NO: 3. Se busca en el listado de gemelos manuales, ESTÁ? (la búsqueda basada en t_datosweb es 2 en 1
			// Se puede emepzar a ver t_datosweb como una tabla asociativa de IDs que están relacionadas con un registro en t_inmueble. Y que además en la depuración se podría usar para validar qué URL está vigente
			if ( ($mid = BuscaIDM2( $idM2 )) != 0 ){  // el idM2 ya existe, luego el IM ya está creado no se requiere insertar fila nueva en t_inm
				$llave = $mid ;
				$arr_cont['yaestaba']++ ;
				MsjE("$idM2 ya estaba analizado") ;
				ActualizarInmueble( $mid , $value ) ;
				$inserta_dw = 0 ;
			}else{				
			// NO: 2. Se busca gemelo de datos geométricos+precio(gemelo exacto), ESTÁ?																		
			// el idM2 no existe, puede que exista un papa gemeleado hay que buscarlo, pero se posible que toque insertar nuevo en t_inm
				$inserta_dw = 1 ;
				$pseudo_gemelos = 0 ;
				$sql = "
					SELECT 
						id_inmueble, idM2_ini
					FROM t_inmueble
					WHERE b_activo = 1 AND identif1='$t_new'
					" ;
				$g_conexion->execute ($sql) ;
				if ( $linea = $g_conexion->fetch() ){
				//SI: ActualizarInmueble()
				//parece existir un papá exacto OJO: se asume que este campo es 'semi' único
				//no se requiere insertar en t_inm sólo se requiere la llave
					$llave = $linea['id_inmueble'] ;
					MsjE("$idM2 es gemelo de " . $linea['idM2_ini'] ) ;
					$i_gemelos++ ;
					$arr_cont['gemelo']++ ;
					ActualizarInmueble( $linea['id_inmueble'] , $value ) ;
					//al final se actualizara t_datosweb, queando lista tabla gemelos
				}else{
				//NO: 3. Se busca en el listado de gemelos manuales, ESTÁ?
				
					$id_gemelo = TraeGemelo( $idM2 ) ;
					if ( $id_gemelo > 0 ){
					//SI: ActualizarInmueble()
						$llave = $id_gemelo ;
						MsjE("$idM2 es gemelo MANUAL del id_inmueble " . $id_gemelo ) ;
						$i_gemelos++ ;
						$arr_cont['gemmanual']++ ;
						ActualizarInmueble( $id_gemelo , $value ) ;					
					}else{
					//NO: 4. Se busca un pseudogemelo, HAY?				
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
								$arr_llaves[] = array ( 'id'=>$linea['id_inmueble'] , 'M2'=>$linea['idM2_ini'] ) ;
								
							}
							$pseudo_gemelos = 1 ;
						}
						
						// Acá se procede a hacer la inserción en t_inmueble para ya tener la llave del IM						
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
							ascensor,
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
							'$ascensor',
							'$precio',
							'$precio',
							'$con_barrio') 
							" ;
						$g_conexion->execute ($sql) ;
						$llave = $g_conexion->ultimoID () ;
						$i_insertos++ ;
						$arr_cont['ins_inm']++ ;
						if ( $con_barrio == 0 ){
							$arr_cont['sinbarr']++ ;
							$i_fallas++ ;
						}
						
						Actualiza_t_precios( $llave , $fecha , $precio , $url ) ;	
						
						if ( $pseudo_gemelos == 1){
							// IF POSIBLES GEMELOS se procede a rellenar una arreglo asociativo auxiliar con los posibles papas gemelos, esté deberá ser depurado por el usuario después
							foreach ( $arr_llaves as $key=>$val ){
								//pero primero se verifica que los pseudo gemelos no estén en la lista de NO gemelos preidentificados
								$needle1 = $idM2 ;
								$needle2 = $val['M2'] ;
								//hacer la busqueda en NO gemelos
								$i = BuscaNoGemelo( $needle1 , $needle2 ) ;
								
								if ( $i == 1 ){
								// la parejita está en la lista de no gemelos: no hay que incluira
									MsjE("$needle1 y $needle2 en la lista de no gemelos") ;
								}else{
								// la parejita no está en la lista, se incluye en las sospechosas
									$id_inm = $val['id'] ;
									$sql = "
										INSERT INTO ta_pseudogemelos (
										inm_1, 
										inm_2
										) VALUES (
										$llave,
										$id_inm)						
									" ;
									$g_conexion->execute ($sql) ;
									$arr_cont['pseudgem']++ ;
								}							
							}
						}else{
							//nada no hay que actualizar la tabla de pseudogeemlos
						}
					}//fin del análisis de los gemelos y su posible insercion en t_inm
				}//fin del análisis de gemelos 
			} //fin del analisis cuando $idM2 no existía en BD	
			
			// ahora si el INSERT en t_dw	
			if ( $inserta_dw == 1 ){
			//con este IF esta tabla queda senciallamente como una ta_ asociacion de gemelos_exactos que se van automatizando ya que sólo los llena una vez, 
			//no se guarda histórico porque se comprobo que la BD crece demasiado rápido con información que no resulta relevante
			
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
				$arr_cont['ins_dw']++ ;
			}
		} // fin del foreach para cada registro				
	}

	echo '<br>' ;
	
	MsjE( "Registros procesados: {$conteo}" ) ;
	//MsjE( "OLD Nuevos registros en la BD t_inmueble: " . $i_insertos ) ;
	MsjE( "Nuevos registros en la BD t_inmueble: {$arr_cont['ins_inm']}" ) ;
	//MsjE( "OLD Registros totales insertados en BD t_datosweb: $i_insdatos" ) ;
	MsjE( "Registros totales insertados en BD t_datosweb: {$arr_cont['ins_dw']}" ) ;
	
	MsjE( "Inmuebles que ya habían sido analizados: {$arr_cont['yaestaba']}" ) ;	
	//MsjE( "OLD Gemelos no ingresados en BD t_inmueble: " . $i_gemelos ) ;
	MsjE( "Gemelos Exactos no ingresados en BD t_inmueble: {$arr_cont['gemelo']}" ) ;
	MsjE( "Gemelos Manuales no ingresados en BD t_inmueble: {$arr_cont['gemmanual']}" ) ;
	MsjE( "Pseudo Gemelos SI ingresados en BD t_inmueble: {$arr_cont['pseudgem']}" ) ;
	
	//MsjE( "OLD Inmuebles con datos Modificados: " . $i_cambios ) ;
	MsjE( "Inmuebles con datos Modificados: {$arr_cont['datmodif']}" ) ;
	MsjE( "Modificaciones de datos no realizadas: {$arr_cont['datrech']}" ) ;
	MsjE( "Inmuebles con info de URL Modificada: {$arr_cont['urlmodif']}" ) ;
		
	//MsjE( "OLD Inmuebles con Precio Modificados: " . $i_precio ) ;
	MsjE( "Inmuebles con Precio Modificado: {$arr_cont['prmodif']}" ) ;
	MsjE( "Inmuebles con Precio Final Modificado: {$arr_cont['prFmodif']}" ) ;
	MsjE( "Modificaciones de Precio no realizadas: {$arr_cont['prrech']}" ) ;
	MsjE( "Inmuebles sin Precio modificado: {$arr_cont['nomodifpr']}" ) ;
	
	//MsjE( "OLD Inmuebles sin modificaciones: $i_igual" ) ;
	MsjE( "Inmuebles sin modificaciones: {$arr_cont['nomodif']}" ) ;
	//MsjE( "OLD Modificaciones de datos en la BD t_inmueble: " . $i_update_web ) ;
	//MsjE( "OLD Modificaciones de Precio en la BD t_inmueble: " . $i_prec_cambiado ) ;
	//MsjE( "OLD Modificaciones no realizadas en la BD: $i_no_cambio" ) ;
	
	//MsjE( "OLD Registros con Barrio sin equivalente estandarizado: $i_fallas" ) ;
	MsjE( "Registros con Barrio sin equivalente estandarizado: {$arr_cont['sinbarr']}" ) ;
	
	MsjE( "Actualizaciones de fecha en BD inmuebles: {$arr_cont['actfecha']}" ) ;
	
	
	function ActualizarInmueble( $id_inmueble, $arr_datos_inm ){
	// esta funcion asume que el inmueble está activo en t_inm
	// también asume que los campos de $arr_datos_inm viene ya prelimpiados para cotejar con datos de BD t_inmueble
	
		global $g_conexion ;
		global $arr_cont ;
		
		msj('en ActualizarInmueble') ;
		$aa = $arr_datos_inm ;
		
		$sql = "SELECT fecha_ini,fecha,precio_ini,precio FROM t_inmueble WHERE id_inmueble='$id_inmueble'" ;
		$g_conexion->execute ($sql) ;
		$linea = $g_conexion->fetch() ;
		ver_arr( $linea , '$linea con resultado del query' ) ;
		
		if ( $aa['fecha'] >= $linea['fecha'] ){
		//SI: f1>ft? (es nueva información?)
		//SI: Se revisan, avisan y actualizan los datos geométricos. Se compra Pt con P1 si cambian se avisa se actualiza Pt y se inserta registro en t_precios 
			ActualizaGoemetria( $id_inmueble , $aa ) ;
			ActualizaPrecio( $id_inmueble , $aa ) ;
			$arr_cont['actfecha']++ ;
			if ( $aa['precio'] != $linea['precio'] ){
				$p_new = $aa['precio'] ;
				$p_old = $linea['precio'] ; 
				$id = $aa['idM2_ini'] ;
				MsjE( "$id SI cambio en PRECIO:<br>OLD:$p_old<br>NEW:$p_new" ) ;
				$arr_cont['prmodif']++ ;
			}else{
				$arr_cont['nomodifpr']++ ;
			}
		}elseif ( $aa['fecha'] < $linea['fecha_ini'] ){
		//NO: f1>fi? (es un dato ancestral)				
			ActualizaPrecioIni( $id_inmueble , $aa ) ;
			$arr_cont['nomodif']++ ;
		}else{
		//NO: entonces fi<f1<ft (es un dato intermedio): NO se actualiza geometría, se compara f1 con las fechas de t_precio
			$arr_cont['nomodif']++ ;
			$sql = "
			SELECT MAX(fecha) AS maxfecha,precio FROM t_precios 
			WHERE id_inmuebleprecio='$id_inmueble' 
			GROUP BY id_inmuebleprecio
			" ;
			$g_conexion->execute ($sql) ;
			$linea2 = $g_conexion->fetch() ;
			if (  $aa['fecha'] > $linea2['maxfecha']  ){
			//f1>fpf: si Ppf <> P1 (NO se actualiza en Pt, porque se asume que Pt corresponde a una actualizacion poterior que se hizo en ft) y se inserta registro en t_precios 
				if ( $aa['precio'] != $linea2['precio'] ){				
					Actualiza_t_precios( $id_inmueble , $aa['fecha'] , $aa['precio'] , $aa['url'] ) ;
					$arr_cont['prmodif']++ ;
				}else{
					MsjE("Fecha f1 > fpf pero precio NO cambia precio old:{$linea2['precio']} , nada se actualiza en t_precios") ;
					$arr_cont['nomodifpr']++ ;
					//nada porque el precio no cambió
				}
			}else{
			//f1<fpf: si Ppf <> P1 se inserta registro en t_precios (si hay para la fecha, se actualiza)
				Actualiza_t_precios( $id_inmueble , $aa['fecha'] , $aa['precio'] , $aa['url'] ) ;
			}
		}	
	}
	
	function Actualiza_t_precios( $id_inmu , $fecha , $precio , $url ){
		
		global $g_conexion ;
		global $arr_cont ;
		
		msj('en Actualiza_t_precios') ;
		$sql = "SELECT MAX(fecha) AS maxfecha FROM t_precios 
		WHERE fecha<='$fecha' AND
		id_inmuebleprecio='$id_inmu'
		GROUP BY id_inmuebleprecio
		" ;
		$g_conexion->execute ($sql) ;
		$linea = $g_conexion->fetch() ;
		
		$sql = "
		SELECT precio,url FROM t_precios 
		WHERE id_inmuebleprecio='$id_inmu' AND fecha='{$linea['maxfecha']}'
		" ;
		$g_conexion->execute ($sql) ;
		if ( $g_conexion->returnedRows() > 0 ){
			$linea2 = $g_conexion->fetch() ;
			if ( $precio != $linea2['precio'] ){
				if ( $fecha == $linea['maxfecha'] ){
				//el registro existe se necesita update				
					MsjE( "Precio para actualizar de {$linea2['precio']} a $precio en $id_inmu el día $fecha<br> URL ANT: {$linea2['url']} <br> URL NEW: $url" ) ;
					$sql = "
						UPDATE t_precios
						SET
						precio='$precio',
						url='$url'
						WHERE 
						id_inmuebleprecio='$id_inmu' AND
						fecha='$fecha'
					" ;
					$g_conexion->execute ($sql) ;
					if ( $g_conexion->affectedRows() != 1 ){
						MsjE("BD no actualizó: $sql") ;
					}else{
						$si = TRUE ;
					}				
				}else{
				// la fecha no existe, hay que insertar
					MsjE( "Precio ha cambiado de {$linea2['precio']} a $precio en $id_inmu el día $fecha<br> URL ANT: {$linea2['url']} <br> URL NEW: $url" ) ;
					$sql = "
						INSERT INTO t_precios (
							id_inmuebleprecio,
							fecha,
							precio,
							url
						) VALUES (
							'$id_inmu',
							'$fecha',
							'$precio',
							'$url'
						)
					" ;
					$g_conexion->execute ($sql) ;
					$arr_cont['prFmodif']++ ;
				}
			}  
		}else{
		//parece que la fecha no tiene registro en t_precios
			MsjE( "Parece precio nuevo: $precio en $id_inmu el día $fecha<br> URL NEW: $url" ) ;
			$sql = "SELECT MIN(fecha) AS minfecha FROM t_precios 
			WHERE id_inmuebleprecio='$id_inmu'
			GROUP BY id_inmuebleprecio
			" ;			
			$g_conexion->execute ($sql) ;
			$inserte = 0 ;
			if ( $g_conexion->returnedRows() > 0 ){
			//si hay registro en t_precios pero de una fecha posterior
				$linea = $g_conexion->fetch() ;
				
				$sql = "
				SELECT precio,url FROM t_precios 
				WHERE id_inmuebleprecio='$id_inmu' AND fecha='{$linea['minfecha']}'
				" ;
				$g_conexion->execute ($sql) ;
				if ( $g_conexion->returnedRows() > 0 ){
					$linea2 = $g_conexion->fetch() ;
					if ( $precio == $linea2['precio'] ){					
					//no cambió el precio inicial
						$sql = "
							UPDATE t_precios
							SET
							fecha='$fecha',
							url='$url'
							WHERE
							id_inmuebleprecio='$id_inmu' AND
							fecha='{$linea['minfecha']}'
						" ;
						$g_conexion->execute ($sql) ;
						if ( $g_conexion->affectedRows() != 1 ){
							MsjE("BD no actualizó: $sql") ;
						}else{
							$si = TRUE ;
						}
					
					}else{
						MsjE('El precio inicial cambio') ;
						$inserte = 1 ;
					}
				}
			}else{
			//no hay registro en t_precios
				MsjE('No hay registros en t_precios') ;
				$inserte = 1 ;
			}			
			
			if ( $inserte == 1 ){
				$sql = "
					INSERT INTO t_precios (
						id_inmuebleprecio,
						fecha,
						precio,
						url
					) VALUES (
						'$id_inmu',
						'$fecha',
						'$precio',
						'$url'
					)
				" ;
				$g_conexion->execute ($sql) ;
			}			
		}			
	}
	
	//SEGURMNTE SE REQUIERE UN OBJETO EN ESTOS UPDATE PARA LLEVAR TODOS LOS CONTADORES
	function ActualizaGoemetria( $id_inmueble , $arr_datos_inm ){
	//Cambia los campos "mutables" del inmueble que no se relacionan con precio
		
		global $g_conexion ;
		global $arr_cont ;
		
		$aa = $arr_datos_inm ;
		$mid = $id_inmueble ;
		$chg = 0 ;
		$chgu = 0 ;
		$sql = "
			SELECT 
			url, idM2_ini, identif1, identif2, precio, b_manual
			FROM t_inmueble
			WHERE id_inmueble = '$mid'  " ;
		$g_conexion->execute ($sql) ;
		$linea = $g_conexion->fetch() ;
		ver_arr( $linea , '$linea con resultado del query' ) ;
		$t_new = $aa['identif1'] ;
		$t_old = $linea['identif1'] ;
		if ( strcmp( $t_old , $t_new ) != 0 ){
			MsjE( "$mid SI cambio en los datos:<br>OLD:$t_old<br>NEW:$t_new" ) ;
			$arr_cont['datmodif']++ ;	
			if ( $linea['b_manual'] == 1 ){
				MsjE("NO se alteran datos en t_inmueble ya que tiene bandera MANUAL") ;
				$arr_cont['datrech'] = 0 ;
				//$i_no_cambio++ ;
			}else{					
				$chg =1 ;
			}		
			
		}else{
		//nada no hay cambios perceptibles no se requiere hacer update
			//$i_igual++ ;
		}
		$t_new = $aa['url'] . ' >> ' . $aa['idM2_ini'] ;
		$t_old = $linea['url'] . ' >> ' . $linea['idM2_ini'] ;
		if ( strcmp( $t_old , $t_new ) != 0 ){
			MsjE( "$mid SI cambio en la URL + IDM2:<br>OLD:$t_old<br>NEW:$t_new" ) ;
			if ( $linea['b_manual'] == 1 ){
				
				MsjE("SI se cambiara URL aunque tiene bandera MANUAL") ;
				$chgu = 1 ;
			}else{					
				$chgu = 1 ;
			}		
			
		}else{
		//nada no hay cambios perceptibles no se requiere hacer update
			//$i_igual++ ;
		}
		
		if ( $chg == 0 and $chgu == 0 ){
			$arr_cont['nomodif']++ ;
		}
		
		$arr_campos['todos'] = array( 		
		'contacto', 
		'telefono', 
		'id_barrio', 
		'idM2_ini',
		'url', 
		'identif1', 
		'identif2', 
		'zona', 
		'barrio',
		'catastro',
		'estrato',
		'antiguedad_rg',
		'area_privada',
		'area_construida',
		'habitaciones',
		'banhos',
		'garajes',
		'ascensor',
		'admon') ;
		//$arr_campos['texto'] = array( 'fecha' , 'contacto' , 'telefono'  )  ;
		//$arr_campos['numeros'] = array(   )  ;;
	
		
		// En ningún caso se actualiza la fecha, ya que esta marca el punto del último precio vigente, y acá no se analizó eso
		if ( $chg == 1 ){		//hay modificaciones se hace el update		
			$txt = '' ; 
			foreach ( $arr_campos['todos'] as $val ){
				//$txt .= $val . "= '{" . '$aa[\'' . $val . '\']} , ' ;
				$txt .= $val . "= '{$aa[$val]}'," ;
			}
			
			$txt = substr( $txt, 0 , -1 ) ;  // quitar la ultima coma
			
		
			$sql = "
				UPDATE t_inmueble
				SET $txt
				WHERE id_inmueble = '$mid' ";
			$g_conexion->execute ($sql) ;
			
		}
		if ( $chgu == 1 ){		//hay modificaciones se hace el update		
			$txt = "
			idM2_ini = '{$aa['idM2_ini']}' ,
			url = '{$aa['url']}' 
			" ;
			
			$sql = "
				UPDATE t_inmueble
				SET $txt
				WHERE id_inmueble = '$mid' ";
			$g_conexion->execute ($sql) ;
			$arr_cont['urlmodif'] ++ ;
			//$i_update_web++ ;
		}		
	}
	
	function ActualizaPrecio( $id_inmueble , $aa ){		
	//Esta fucnión se llama cuando se sabe que el precio cambió verifica si está manual
		
		global $g_conexion ;
		global $arr_cont ;
		
		
		$chgp = 1 ;
		
		$sql = "
			SELECT 
			url, idM2_ini, identif1, identif2, precio, b_manual, fecha_fin
			FROM t_inmueble
			WHERE id_inmueble = '$id_inmueble'  " ;
		$g_conexion->execute ($sql) ;
		$linea = $g_conexion->fetch() ;
		ver_arr( $linea , '$linea con resultado del query' ) ;
		
		if ( $linea['b_manual'] == 1 ){
			$arr_cont['prrech']++ ;
			MsjE("NO actualiza precio por bandera MANUAL") ;
			$sql = "
				UPDATE t_inmueble SET
				fecha='{$aa['fecha']}'
				WHERE id_inmueble = '$id_inmueble'
			" ;
			$g_conexion->execute ($sql) ;			
		}else{
			$sql = "
				UPDATE t_inmueble SET
				fecha='{$aa['fecha']}',
				precio='{$aa['precio']}'
				WHERE id_inmueble = '$id_inmueble'
			" ;
			$g_conexion->execute ($sql) ;			
			Actualiza_t_precios( $id_inmueble , $aa['fecha'] , $aa['precio'] , $aa['url'] ) ;
		}		
	}
	
	function ActualizaPrecioIni( $id_inmueble , $aa ){
		global $g_conexion ;
		global $arr_cont ;
		
		msj('en ActualizaPrecioIni') ;
		$sql = "
			UPDATE t_inmueble SET
			fecha_ini='{$aa['fecha']}',
			precio_ini='{$aa['precio']}'
			WHERE id_inmueble = '$id_inmueble'
		" ;
		$g_conexion->execute ($sql) ;
		Actualiza_t_precios( $id_inmueble , $aa['fecha'] , $aa['precio'] , $aa['url'] ) ;
	}
	
	/*
	function InmueblesActivarBD( $psfecha ){
	//ajusta la bandera b_activo en la BD t_inmueble de acuerdo con la fecha, para buscar inmuebles que estuvieran activos en lso 15 DIAS CONSITGUOS
		global $g_conexion ;
	
		$lafecha = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $psfecha , 5 , 2 ) , substr( $psfecha , 8 , 2 ) + 7 , substr( $psfecha , 0 , 4 ))) ;
		$lafecha2 = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $psfecha , 5 , 2 ) , substr( $psfecha , 8 , 2 ) - 7 , substr( $psfecha , 0 , 4 ))) ;
		//$mañana        = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
		msj("la fecha: $lafecha") ;			
		
		$sql = "UPDATE t_inmueble SET b_activo = '0' WHERE 1" ;
		$g_conexion->execute ($sql) ;		
		$sql = "UPDATE t_inmueble SET b_activo = '1' WHERE (fecha_ini<'$lafecha' AND (fecha_fin IS NULL OR fecha_fin>'$lafecha2'))" ;
		$g_conexion->execute ($sql) ;	
	}
	*/
	
?>