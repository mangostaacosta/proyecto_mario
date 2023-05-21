<?php

require_once 'header.php' ;

class Formulario {
	public $arr_variable ;
	public $form_name ;
	public $numero ;
	public $siempre ;
	public $virgen ;

	function Formulario ( $form_name = 'FormMio') {
		$this->numero = 0 ;
		$this->siempre = FALSE ;
		$this->arr_variable = array() ;
		$this->form_name = $form_name ;
	}
	
	function Insertar( $nombre , $texto='' , $default='' ){
		if ( $texto == '' ){
			$texto = $nombre ;
		}
		$this->arr_variable[$nombre] = array( 'nombre'=>$nombre, 'texto'=>$texto, 'omision'=>$default ) ;
		$this->numero++ ;
	}
	
	function Imprimir(){
		$this->virgen = FALSE ;
		foreach ( $this->arr_variable as $key => $val ){
			if (isset( $_GET[$key] )){
				$this->arr_variable[$key]['valor'] = $_GET[$key] ;
			}else{
				$this->arr_variable[$key]['valor'] = $val['omision'] ;
				$this->virgen = TRUE ;
			}
		}
		
		if ( $this->virgen OR $this->siempre ){
		//en estos estados se imprime el formulario
			$html = "<div class=\"center\"><form  action='$_SERVER[PHP_SELF]' method='GET' name='{$this->form_name}'>\n" ;
			$html .="<table border=\"0\" width=\"350\">\n" ;
			foreach ( $this->arr_variable as $key=>$val ){
				$html .= "<tr><td>{$val['texto']}</td><td><INPUT TYPE='text' name='{$val['nombre']}' VALUE='{$val['valor']}'></td></tr>\n" ; 
			}
			$html .= "<tr><td colspan=2 align=right><INPUT TYPE='submit' name='boton' VALUE='OK'></td></tr>\n" ;
			$html .= "</table></form></div>\n" ;
		}else{
			$html = '' ;
			foreach ( $this->arr_variable as $key=>$val ){
				$html .= "{$val['texto']} => {$val['valor']} <br>\n" ; 
			}
		}
		echo $html ;
		
		if ( $this->virgen AND !($this->siempre)){
			die() ;
		}		
	}
	
	function Sacar( $nombre ){
		ver_arr( $this->arr_variable , 'this->arr_variable') ;
		return ( $this->arr_variable[$nombre]['valor'] ) ;
	}	
}

/*
$forma = new Formulario() ;
$forma->Insertar('f_fecha','',date('Y-m-d')) ;
$forma->Insertar('nom_campo','Variable a Analizar','precio_metro') ;
//$forma->siempre = TRUE ;
$forma->Imprimir() ;
$f_fecha = $forma->Sacar('f_fecha') ;
$id_campo = $forma->Sacar('nom_campo') ;

if ( $forma->virgen ){
	echo 'primera vez' ;
}else{
	echo "Los datos del formulario: $f_fecha, $id_campo" ;
}
*/


Class FormDespl extends Formulario{
	private $arr_desplegable ;
	private $numero2 ;
	

	function FormDespl() {
		$this->numero2 = 0 ;
		$this->arr_desplegable = array() ;
		//$this->Formulario() ;
	}

	//ojo los valores que no vengan del form se le deben insertar vía el tercer parámetro
	function InsertarDespl( $nombre , $texto='' , $default='' , $arr_opciones ){
		if ( $texto == '' ){
			$texto = $nombre ;
		}
		$this->arr_desplegable[$nombre] = array( 'nombre'=>$nombre, 'texto'=>$texto, 'omision'=>$default , 'opciones' => $arr_opciones ) ;
		$this->numero2++ ;
	}
	
	function SacarDespl( $nombre ){
		return ( $this->arr_desplegable[$nombre]['valor'] ) ;
	}	

	
	function Imprimir(){
		$this->virgen = FALSE ; 
		foreach ( $this->arr_variable as $key => $val ){
			if (isset( $_GET[$key] )){
				$this->arr_variable[$key]['valor'] = $_GET[$key] ;
			}else{				
				$this->arr_variable[$key]['valor'] = $val['omision'] ;
				$this->virgen = TRUE ;
			}
		}
		foreach ( $this->arr_desplegable as $key => $val ){			
			if (isset( $_GET[$key] )){
				$this->arr_desplegable[$key]['valor'] = $_GET[$key] ;
			}else{				
				$this->arr_desplegable[$key]['valor'] = $val['omision'] ;
				$this->virgen = TRUE ;
			}
		}
		
		ver_arr( $this->arr_variable ) ;
		$html = '' ;
		
		if ( $this->virgen OR $this->siempre ){
		//en estos estados se imprime el formulario
			$html .= "<div class=\"center\"><form  action='$_SERVER[PHP_SELF]' method='GET' name='{$this->form_name}'>\n" ;
			$html .="<table border=\"0\" width=\"350\">\n" ;
			foreach ( $this->arr_variable as $key=>$val ){
				$html .= "<tr><td>{$val['texto']}</td><td><INPUT TYPE='text' name='{$val['nombre']}' VALUE='{$val['valor']}'></td></tr>\n" ; 
			}
			//OJO ACA ESTOY LA IDEA ES QUE EL ARREGLO DE OPCIONES TIENE QUE SER RECORRIDO Y COMPARADO CON EL DEFAULT
			
			foreach ( $this->arr_desplegable as $key1 => $arr_pregunta ){
				$html .= "<tr><td>{$arr_pregunta['texto']}</td><td><select name='{$arr_pregunta['nombre']}'>\n" ; 	
				foreach ( $arr_pregunta['opciones'] as $key2 => $val ){
					if ( $key2 == $arr_pregunta['valor'] ){
						$html .= "<option  selected=\"selected\"  autocomplete=\"off\" value='$key2'>$val</option>\n" ;
					}else{
						$html .= "<option value='$key2'>$val</option>\n" ;
					}
				}
				$html .="</td></tr>\n" ;
			}
			$html .= "<tr><td colspan=2 align=right><INPUT TYPE='submit' name='boton' VALUE='OK'></td></tr>\n" ;			
			$html .= "</table></form></div>\n" ;
		}else{
			foreach ( $this->arr_variable as $key=>$val ){
				$html .= "{$val['texto']} => {$val['valor']} <br>\n" ; 
			}
			foreach ( $this->arr_desplegable as $key=>$val ){
				$html .= "{$val['texto']} => {$val['valor']} <br>\n" ; 
			}
		}
		echo $html ;
		
		if ( $this->virgen AND !($this->siempre)){
			die() ;
		}
	}
	
	
}

/*
$arr_fechas = array('2015-02-03'=>'2015-02-03','2015-02-25'=>'2015-02-25','2015-03-03'=>'2015-03-03','2015-04-03'=>'2015-04-03','2015-05-25'=>'2015-05-25') ;
ver_arr($arr_fechas) ;
$form1 = new FormDespl() ;
$form1->InsertarDespl('f_fecha1','Fecha inicial:','', $arr_fechas ) ;
$form1->InsertarDespl('f_fecha2','Fecha final:','', $arr_fechas ) ;
$form1->Insertar('f_campo','Campo solicitado:','precio') ;
$form1->siempre = TRUE ;
$form1->Imprimir() ;
$f_fecha1 = $form1->SacarDespl('f_fecha1') ;
$f_fecha2 = $form1->SacarDespl('f_fecha2') ;
$f_campo = $form1->Sacar('f_campo') ;
MsjE( "$f_fecha1 , $f_fecha2 , $f_campo" ) ;
*/

?>
