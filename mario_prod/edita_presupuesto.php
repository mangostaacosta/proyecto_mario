<?php
 /****************************************************************************
  20150213  >> edita_presupuesto.php
  Proyecto: Obsevatorio Mario
  La variable de entrada es el codigo de un id_inmueble
  Sirve para presetnar y editar la información de compra y venta de un inmueble, presentando datos comparativos para guiar al ususario
  PRE: $id_inmueble
  POST: edición de t_presupuesto
  Cuando $_GET['pag'] = 2 tiene información para UPDATE tabla  
 ******************************************************************************/
 
	require_once 'header.php' ;
	
	$manual['tit'] = 'Presupuesto de Remodelación' ;
	$manual['tex'] = 'Esta opción permite ingresar o editar los valores de remodelación y negociación del inmueble.
	
	El ingreso a esta opción se realiza a través de un enlace interno. La página presenta dos tablas. La primera tabla es un formulario donde se pueden editar los valores sugeridos. Al finalizar la edición de los valores se debe presionar el Botón "Enviar" para que los cambios se guarden en la Base de Datos. Adicionalmente se incluye un enlace que permite retornar a la pantalla de edición.
	
	Enlace:
	<b>Volver a Edición:</b> al presionar retorna a la opción de Edición del Inmueble.
	
	El formulario en la primera tabla incluye los datos enumerados más abajo.
	En la columna a la derecha de cada cifra se presenta el valor porcentual de la cifra respecto al <b>Precio de Venta Esperado</b>(los valores monetarios se diligencian en millones de pesos):
	
	<b>Autor:</b> nombre del responsable de la proyección del presupuesto.
	<b>Fecha Presupuesto:</b> Fecha en que se diligencia el preupuesto. Formato AAAA-MM-DD
	<b>Precio Anunciado:</b> Última actualización del Precio de venta del inmueble.
	<b>Precio Ofertado:</b> Valor que se le ofreció al vendedor.
	<b>Precio Negociado:</b> Precio al que finalmente se lograría la negociación del inmueble.
	<b>Precio Venta Esperado:</b> Precio al que se proyecta vender después de la remodelación.
	<b>Precio Venta de Salida:</b> Precio inflado al que se proyecta ofrecer inicialmente el inmueble después de la remodelación.
	<b>Costo de Obra:</b> Costo estimado de la obra de remodelación.
	<b>Costo Otros:</b> Estimativo de los demás costos que impactan la rentabilidad del negocio: mercadeo, impuestos, administrativos, etc.	
	<b>Utilidad Optimista:</b> Utilidad que se lograría con el precio de venta de Salida.
	<b>Utilidad Esperada:</b> Utilidad que se logra con el precio de venta esperado.	
	<b>Tiempo de Venta:</b> Estimativo del tiempo en que se venderá el inmueble, incluyendo el tiempo de obra.
	<b>Utilidad Anualizada:</b> Equivalente de la utilidad anualizada, tomando en consideración el tiempo de venta.
		
	La segunda tabla incluye un resumen de los últimos datos archivados del inmueble, sirve como referencia para diligenciar el formulario superior. 
	Incluye los campos diligenciados en la iteración anterior del presupuesto así como la información de la opción Editar Inmueble y el Puntaje de la Matriz de Evaluación.
	En la primera columna a la derecha de cada cifra se presenta el valor porcentual de la cifra contra el <b>Precio de Compra</b> y contra el <b>Precio de Venta Esperado</b>(los valores monetarios se diligencian en millones de pesos):
	
	Algunos campos para destacar:
	<b>Inmueble:</b> Identificador de búsqueda del inmueble. Al hacer click en el enlace se despliega el URL externo del inmueble.
	<b>Puntaje Evaluación:</b> En este campo se muestra el puntaje total obtenido por el inmueble acorde con las calificaciones remitidas. El puntaje máximo es 100 y el mínimo es 0.
	' ;
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Shortcuts > Presupuesto de Remodelación' ) ;
	
	/*
	define("FACTOR_VENTA", 1.3 ) ;
	define("FACTOR_OPTIMISTA", 1.4 ) ;
	define("FACTOR_OBRA", 0.08 ) ;
	define("FACTOR_OTROS", 0.03 ) ;
	define("FIJOS", 2 ) ;
	define("FACTOR_DCTO", 0.9 ) ;	
	define("PESO_MERCADO", 0.75 ) ;
	define("FACTOR_NUEVO", 0.80 ) ;
	*/
	
	global $g_fecha ;
	global $g_conexion ;
	
	/*
	function limpia_sql( $texto ){
		$texto = trim( $texto ) ;
		$texto = mysql_real_escape_string( $texto ) ;
		return $texto ;
	}
	*/
	/*
	if (isset( $_GET['id_inmueble'] )){
		$id_inmueble = $_GET['id_inmueble'] ;		
	}else{
		//nada
	}
	*/
	
	if (isset( $_GET['id_inmueble'] )){
		$id_inmueble = $_GET['id_inmueble'] ;
	}else{
		echo "Falta id_inmueble" ;
		die() ;
	}
	
	if (isset( $_GET['f_fecha'] )){
		$f_fecha = $_GET['f_fecha'] ;		
	}else{
		//nada
	}
	
	if (isset( $_GET['pag'] )){
		$pag = $_GET['pag'] ;
		if ( !(isset( $_GET['id_inmueble'] ))){
			MsjE('Error, falta información de identidad de inmueble') ;
			die() ;
		}else{
			//nada
		}
	
		if ( $pag == 2 ){
		// Viene información de UPDATE
			$id_inmueble = limpia_dat( $_GET['id_inmueble'] ) ;
						
			$autor = limpia_dat( $_GET['autor'] ) ;
			$fecha_presup = limpia_dat( $_GET['fecha_presup'] ) ;
			$p_anunciado = limpia_dat( $_GET['p_anunciado'] ) ;
			$p_ofertado = limpia_dat( $_GET['p_ofertado'] ) ;
			$p_negociado = limpia_dat( $_GET['p_negociado'] ) ;
			$p_venta_esperado = limpia_dat( $_GET['p_venta_esperado'] ) ;
			$p_venta_ini = limpia_dat( $_GET['p_venta_ini'] ) ;
			$valor_obra = limpia_dat( $_GET['valor_obra'] ) ;
			$valor_otros = limpia_dat( $_GET['valor_otros'] ) ;
			$tiempo = limpia_dat( $_GET['tiempo'] ) ;
			if ( $fecha_presup == '' ){
				$fecha_presup = date('Y-m-d') ;
			}
			
			if ( $p_anunciado == '' OR $p_anunciado < 10 ){
				MsjE('Favor revisar el precio ANUNCIADO no debe ser menor a $10,000,000') ;				
			}
			if ( $p_ofertado == '' OR $p_ofertado < 10 ){
				MsjE("Favor revisar el precio OFERTADO no debe ser menor a $10,000,000, se estableció igual al anunciado: $p_anunciado") ;
				$p_ofertado = $p_anunciado ;
			}
			if ( $p_negociado == '' OR $p_negociado < 10 ){
				MsjE("Favor revisar el precio OFERTADO no debe ser menor a $10,000,000, se estableció igual al anunciado: $p_anunciado") ;
				$p_negociado = $p_anunciado ;
			}
			if ( $tiempo == '' OR $tiempo == 0 ){
				MsjE("Favor revisar el TIEMPO no debe ser cero, ajustado a 1") ;
				$tiempo = $tiempo ;
			}
			$utilidad = $p_venta_esperado - ($p_negociado + $valor_obra + $valor_otros) ;
			
			$sql = "
				UPDATE t_presupuesto
				SET 
					autor = '$autor',
					fecha_presup = '$fecha_presup',
					p_anunciado = '$p_anunciado',
					p_ofertado = '$p_ofertado', 
					p_negociado = '$p_negociado', 
					p_venta_esperado = '$p_venta_esperado', 
					p_venta_ini = '$p_venta_ini',  
					valor_obra = '$valor_obra',
					valor_otros = '$valor_otros',	
					tiempo = '$tiempo', 
					utilidad = '$utilidad' 					
				WHERE id = $id_inmueble
			" ;		//b_activo = $b_activo, 

			$g_conexion->execute ($sql) ;
		}
	}
	
	$sql = "SELECT * FROM t_presupuesto WHERE id = '$id_inmueble'" ;
	$g_conexion->execute( $sql ) ;	
	$salidabd = $g_conexion->fetch() ;		//sólo devuelve un registro por el momento (después incluir varios evaluadores)		
	if ( !($salidabd) ){ 					//entiendo que esto querría decir que el registro no ha sido creado en t_matevaluacion
		$sql = "
			INSERT INTO t_presupuesto (
				id
			) VALUES (
				'$id_inmueble'
			)
		" ;
		$g_conexion->execute( $sql ) ;
	}
	unset( $salidabd ) ;
	
	$sql = "
		SELECT 
		autor,
		fecha_presup,
		p_anunciado,
		p_ofertado, 
		p_negociado, 
		p_venta_esperado, 
		p_venta_ini,  
		valor_obra,
		valor_otros,
		tiempo, 
		utilidad,		
		
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
		t_inmueble.comentario,
		punt_total,
		
		b_normal,
		b_manual,
		b_duda_barrio,
		b_conbarrio
		FROM t_presupuesto
		LEFT JOIN t_inmueble ON id = id_inmueble
		LEFT JOIN t_matevaluacion ON id = id_evaluado
		WHERE id_inmueble = $id_inmueble
	" ; //b_activo,
	
	
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	$salidabd = $g_conexion->fetch() ;		//sólo devuelve un registro
	$val = $salidabd ;
	
	$elprecio = $val['precio'] / 1000000 ;
	if ( $val['fecha_presup'] == '' ){
		$val['fecha_presup'] = date('Y-m-d') ;		
	}
	if ( $val['p_anunciado'] == '' ){
		$val['p_anunciado'] = $elprecio ;		
	}
	if ( $val['p_negociado'] == '' ){
		$val['p_negociado'] = $elprecio ;		
	}
	if ( $val['p_ofertado'] == '' ){
		$val['p_ofertado'] = $elprecio ;
	}
	if ( $val['p_venta_esperado'] == '' ){
		$val['p_venta_esperado'] = $elprecio * FACTOR_VENTA ;
	}
	if ( $val['p_venta_ini'] == '' ){
		$val['p_venta_ini'] = $elprecio * FACTOR_OPTIMISTA ;
	}
	if ( $val['valor_obra'] == '' ){
		$val['valor_obra'] = $elprecio * FACTOR_OBRA ;
	}
	if ( $val['valor_otros'] == '' ){
		$val['valor_otros'] = $elprecio * FACTOR_OTROS + FIJOS ;
	}
	if ( $val['utilidad'] == '' ){
		$val['utilidad'] = $elprecio * ( FACTOR_VENTA - FACTOR_OBRA - FACTOR_OTROS - 1 ) - FIJOS ;
		$util_optimista = $elprecio * ( FACTOR_OPTIMISTA - FACTOR_OBRA - FACTOR_OTROS - 1 ) - FIJOS ;
	}else{
		$val['utilidad'] = $val['p_venta_esperado'] - $val['p_negociado'] -  $val['valor_obra'] - $val['valor_otros'] ;
		$util_optimista = $val['p_venta_ini'] - $val['p_negociado'] -  $val['valor_obra'] - $val['valor_otros'] ;
	}
	
	//llenar el arreglo para pintar el html
	
	function TextoPorcentaje( $a , $b , $c = 0 ){
		$texto = $a/$b * 100 ;
		$texto = formato_n( $texto , 1 ) ;
		$texto = "</td><td> $texto%" ;
		if ( $c != 0 ){
			$texto2 = $a/$c * 100 ;
			$texto2 = formato_n( $texto2 , 1 ) ;
			$texto .= "</td><td> $texto2%" ;
		}		
		return $texto ;
	}
	
	$fin = '</td><td>' ;
	
	$elprecio = $val['p_negociado'] ;
	$elprecio2 = $val['p_venta_esperado'] ;
	
	$linea['Autor_________________'] = FormTexto( 'autor' , $val['autor']  , '' , 0 , 20 ) . $fin ;
	$linea['Fecha Presupuesto'] = FormTexto( 'fecha_presup' , $val['fecha_presup'] , '' , 0 , 10 ) . $fin ;
	$linea['Precio Anunciado'] = FormTexto( 'p_anunciado' , formato_n( $val['p_anunciado']  , 0 ) , '' , 0 , 10 ) . $fin ;
	$linea['Precio Ofertado'] = FormTexto( 'p_ofertado' , formato_n( $val['p_ofertado']  , 0 ) , '' , 0 , 10 ) . $fin ;
	$linea['Precio Negociado'] = FormTexto( 'p_negociado' , formato_n( $val['p_negociado']  , 0 ) , '' , 0 , 10 ) . TextoPorcentaje( $val['p_negociado'] , $elprecio2 ) ;
	$linea['Precio Venta Esperado'] = FormTexto( 'p_venta_esperado' , formato_n( $val['p_venta_esperado']  , 0 ) , '' , 0 , 10 ) . TextoPorcentaje( $val['p_venta_esperado'] , $elprecio2 ) ;
	$linea['Precio Venta de salida'] = FormTexto( 'p_venta_ini' , formato_n( $val['p_venta_ini']  , 0 ) , '' , 0 , 10 ) . TextoPorcentaje( $val['p_venta_ini'] , $elprecio2 ) ;
	$linea['Costo de Obra'] = FormTexto( 'valor_obra' , formato_n( $val['valor_obra']  , 0 ) , '' , 0 , 10 ) . TextoPorcentaje( $val['valor_obra'] , $elprecio2 ) ;
	$linea['Costo Otros'] = FormTexto( 'valor_otros' , formato_n( $val['valor_otros']  , 0 ) , '' , 0 , 10 ) . TextoPorcentaje( $val['valor_otros'] , $elprecio2 ) ;	
	$linea['Utilidad Optimista'] = formato_n( $util_optimista  , 0 ) . TextoPorcentaje( $util_optimista , $elprecio2 ) ;
	$linea['Utilidad Esperada'] = formato_n( $val['utilidad']  , 0 ) . TextoPorcentaje( $val['utilidad'] , $elprecio2 ) ;
	$linea['Utilidad Anualizada'] = TextoPorcentaje( $val['utilidad'] * 12 /  $val['tiempo'] , $elprecio2 ) ;
	$linea['Tiempo de venta (meses)'] = FormTexto( 'tiempo' , $val['tiempo']  , '' , 0 , 10 ) . $fin ;
	
	$arr_first = $linea ;
	$pref['Inmueble'] = "<a href=\"{$val['url']}\" target=\"_blank\">{$val['idM2_ini']}</a>" ;
	$pref['Puntaje Evaluación'] = $val['punt_total'] ;
	
	$linea = array_merge( $pref , $linea ) ;
	
	//vuelvo a mostrar lo anterior para que sirva de referencia
	//$linea['Inmueble'] = "<a href=\"{$val['url']}\" target=\"_blank\">{$val['idM2_ini']}</a>" ;
	//$linea['Puntaje Evaluación'] = $val['punt_total'] ;
	$linea['Autor'] = $val['autor'] ;
	$linea['Fecha Presupuesto'] = $val['fecha_presup'] ;
	$linea['Precio Anunciado'] = formato_n( $val['p_anunciado'] ) ;
	$linea['Precio Ofertado'] = formato_n( $val['p_ofertado'] ) ;
	$linea['Precio Negociado'] = formato_n( $val['p_negociado'] ) . TextoPorcentaje( $val['p_negociado'] , $elprecio , $elprecio2 ) ;
	$linea['Precio Venta Esperado'] = formato_n( $val['p_venta_esperado'] ) . TextoPorcentaje( $val['p_venta_esperado'] , $elprecio , $elprecio2 ) ;
	$linea['Precio Venta de salida'] = formato_n( $val['p_venta_ini'] ) . TextoPorcentaje( $val['p_venta_ini'] , $elprecio , $elprecio2 ) ;
	$linea['Costo de Obra'] = formato_n( $val['valor_obra'] ) . TextoPorcentaje( $val['valor_obra'] , $elprecio , $elprecio2 ) ;
	$linea['Costo Otros'] = formato_n( $val['valor_otros'] ) . TextoPorcentaje( $val['valor_otros'] , $elprecio , $elprecio2 ) ;	
	$linea['Tiempo de venta (meses)'] = $val['tiempo'] ;
	$linea['Utilidad Esperada'] = formato_n( $val['utilidad'] ) . TextoPorcentaje( $val['utilidad'] , $elprecio , $elprecio2 ) ;
	$linea['Utilidad Optimista'] = formato_n( $util_optimista ) . TextoPorcentaje( $util_optimista , $elprecio , $elprecio2 ) ;	
	$linea['Utilidad Anualizada'] = TextoPorcentaje( $val['utilidad'] * 12 /  $val['tiempo'] , $elprecio , $elprecio2 ) ;
	$linea['Detalle_Inmueble_________'] = '' ;
	$linea['Tipo'] = $val['tipo_inm'] ;
	$linea['Fecha Inicio'] = $val['fecha_ini'] ;	
	$linea['Fecha Final'] = $val['fecha_fin'] ;
	$linea['Fecha'] = $val['fecha'] ;
	$linea['Dirección'] = $val['direccion'] ;
	$linea['Piso'] = $val['piso'] ;
	$linea['Teléfono'] =$val['telefono'] ;
	$linea['COMENTARIO'] = $val['comentario'] ;
	$linea['id_barrio'] = $val['id_barrio'] ;	
	$linea['Catastro'] = $val['catastro'] ;
	$linea['Estrato'] = $val['estrato'] ;	
	$linea['Antigüedad'] = $val['antiguedad'] ;
	$linea['Antigüedad_rgo'] = $val['antiguedad_rg'] ;
	$linea['Area'] = formato_n( $val['area_privada'], 0 ) ;
	$linea['Area cons'] = formato_n( $val['area_construida'], 0 ) ;
	$linea['Habits'] = $val['habitaciones'] ;
	$linea['Baños'] = $val['banhos'] ;
	$linea['Garajes'] = $val['garajes'] ;
	$linea['Ascensores'] = $val['ascensor'] ;
	$linea['Valor_Admin'] = formato_n( $val['admon'] , 0 ) ;
	$linea['Precio_Inic'] = formato_n( $val['precio_ini'] / 1 , 0 ) ;
	$linea['Precio'] = formato_n( $val['precio'] / 1 , 0 ) ;
	$linea['PM2'] = formato_n( $val['precio_metro'] / 1 , 0 ) ;
	$linea['Contacto'] = $val['contacto'] ;
	$linea['Tipo_contacto'] = $val['tipo_contacto'] ;
	//$linea['b_activo'] = FormCheck( 'b_activo' , $val['b_activo'] ) ;
	$linea['b_normal'] = FormCheck( 'b_normal' , $val['b_normal'] ) ;
	$linea['b_manual'] = FormCheck( 'b_manual' , $val['b_manual'] ) ;
	//$linea['b_duda_barrio'] = FormRadio( 'tipo_inm' , $val['b_duda_barrio'] ) ;
	//$linea['b_conbarrio'] = FormRadio( 'tipo_inm' , $val['b_conbarrio'] ) ;	
	
	
	//$f_fecha = ( isset( $fecha ) ? $fecha : $f_fecha ) ;
	
	$g_html = '' ;
	$g_html .= "
		<div id='newmenu'>
		<ul>
			<li><a href='edita_inm.php?f_idinmueble=$id_inmueble'>Volver a Edición</a></li>
		</ul>
		</div><br>" ;
	
	$g_html .= "<div class=\"center\"><form  action='$_SERVER[PHP_SELF]' name='FormEditaInm' method='GET'>
	<INPUT TYPE='hidden' NAME='pag' VALUE='2'>
	<INPUT TYPE='hidden' NAME='id_inmueble' VALUE=$id_inmueble>
	<input type='submit' value='Enviar'>\n" ;
	//$g_html .="<table border=\"0\" width=\"700\">" ;

	$n = array() ;
	$g_html .= html_arreglo_uni( $arr_first , 1 , $n , "Datos Presupuesto (en millones)") ;
	$g_html .= '</table></form></div><br>' ;
	
	$g_html .= html_arreglo_uni( $linea , 1 , $n , "Detalle Inmueble") ;	
	
  	echo $g_html ;	
  
?>