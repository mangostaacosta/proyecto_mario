<?php
 /****************************************************************************
  20150116  >> inserta_inm.php
  Proyecto: Obsevatorio Mario
  Codigo para insrtar un nuvo inmueble en la BD que no viene a través del cargue masivo de metrocuadrado
	Interesante notar que cuando acá o en edita_inm.php se cambia el precio debería afectar la FUTURA tabal de seguimiento de precios
  Cuando $_GET['pag'] = 2 tiene informción para UPDATE tabla  
 ******************************************************************************/

	require_once 'header.php' ;
	
	global $g_fecha ;
	global $g_conexion ;

	
	$manual['tit'] = 'Captura Manual' ;
	$manual['tex'] = 'Esta opción permite ingresar el registro de un nuevo inmueble de forma manual, es decir sin que sea descargado automáticamente de un portal en Internet.
	La página presenta el formulario "Datos Iniciales" en donde se deben diligenciar los siguientes campos:
	<b>Fecha Inicio (obligatorio):</b> corresponde a la fecha que se quiere asignar al inmueble inicialmente en la Base de Datos, normalmente corresponde al mismo día en que se está ingresando la información. Los inmuebles ingresados tienen una vida útil de 15 días después de la fecha de inicio, a partir de los cuales se considerará que el inmueble ya no está vigente. En caso de que el inmueble continúe en el mercado tras los 15 días, se deberá actualizar la información con la opción "Editar Inmueble" para que mantenga una vigencia adicional.
	<b>URL (opcional):</b> corresponde a la dirección WEB donde se puede encontrar información del inmueble en caso de que exista.
	<b>Identificador (obligatorio):</b> El identificador corresponde a un código alfanumérico para identificar el inmueble en la Base de Datos. Al identificador ingresado por el usuario, se le anteponen la letras "IIMM" para señalar que éste fue ingresado manualmente.
	<b>Precio Inicial (opcional):</b> corresponde a un campo numérico donde se debe ingresar el valor del inmueble en pesos
	<b>ID Barrio (obligatorio):</b> se debe escoger uno de los barrios del menú desplegable, el cual presenta los barrios que están incluidos en la Base de Datos.
	
	Después de digitar la información de cada campo se debe presionar el botón "Enviar" en la parte superior para realizar la inserción en la Base de Datos.
	Los datos ingresados en la Base de Datos se presentan nuevamente al usuario para que realice correcciones en caso de ser requerido. Adicionalmente se muestra un enlace en la parte superior que remite a la opción "Editar Inmueble" donde se puede complementar la información ingresada.
	' ;
	
	//$manual['tex'] = "hola (sumercé) 	que vaina" ;
	
	echo VentanaHelp( $manual ) ;

	//Verificar si es la primera vez que se ingresa a la página
	if (isset( $_GET['pag'] )){
		$pag = $_GET['pag'] ;
		if ( $pag == 2 ){
		// Viene información de UPDATE (es decir no es la primera vez)		
			$precio_ini = limpia_dat( $_GET['precio_ini'] ) ;
			$telefono =  limpia_dat( $_GET['telefono'] ) ;
			$url =  limpia_dat( $_GET['url'] ) ;
			$idM2_ini = 'IIMM_' . limpia_dat( $_GET['idM2_ini'] ) ;
			$id_barrio = limpia_dat( $_GET['id_barrio'] ) ;
			$fecha_ini = limpia_dat( $_GET['fecha_ini'] ) ;			
			$fecha_ini = ( $fecha_ini != '' ? $fecha_ini : date('Y-m-d') ) ;
			$fecha_fin = date('Y-m-d', mktime( 0 ,0 , 0 , substr( $fecha_ini , 5 , 2 ) , substr( $fecha_ini , 8 , 2 ) + 15 , substr( $fecha_ini , 0 , 4 ))) ;
			
			/*
			$id_inmueble = limpia_dat( $_GET['f_idinmueble'] ) ;			
			$tipo_inm = limpia_dat( $_GET['tipo_inm'] ) ;
			$fecha = limpia_dat( $_GET['fecha'] ) ;		
			$direccion = limpia_dat( $_GET['direccion'] ) ;
			$piso = limpia_dat( $_GET['piso'] ) ;
			$telefono = limpia_dat( $_GET['telefono'] ) ;
			$comentario = limpia_dat( $_GET['comentario'] ) ;
			$barrio = limpia_dat( $_GET['barrio'] ) ;
			$catastro = limpia_dat( $_GET['catastro'] ) ;
			$estrato = limpia_dat( $_GET['estrato'] ) ;
			$antiguedad_rg = limpia_dat( $_GET['antiguedad_rg'] ) ;
			$area_privada = limpia_dat( $_GET['area_privada'] ) ;
			$habitaciones = limpia_dat( $_GET['habitaciones'] ) ;
			$banhos = limpia_dat( $_GET['banhos'] ) ;
			$garajes = limpia_dat( $_GET['garajes'] ) ;
			$ascensor = limpia_dat( $_GET['ascensor'] ) ;
			$admon = PrecioADecimal( limpia_dat( $_GET['admon'] ) , ',' ) ;
			$precio = PrecioADecimal( limpia_dat( $_GET['precio'] ) , ',' ) ;
			$contacto = limpia_dat( $_GET['contacto'] ) ;
			$tipo_contacto = limpia_dat( $_GET['tipo_contacto'] ) ;
			$b_activo = ( isset($_GET['b_activo'] ) ? 1 : 0 ) ;
			$b_normal = ( isset($_GET['b_normal'] ) ? 1 : 0 ) ;
			$b_manual = ( isset($_GET['b_manual'] ) ? 1 : 0 ) ;		
			*/
			
			//Es la primera vez que se recibe información en la BD o ya existía? 
			if (isset( $_GET['f_idinmueble'] )){	//El registro ya estaba creado en la BD , hay que actualizar
				$id_inmueble = $_GET['f_idinmueble'] ;
				$sql = "
					UPDATE t_inmueble SET 						
					fecha_ini = '$fecha_ini', 
					fecha = '$fecha_ini', 
					fecha_fin = '$fecha_fin',					
					precio_ini = '$precio_ini',
					precio = '$precio_ini',
					telefono = '$telefono',
					url = '$url',
					idM2_ini = '$idM2_ini', 
					id_barrio = '$id_barrio'
					WHERE id_inmueble = '$id_inmueble'					
				" ;
				$g_conexion->execute ($sql) ;				
			}else{									//El registro del inmueble aún no ha sido creado, es necesario crearlo primero en la BD
				$sql = "
					INSERT INTO t_inmueble (						
					tipo_inm,
					fecha_ini,
					fecha,
					fecha_fin,
					precio_ini,
					precio,
					telefono,
					url,
					idM2_ini,
					id_barrio
					) VALUES (
					'apartamento',
					'$fecha_ini',
					'$fecha_ini',
					'$fecha_fin',
					'$precio_ini',
					'$precio_ini',
					'$telefono',
					'$url',
					'$idM2_ini',
					'$id_barrio'
					)			
				" ;
				$g_conexion->execute ($sql) ;
				$llave = $g_conexion->ultimoID () ;
				$id_inmueble = $llave ;
			}
			
			
			//redirect.... edita_inm.php?$llave
		}
	}
	
	//OJO acá debe meterse el formulario que captura el IDINMUEBLE para poder editar el precio_ini etc...	
	$forma = new Formulario() ;
	$forma->Insertar('f_idinmueble','ID Inmueble a corregir:') ;	
	$forma->siempre = TRUE ;
	$forma->Imprimir() ;
	$id_temp = $forma->Sacar('f_idinmueble') ;
	
	if ( $id_temp != '' ){
		$id_inmueble = $id_temp ;
		msj("Funcionamiento de edición por busqueda con id_inmueble=$id_inmueble") ;
	}else{
		msj('Funcionamiento estándar') ;
	}
		
	if ( isset( $id_inmueble )){	//el registro ya ha sido creado en la BD, se recupera info de BD para presentar en el formulario
		$sql = "
			SELECT 
				fecha_ini, 
				url,
				telefono,
				telefono,
				idM2_ini,
				id_barrio,
				precio_ini
			FROM t_inmueble
			WHERE id_inmueble='$id_inmueble' 
		" ;
		$g_conexion->execute ($sql) ;
		$salidabd = $g_conexion->fetch() ;		//sólo devuelve un registro
		$val = $salidabd ;
	}else{
		$val = array() ;
	}
	
	
	$val['fecha_ini'] = ( isset( $val['fecha_ini'] ) ? $val['fecha_ini'] : date('Y-m-d') ) ;
	
	$linea['Fecha Inicio'] = FormTexto( 'fecha_ini' , $val['fecha_ini'] ) ;
	$linea['Teléfono'] = FormTexto( 'telefono' , $val['telefono'] ) ;
	$linea['URL'] = FormTexto( 'url' , $val['url'] ) ;
	$linea['Identificador'] = FormTexto( 'idM2_ini' , $val['idM2_ini'] ) ;
	$linea['Precio Inicial'] = FormTexto( 'precio_ini' , $val['precio_ini'] ) ;
	//$linea['ID Barrio'] = FormMenuBarrio( $val['id_barrio'] ) ;
	$linea['ID Barrio'] = FormMenuBarrio( 'id_barrio' , $val['id_barrio'] ) ;
		
	
	$g_html = '' ;
		if (isset( $id_inmueble )){		
		$g_html .= "El registro ha sido creado, <a href=\"edita_inm.php?f_idinmueble=$id_inmueble&f_fecha=$fecha_ini\">completar la información:</a><br>\n" ;
	}
	
	$g_html .= "<div class=\"center\"><form  action='$_SERVER[PHP_SELF]' name='FormInserta' method='GET'>
		<INPUT TYPE='hidden' NAME='pag' VALUE='2'>
		<input type='submit' value='Enviar'>\n" ;
		
	if (isset( $id_inmueble )){		
		$g_html .= "<INPUT TYPE='hidden' NAME='f_idinmueble' VALUE='$id_inmueble'>" ;
	}
	
	$n = array() ;
	$g_html .= html_arreglo_uni( $linea , 1 , $n , "Datos Iniciales <br>ID Inmueble: $id_inmueble") ;
	$g_html .= '</table></form></div>' ;	
	
  	echo $g_html ;	
  
?>