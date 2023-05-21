<?php
/****************************************************************************
  20150121  >> evaluador.php
  Proyecto: Obsevatorio Mario
  Carga archivo que se baja de pagina web a la base de datos. 
  Se alimenta la tabla t_matevaluacion 
  El código muestra un FORM para recopilar la información y un resultado de puntaje final
  PENDEINTE (después incluir varios evaluadores)
  OJO la $categoria no está cumpliendo ninguna función actualmente, el cpdigo la sobreescribe con 'aptos'
******************************************************************************/
	
	require_once 'header.php' ; 
	
	$manual['tit'] = 'Matriz de Evaluación' ;
	$manual['tex'] = 'Esta opción permite ingresar o editar la matriz de calificación de un inmueble.
	
	El ingreso a esta opción se realiza a través de un enlace interno. La página presenta una tabla con los campos a evaluar. Los datos se pueden editar y al finalizar se debe presionar el Botón "Enviar" para que los cambios se guarden en la Base de Datos. Adicionalmente se incluye un enlace que permite retornar a la pantalla de edición.
	
	Enlace:
	<b>Volver a Edición:</b> al presionar redirecciona al listado de inmuebles del barrio respectivo.
	
	El formulario incluye los siguientes campos:
	<b>punt_total:</b> En este campo se muestra el puntaje total obtenido por el inmueble acorde con las calificaciones remitidas. El puntaje máximo es 100 y el mínimo es 0.
	<b>id_evaluado:</b> Identificador interno del inmueble.
	<b>fecha_evalua:</b> Fecha de la evaluación. Formato AAAA-MM-DD
	<b>evaluador:</b> nombre de la persona que realizó la evaluación.
	<b>categ_matriz:</b> tipo de matriz de evaluación a utilizar. Actualmente sólo está parametrizada la categoría "aptos" por lo que no se debe modificar.
	<b>Criterios 1 a N:</b> cada fila presenta sucesivamente los criterios de evaluación en la categoría definida. El usuario debe escoger el puntaje correspondiente del menú desplegable. En cada desplegable los mayores puntajes están situados en la parte superior del menú. 
	<b>...se listan todos los criterios</b>	
	<b>comentario:</b> texto reservado para que el usuario digite sus comentarios específicos con respecto a la evaluación.
	' ;
	
	echo VentanaHelp( $manual ) ;
	echo TituloPagina( 'Shortcuts > Matriz de Evaluación' ) ;
	
	
	if (isset( $_GET['id_inmueble'] )){
		$id_inmueble = $_GET['id_inmueble'] ;
	}else{
		echo "Falta id_inmueble" ;
		die() ;
	}
	
	$categoria = 'aptos' ;
	
	// Buscar los nombres y los pesos de los parámetros de la matriz
	$sql = "
		SELECT
			id_param,
			nom_var,
			nombre,
			peso,
			descalifica,
			desc_1,
			desc_2,
			desc_3,
			desc_4,
			desc_5
		FROM t_parametros
		WHERE categoria='$categoria'
	" ;
	
	$g_conexion->execute( $sql ) ;
	$arr_salidabd = array() ;
	while ( $arr_salidabd[] = $g_conexion->fetch()) ;
	array_pop( $arr_salidabd ) ;
	$toti_puntos = 0 ;
	foreach( $arr_salidabd as $linea ){
		$nombre = $linea['nombre'] ;
		
		if ( isset( $linea['descalifica'] )){
			$nombre .= "<br>(descalifica:{$linea['descalifica']})" ;
		}
		$arr_params[$linea['nom_var']] = $nombre ;
		$arr_peso[$linea['nom_var']] = $linea['peso'] ;
		$toti_puntos += $linea['peso'] * 7 ;
		
		$linVars = array() ; //areglo para guardar los identificadoes de cada puntaje
		$linVars[] = $linea['desc_1'] ;
		$linVars[] = $linea['desc_2'] ;
		$linVars[] = $linea['desc_3'] ;
		$linVars[] = $linea['desc_4'] ;
		$linVars[] = $linea['desc_5'] ;		
		$arr_nomVars[$linea['nom_var']] = $linVars ;		
	}
	msj ("Puntos máximos: $toti_puntos") ;
	ver_arr( $arr_params , 'arr_params' ) ;
	ver_arr( $arr_peso , 'arr_peso' ) ;
	//$arr_params tiene el nombre de los parametros
	//$arr_peso tiene el peso de los parametros de evaluaciónn
	//$arr_nomVars tiene un arreglo con los nombres indicativos de cada puntaje
	
	//Revisar si es la primera vez que se ingresa a la página o si ya trae datos para hacer el UPDATE a la tabla de la Base de Datos
	if (isset( $_GET['pag'] )){
		$pag = $_GET['pag'] ;
		if ( $pag == 2 ){			//la página trae datos del FORM para UPDATE
			$total = 0 ;
			$arr_sql = array() ;
			//preparar el UPDATE de SQL con los valores escogidos en el FORM para cada parámetro
			for ( $i = 1 ; $i < 31 ; $i++ ){
				$key = "v_$i" ;
				if ( isset( $_GET[$key] )){
					$punt = limpia_dat( $_GET[$key] ) ;
					$arr_sql[$key] = "$key = '$punt' " ;
					$total += $punt * $arr_peso[$key] ;
				}
			}
			
			$fecha_evalua = limpia_dat( $_GET['fecha_evalua'] ) ;
			$evaluador = limpia_dat( $_GET['evaluador'] ) ;
			$comentario = limpia_dat( $_GET['comentario'] ) ;		
			$punt_total = $total * 100 / $toti_puntos ;
			
			//armar el UPDATE con todos los datos a actualizar en la tabla t_matevaluacion
			$sql = 'UPDATE t_matevaluacion SET ' ;
			foreach ( $arr_sql as $val ){
				$sql .= $val . ', ' ;
			}
			$sql .= "
				fecha_evalua = '$fecha_evalua', 
				evaluador = '$evaluador', 
				fecha_evalua = '$fecha_evalua', 
				comentario = '$comentario', 
				punt_total = $punt_total
				WHERE id_evaluado = '$id_inmueble' AND categ_matriz='$categoria'
			" ;
			$g_conexion->execute ($sql) ;		
		}
	}
	
	//Se busca el registro de evaluación en la BD a ver si existe o si no se crea uno nuevo
	$sql = "
		SELECT 
		`id_evaluado` ,
		`fecha_evalua`,
		`evaluador` ,
		`categ_matriz` 
		FROM t_matevaluacion
		WHERE id_evaluado = '$id_inmueble' AND categ_matriz='$categoria'
	" ;
	
	$g_conexion->execute( $sql ) ;	
	$salidabd = $g_conexion->fetch() ;		//sólo devuelve un registro 
	if ( !($salidabd) ){
	//esto querría decir que el registro no ha sido creado en t_matevaluacion
		//query para crear el uevo registro en t_matevaluacion
		$sql = "
			INSERT INTO t_matevaluacion (
			id_evaluado,
			categ_matriz
			) VALUES (
			'$id_inmueble',
			'$categoria'
			)		
		" ;			
		$g_conexion->execute( $sql ) ;
		
		//query para actualizar la bandera en t_inmueble
		$sql = "
			UPDATE t_inmueble SET b_sgtosimp=2 WHERE id_inmueble='$id_inmueble'					
		" ;			
		$g_conexion->execute( $sql ) ;		
	}
	unset( $salidabd ) ;
	
	//query para hacer la consulta para traer los datos de puntajes de cada parametro de la BD
	$sql = "
		SELECT 
		`id_evaluado`,  
		`fecha_evalua`,
		`evaluador`,  
		`categ_matriz`,  
		v_1 ,
		v_2 ,
		v_3 ,
		v_4 ,
		v_5 ,
		v_6 ,
		v_7 ,
		v_8 ,
		v_9 ,
		v_10 ,
		v_11 ,
		v_12 ,
		v_13 ,
		v_14 ,
		v_15 ,
		v_16 ,
		v_17 ,
		v_18 ,
		v_19 ,
		v_20 ,
		v_21 ,
		v_22 ,
		v_23 ,
		v_24 ,
		v_25 ,
		v_26 ,
		v_27 ,
		v_28 ,
		v_29 ,
		v_30 ,
		punt_total ,
		`comentario`
		FROM t_matevaluacion
		WHERE id_evaluado = '$id_inmueble' AND categ_matriz='$categoria'		
	" ;	
	
	$g_conexion->execute( $sql ) ;	
	$salidabd = $g_conexion->fetch() ;		//sólo devuelve un registro por el momento (después incluir varios evaluadores)	
	ver_arr( $salidabd ) ;
	unset( $linea ) ;
	$linea['punt_total'] = $salidabd['punt_total'] ;
	$linea['id_evaluado'] = $salidabd['id_evaluado'] ;
	//ciclo para ir armando el campo correspondiente del FORM para cada uno de los parámetros
	foreach ( $salidabd as $key => $val ){
		if ( $key[0] == 'v' ){
			if ( isset( $arr_params[$key] )){
				$nom_variable = $arr_params[$key] ;
				//$linea[$nom_variable] = FormPuntos( $key , $val ) ;	//esta era la función anterior que no incluia el nombre del puntaje
				$linea[$nom_variable] = FormPuntos1( $key , $val , $arr_nomVars[$key] ) ;
			}else{
				//esta variable no está definida y no se incluye en el formulario
			}			
		}else{
			$linea[$key] = FormTexto( $key , $val ) ;
		}
	}
	//repasar nuevamente los campos no editables
	$linea['punt_total'] = $salidabd['punt_total'] ;
	$linea['id_evaluado'] = $salidabd['id_evaluado'] ;
	
	//imprimir el $html correspondiente 
	$g_html = '' ;
	$g_html .= "
		<div id='newmenu'>
		<ul id=\"nav\">
			<li><a href='edita_inm.php?f_idinmueble=$id_inmueble'>Volver a Edición</a></li>
		</ul>
		</div><br>" ;
	$g_html .= "<div class=\"center\"><form  action='$_SERVER[PHP_SELF]' name='FormEvalua' method='GET'>
	<INPUT TYPE='hidden' NAME='pag' VALUE='2'>
	<INPUT TYPE='hidden' NAME='id_inmueble' VALUE='$id_inmueble'>
	<INPUT TYPE='hidden' NAME='categoria' VALUE='$categoria'>
	<input type='submit' value='Enviar'>\n
	" ;
	//$g_html .="<table border=\"0\" width=\"200\">" ;

	$n = array() ;
	$g_html .= html_arreglo_uni( $linea , 1 , $n , "Matriz de Evaluación Inmueble $id_inmueble" , 300 ) ;
	$g_html .= '</table></form></div>' ;	
	
  	echo $g_html ;	

?>