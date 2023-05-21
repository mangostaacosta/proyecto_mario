<?php
 /****************************************************************************
  20150116  >> edita_inm.php
  Proyecto: Obsevatorio Mario
  La variable de entrada es el codigo de un id_inmueble
  Este código sirve para editar manualmente la información de t_inmueble y la información de t_estudio 
  Cuando $_GET['pag'] = 2 tiene informción para UPDATE tabla  
 ******************************************************************************/

	require_once 'header.php' ;
	
	$manual['tit'] = 'Editar Inmueble' ;
	$manual['tex'] = 'Esta opción permite editar la información de un inmueble en el sistema.
	
	El ingreso a esta opción se debe realizar a través de un enlace interno. La página presenta un formulario con los datos del inmueble seleccionado. Los datos se pueden editar y al finalizar se debe presionar el Botón "Enviar" para que los cambios se guarden en la Base de Datos. Adicionalmente se incluyen tres enlaces que remiten a funcionalidades extendidas que serán explicadas más adelante.
	
	Los enlaces que incluye la página son los siguientes:
	<b>Volver a listado:</b> al presionar redirecciona al listado de inmuebles del barrio respectivo.
	<b>Matriz de Evaluación:</b> al presionar se ingresa a la opción de editar los valores de la matriz de evaluación. Los inmuebles con matriz de evaluación se marcan como en SEGUIMIENTO
	<b>Presupuesto:</b> al presionar se ingresa a la opción de editar el presupuesto de obra y negocio del inmueble.
	
	El formulario incluye los siguientes campos:
	<b>Inmueble:</b> Identificador de búsqueda del inmueble. Al hacer click en el enlace se despliega el URL externo del inmueble. Normalmente no es necesario editar este campo, salvo cuando se necesite corregir un error de digitación en inmuebles ingresados manualmente.	
	<b>Tipo:</b> tipología del inmueble (apartamento|casa).
	<b>Fecha Inicio:</b> Fecha de ingreso del inmueble al sistema.
	<b>Fecha Final:</b> Fecha de pérdida de vigencia del inmueble en el sistema. Esta fecha se puede editar manualmente en el caso de que un inmueble se venda o sea retirado del mercado.
	<b>Fecha:</b> Fecha a la que corresponde la actualización que se está realizando en los datos del inmueble.
	<b>Dirección:</b> Alfanumérico donde se diligencia la dirección, no se captura automáticamente ya que suele contener la dirección de la inmobiliaria.
	<b>Piso:</b> Número del piso. Forma parte de los datos Nivel 3.
	<b>Teléfono:</b> Alfanumérico. Forma parte de los datos Nivel 2.
	<b>COMENTARIO:</b> texto reservado para que el usuario digite sus comentarios.
	<b>id_Barrio:</b> menu desplegable donde el usuario puede escoger el identificdor de barrio único (id_barrio) del inmueble, se genera a partir de la información de dirección catastral.
	<b>Barrio:</b> Datos del barrio digitados manualmente en la página externa. Forma parte de los datos Nivel 2. Cuando está precedido por la palabra (Duda) indica que el barrio tiene un homónimo, por lo que es recomendable verificar el Barrio y el id_Barrio, para evitar errores de localización.
	<b>Catastro:</b> Datos del barrio catastral a partir de los cuales el sistema genera el id_Barrio. Nivel 2.
	<b>Estrato:</b> campo númerico con el estrato. Nivel 2. 
	<b>Antigüedad:</b> campo numérico para que el usuario digite la edad exacta.
	<b>Antigüedad_rgo:</b> rango de antigüedad, los cuales están estandarizados bajo las siglas(new|0a10|10a20|20a). Nivel 1.	
	<b>Area:</b> área privada del inmueble. Nivel 2, aunque ocasionalmente requiere ajuste manual por factores como terrazas.
	<b>Area cons:</b> área construida del inmueble. Normalmente se utiliza este campo para digitar el área que incluye terrazas y otros "adicionales" pero que distorsionan el valor del precio por M2. Nivel 2. 
	<b>Habitaciones:</b> cantidad de habitaciones. Nivel 2.
	<b>Baños:</b> cantidad de baños. Nivel 2.
	<b>Garajes:</b> cantidad de garajes. Nivel 2.
	<b>Ascensores:</b> cantidad de garajes. Nivel 3.
	<b>Valor_Admin:</b> valor de la administración en pesos. Nivel 2.
	<b>Precio_Inic:</b> Precio del inmueble (en pesos) en el momento en que se ingresó al sistema. Nivel 1.
	<b>Precio:</b> Última actualización del Precio de venta (en pesos) del inmueble. Nivel 1.
	<b>PM2:</b> valor del precio/M2 del inmueble. Se recalcula automáticamente por el sistema.
	<b>Contacto:</b> texto para digitar la información de contacto.
	<b>Tipo_contacto:</b> categorización del contacto (inmobiliaria|asesor|natural)
	<b>b_normal:</b> bandera que normalmente debe estar MARCADA para incluir el inmueble en los cálculos estadísticos. Se desmarca cuando alguno de los datos relacionados con dinero del inmueble es dudoso.
	<b>b_manual:</b> bandera que normalmente debe estar DESMARCADA para que los datos del inmueble se actualicen con cada cargue automático de información externa. Se MARCA cuando el inmueble ha sido ingresado manualmente o cuando la información externa esta errada y se ha corregido, para evitar que se sobreescriba.
	<b>b_sgtosimp:</b> bandera que se normalmente está DESMARCADA, y se MARCA en los inmuebles a los que se desea hacer seguimiento. Aparece "matriz" si el inmueble tiene matriz de evaluación.
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Shortcuts > Editar Inmueble' ) ;
	
	
	global $g_fecha ;
	global $g_conexion ;
	
		
	//validar que venga parametro de inmueble
	if (isset( $_GET['f_idinmueble'] )){
		$id_inmueble = $_GET['f_idinmueble'] ;
	}else{
		echo "Falta id_inmueble" ;
		die() ;
	}
	
	//validar y asignar parametro fecha
	if (isset( $_GET['f_fecha'] )){
		$f_fecha = $_GET['f_fecha'] ;		
	}else{
		//nada
	}
	
	//validar y asignar parametro pag, que indica si es la primera vez que se ingresa (solo consulta) o la segunda vez (actuallizar BD UPDATE)
	if (isset( $_GET['pag'] )){
		$pag = $_GET['pag'] ;
		if ( !(isset( $_GET['f_idinmueble'] ))){
			MsjE('Error, falta información de identidad de inmueble') ;
			die() ;
		}else{
			//nada
		}
	
		if ( $pag == 2 ){
		// Viene información de UPDATE
			//asignación y limpieza de variables del Formulario
			$id_inmueble = limpia_sql( $_GET['f_idinmueble'] ) ;
						
			$idM2_ini = limpia_sql( $_GET['idM2_ini'] ) ;
			$tipo_inm = limpia_sql( $_GET['tipo_inm'] ) ;
			$fecha = limpia_sql( $_GET['fecha'] ) ;
			//$fecha1 = trim($fecha,"'") ;			
			//msj( "fecha: $fecha1:" . substr( $fecha1 , 0 , 4 ) . substr( $fecha1 , 5 , 2 ) ) ;
			
			//$fecha_fin = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $fecha1 , 5 , 2 ) , substr( $fecha1 , 8 , 2 ) + 15 , substr( $fecha1 , 0 , 4 ))) ;
			$fecha_fin = limpia_sql( $_GET['fecha_fin'] ) ;
			$fecha_ini = limpia_sql( $_GET['fecha_ini'] ) ;
			
			if ( $fecha_ini > $fecha ){
				MsjE( "Fecha debe ser posterior a la fecha Inicial se cambiará de $fecha a $fecha_ini" ) ;
				$fecha = $fecha_ini ;
			}
			
			if ( $fecha > $fecha_fin ){
				MsjE( "Fecha Final debe ser posterior a la fecha se cambiará de $fecha_fin a $fecha" ) ;
				$fecha_fin = $fecha ;				
			}
			
			$direccion = limpia_sql( $_GET['direccion'] ) ;
			$piso = limpia_sql( $_GET['piso'] ) ;
			$telefono = limpia_sql( $_GET['telefono'] ) ;
			$comentario = limpia_sql( $_GET['comentario'] ) ;
			$id_barrio = limpia_sql( $_GET['ID_Barrio'] ) ;
			if ( isset( $_GET['barrio'] )){
				$barrio = limpia_sql( $_GET['barrio'] ) ;
			}elseif ( isset( $_GET['barrio(duda)'] )){
				$barrio = limpia_sql( $_GET['barrio'] ) ;
			}			
			$catastro = limpia_sql( $_GET['catastro'] ) ;
			$estrato = null_sql( limpia_sql( $_GET['estrato'] )) ;
			$antiguedad = null_sql( limpia_sql( $_GET['antiguedad'] )) ;
			$antiguedad_rg = limpia_sql( $_GET['antiguedad_rg'] ) ;
			$area_privada = null_sql( limpia_sql( $_GET['area_privada'] )) ;
			$area_construida = null_sql( limpia_sql( $_GET['area_construida'] )) ;
			$habitaciones = null_sql( limpia_sql( $_GET['habitaciones'] )) ;
			$banhos = null_sql( limpia_sql( $_GET['banhos'] )) ;
			$garajes = null_sql( limpia_sql( $_GET['garajes'] )) ;
			$ascensor = null_sql( limpia_sql( $_GET['ascensor'] )) ;
			$admon = PrecioADecimal( limpia_sql( $_GET['admon'] ) , ',' ) ;
			$precio = PrecioADecimal( limpia_sql( $_GET['precio'] ) , ',' ) ;
			$contacto = limpia_sql( $_GET['contacto'] ) ;
			$tipo_contacto = limpia_sql( $_GET['tipo_contacto'] ) ;
			//$b_activo = ( isset($_GET['b_activo'] ) ? 1 : 0 ) ;
			$b_normal = ( isset($_GET['b_normal'] ) ? 1 : 0 ) ;
			$b_manual = ( isset($_GET['b_manual'] ) ? 1 : 0 ) ;
			$b_sgtosimp = ( isset($_GET['b_sgtosimp'] ) ? 1 : 0 ) ;
			
			if ( $area_privada == 'NULL' OR $precio == '0' ){
				msj ('entre en null') ;
				$precio_metro = 'NULL' ;
			}else{
				$p = trim( $precio , "'" ) ;
				$a = trim( $area_privada , "'" ) ;								
				$precio_metro = $p / $a ;
			}
			
			//query de actualización del registro en t_inmueble
			$sql = "
				UPDATE t_inmueble
				SET 
					fecha = $fecha,
					fecha_fin = $fecha_fin,
					tipo_inm = $tipo_inm, 
					direccion = $direccion, 
					piso = $piso, 
					telefono = $telefono,  
					comentario = $comentario,
					id_barrio = $id_barrio,	
					idM2_ini = $idM2_ini, 
					barrio = $barrio, 
					catastro = $catastro,  
					estrato = $estrato, 
					antiguedad = $antiguedad, 
					antiguedad_rg = $antiguedad_rg, 
					area_privada = $area_privada, 
					area_construida = $area_construida, 
					habitaciones = $habitaciones, 
					banhos = $banhos, 
					garajes = $garajes, 
					ascensor = $ascensor, 
					admon = $admon, 
					precio = $precio, 
					precio_metro = $precio_metro, 
					contacto = $contacto,  
					tipo_contacto = $tipo_contacto, 
					
					b_normal = $b_normal, 
					b_manual = $b_manual,
					b_sgtosimp = $b_sgtosimp
				WHERE id_inmueble = $id_inmueble
			" ;		//b_activo = $b_activo, 

			$g_conexion->execute ($sql) ;
		}
	}
	
	//query de consulta del inmueble en t_inmueble
	$sql = "
		SELECT 
		id_inmueble,
		tipo_inm,
		fecha_ini,
		fecha_fin,
		fecha,
		direccion,
		piso,
		telefono,
		id_barrio,
		idM2_ini,
		url,		
		barrio,
		catastro, 		
		estrato, 
		antiguedad, 
		antiguedad_rg, 
		area_privada,
		area_construida,
		habitaciones,
		banhos,
		garajes,
		ascensor,		
		admon,
		precio_ini,
		precio,		
		precio_metro, 
		contacto,
		tipo_contacto,		
		comentario,
		
		b_normal,
		b_manual,
		b_sgtosimp,
		b_duda_barrio,
		b_conbarrio
		FROM t_inmueble		
		WHERE id_inmueble = $id_inmueble
	" ; //b_activo,
	
	
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	$salidabd = $g_conexion->fetch() ;		//sólo devuelve un registro
	$val = $salidabd ;
	ver_arr( $val , '$salidabd' ) ;
	//$arr_pintar = array() ;
	

	//llenar el arreglo para pintar el html
	$linea['Inmueble'] = FormTexto( 'idM2_ini' , $val['idM2_ini'] , "<a href=\"{$val['url']}\" target=\"_blank\">{$val['idM2_ini']}</a>" ) ;
	$linea['Tipo'] = FormTexto( 'tipo_inm' , $val['tipo_inm'] ) ;
	$linea['Fecha Inicio'] = $val['fecha_ini'] ;
	//$linea['Fecha Final'] = $val['fecha_fin'] ;
	$linea['Fecha Final'] = FormTexto( 'fecha_fin' , $val['fecha_fin'] ) ;
	$linea['Fecha'] = FormTexto( 'fecha' , $val['fecha'] ) ;	
	$direccion_limpia = preg_replace('/[<>"\n\r]/', '_', $val['direccion'] ) ; //20180515: para quitar los caracteres quedañan el GET generando error
	//$direccion_limpia = preg_replace('/[^A-Za-z0-9\-]/', '', $val['direccion'] ) ; //20180515: para quitar los caracteres quedañan el GET generando error
	$linea['Dirección'] = FormTexto( 'direccion' , $direccion_limpia ) ;
	$linea['Piso'] = FormTexto( 'piso' , $val['piso'] ) ;
	$linea['Teléfono'] = FormTexto( 'telefono' , $val['telefono'] ) ;
	$linea['COMENTARIO'] = FormTexto( 'comentario' , $val['comentario'] , '' , 1 ) ;
	$linea['id_barrio'] = FormMenuBarrio( 'ID_Barrio' , $val['id_barrio'] ) ;
	$temp_barrio = $val['barrio'] ;
	if ( $val['b_duda_barrio'] == 1 ){
		$linea['Barrio'] = FormTexto( 'barrio' , $val['barrio'] , ' (Duda) ' ) ;
	}else{
		$linea['Barrio'] = FormTexto( 'barrio' , $val['barrio'] ) ;
	}	
	$linea['Catastro'] = FormTexto( 'catastro' , $val['catastro'] ) ;
	$linea['Estrato'] = FormTexto( 'estrato' , $val['estrato'] ) ;	
	$linea['Antigüedad'] = FormTexto( 'antiguedad' , $val['antiguedad'] ) ;
	$linea['Antigüedad_rgo'] = FormTexto( 'antiguedad_rg' , $val['antiguedad_rg'] ) ;
	$linea['Area'] = FormTexto( 'area_privada' , formato_n( $val['area_privada'], 0 )) ;
	$linea['Area cons'] = FormTexto( 'area_construida' , formato_n( $val['area_construida'], 0 )) ;
	$linea['Habitaciones'] = FormTexto( 'habitaciones' , $val['habitaciones'] ) ;
	$linea['Baños'] = FormTexto( 'banhos' , $val['banhos'] ) ;
	$linea['Garajes'] = FormTexto( 'garajes' , $val['garajes'] ) ;
	$linea['Ascensores'] = FormTexto( 'ascensor' , $val['ascensor'] ) ;
	$linea['Valor_Admin'] = FormTexto( 'admon' , formato_n( $val['admon'] , 0 )) ;
	$linea['Precio_Inic'] = formato_n( $val['precio_ini'] / 1 , 0 ) ;
	$linea['Precio'] = FormTexto( 'precio' , formato_n( $val['precio'] / 1 , 0 )) ;
	$linea['PM2'] = formato_n( $val['precio_metro'] / 1 , 0 ) ;
	$linea['Contacto'] = FormTexto( 'contacto' , $val['contacto'] ) ;
	$linea['Tipo_contacto'] = FormTexto( 'tipo_contacto' , $val['tipo_contacto'] ) ;
	//$linea['b_activo'] = FormCheck( 'b_activo' , $val['b_activo'] ) ;
	$linea['b_normal'] = FormCheck( 'b_normal' , $val['b_normal'] ) ;
	$linea['b_manual'] = FormCheck( 'b_manual' , $val['b_manual'] ) ;
	
	//comprobar el tipo de seguimiento del inmueble
	if ( $val['b_sgtosimp'] == 0 ){	//no tiene
		$linea['b_sgtosimp'] = FormCheck( 'b_sgtosimp' , $val['b_sgtosimp'] ) ;
	}elseif ( $val['b_sgtosimp'] == 1 ){	//tienen seguimiento de observación
		$linea['b_sgtosimp'] = FormCheck( 'b_sgtosimp' , $val['b_sgtosimp'] ) ;
	}elseif ( $val['b_sgtosimp'] == 2 ){	//tienen seguimiento con matriz de evaluzación
		$linea['b_sgtosimp'] = 'matriz' ;
	}
	
	//$linea['b_duda_barrio'] = FormRadio( 'tipo_inm' , $val['b_duda_barrio'] ) ;
	//$linea['b_conbarrio'] = FormRadio( 'tipo_inm' , $val['b_conbarrio'] ) ;	
	
	//$arr_pintar[] = $linea ;			 
	//ver_arr( $arr_pintar, '$arr_pintar' ) ;
	
	$f_fecha = ( isset( $fecha ) ? $fecha : $f_fecha ) ;
	
	$g_html = '' ;
	
	$idnext = trim( $id_inmueble , "'" ) ;
	
	//links de navegación adicionales del html
	$g_html .= "
		<div id='newmenu'>
		<ul id=\"nav\">
			<li><a href='vecinos.php?f_idbarrio={$val['id_barrio']}'>Volver a listado</a></li>
			<li><a href='evaluador.php?id_inmueble=$idnext'>Matriz de Evaluación</a></li>
			<li><a href='edita_presupuesto.php?id_inmueble=$idnext'>Presupuesto</a></li>
			<li><a href='informe_baratos.php'>Listado Ofertas</a></li>
		</ul>
		</div><br>" ;	
	$g_html .= "<div class=\"center\"><form  action='$_SERVER[PHP_SELF]' name='FormEditaInm' method='GET'>
	<INPUT TYPE='hidden' NAME='pag' VALUE='2'>
	<INPUT TYPE='hidden' NAME='f_idinmueble' VALUE=$id_inmueble>
	<INPUT TYPE='hidden' NAME='fecha_ini' VALUE={$val['fecha_ini']}>
	<input type='submit' value='Enviar'>\n" ;
	//$g_html .="<table border=\"0\" width=\"700\">" ;

	$n = array() ;
	$g_html .= html_arreglo_uni( $linea , 1 , $n , "Detalle Inmueble") ;
	$g_html .= '</table></form></div>' ;	
	
  	echo $g_html ;
	
	/*
	function html_arreglo_uni( $arreglo , $keys = 0 , $titulos = array() , $encabezado = "" ){
	// imprime un arreglo unidimensional
	//si $keys = 1 los titulos son las llaves del arreglos , si 2 el arreglo de titulos, si 0 nada       
    
		if ( $keys == 1 ){
			foreach ( $arreglo as $key => $val ){
				$arr_pinta[] = array( $key , $val ) ;				
			}	
		}
		if ( $keys == 2 ){
			foreach ( $arreglo as $key => $val ){
				$arr_pinta[] = array ( $titulos[$key] , $val ) ;				
			}	
		}
		
		$n = array() ;
		if ( $keys == 0 ){
			//Lo hago en algún momento que lo necesite	
		}else{
			$texto_html = html_arreglo_bi( $arr_pinta , 0 , $n , $encabezado ) ;
		}
		return $texto_html ;
	}
	*/
  
?>