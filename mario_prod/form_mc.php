<?php
/****************************************************************************
  20150225  >> form_mc.php
  Proyecto: Obsevatorio Mario
  Formulario donde se insertan los parámetros para la búsqueda en metrocuadrado  
******************************************************************************/
	
	require_once 'header.php' ;
	include "manual_captura_autom.php" ;
	echo TituloPagina( 'Operación > Captura Automática > Descarga MC' ) ;

	$arr_form['Ciudad'] = 'Bogota' ;
	$arr_form['Tipo de Inmueble'] = 'Apartamento' ;
		
	$arr_form['Zona'] = '
		<select lang="es" name="zona">			
			<option selected="selected" value="chapinero">Chapinero</option>
			<option value="norte">Norte</option>
			<option value="noroccidente">Noroccidente</option>			
			<option value="occidental">Occidental</option>			
			<option value="centro">Centro</option>
			<option value="Todas">Todas las zonas</option>
		</select>' ;
	$arr_form['Antigüedad'] = '
		<select lang="es" name="tiempo">			
			<option values="Para Estrenar">Para Estrenar</option>
			<option value="Entre 0 y 5 años">Entre 0 y 5 años</option>
			<option value="Entre 5 y 10 años">Entre 5 y 10 años</option>
			<option value="Entre 10 y 20 años">Entre 10 y 20 años</option>
			<option value="Más de 20 años">Más de 20 años</option>
			<option values="ESPECIAL">ESPECIAL</option>
		</select>' ;
	$arr_form['Cantidad de Resultados'] = FormTexto( 'max' , '50' , '' , 0 , 10 ) ;		
	
	
	
	
	/*
	$arr_form['Estado'] = '	
			<select lang="es" name="estado">
			<option selected="selected" value="Usado">Usado</option>
			<option value="Nuevo">Nuevo</option>
		</select>' ;	
	$arr_form['Tipo de Inmueble'] = '
		<select lang="es" id="TipoInmueble" name="TipoInmueble">    
            <optgroup label="Vivenda">
                <option value="Casas">Casas</option>
                <option selected="selected" value="Apartamentos">Apartamentos</option>
                <option value="Finca">Finca</option>
                <option value="Edificio de apartamentos">Edificio de apartamentos</option>
            </optgroup>
            <optgroup label="Comercial">
                <option value="Oficina">Oficina</option>
                <option value="Bodega">Bodega</option>
                <option value="Consultorio">Consultorio</option>
                <option value="Local">Local</option>
                <option value="Lote">Lote</option>
                <option value="Edificio de oficinas">Edificio de oficinas</option>
            </optgroup>
            <optgroup label="Turismo y descanso">
                <option value="Apartamento">Apartamento</option>
                <option value="Fincas y Cabañas">Fincas y Cabañas</option>
                <option value="Hoteles">Hoteles</option>
            </optgroup>
        </select>' ;
	$arr_form['Precio Mínimo (millones)'] = FormTexto( 'pd' , 0 , '' , 0 , 10) ;
	$arr_form['Precio Máximo (millones)'] = FormTexto( 'ph' , 500 , '' , 0 , 10 ) ;
		*/
	
	$g_html = '' ;	
	$g_html .= "
		<div class=\"center\"><form  action='parser0.php' name='FormEvalua' method='POST'>
		<input type='submit' value='Enviar'>\n" ;
		
	$n = array() ;
	$g_html .= html_arreglo_uni( $arr_form , 1 , $n , "Formulario de Consulta WEB" , 450 ) ;
	$g_html .= '</table></form></div>' ;	
	
  	echo $g_html ;	
?>
