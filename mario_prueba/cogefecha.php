<?php
  /*****************************************************************************
  Pide la fecha. Esta se puede usar en cualquier archivo que la necesite
  POST : devuelve tres variables : $g_fecha , $g_fecha2
  ******************************************************************************/
	
	if ( ! isset( $nformato1)) {
		$nformato1 = "Fecha Solicitada:" ;
	}
	if ( ! isset( $nformato2)) {
		$nformato2 = "No usar:" ;
	}
	
	$pag = 1 ;
  	
	if (isset( $_GET['g_fecha'] )){
		$g_fecha = $_GET['g_fecha'] ;		
	}else{
		$g_fecha_link = date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"))) ;
	}
	if (isset( $_GET['g_fecha2'] )){
		$g_fecha2 = $_GET['g_fecha2'] ;	
	}else{
		$g_fecha2 = $nrespuesta2 ;	
	}

	if ( ! isset($g_fecha)){
		$g_fecha = $g_fecha_link ;
		$g_html = "<form  action='$_SERVER[PHP_SELF]' method='GET'><INPUT TYPE='hidden' NAME='pag' VALUE='$pag'>" ;
		$g_html .="<table border=\"0\" width=\"300\">" ;
		$g_html .= "<tr><td>$nformato1</td><td><INPUT TYPE='text' name='g_fecha' VALUE='$g_fecha'></td></tr> " ; 
		$g_html .= "<tr><td>$nformato2</td><td><INPUT TYPE='text' name='g_fecha2' VALUE='$g_fecha2'></td></tr> " ; 
		$g_html .= "<tr><td colspan=2 align=right><INPUT TYPE='submit' name='boton' VALUE='OK'></td></tr></table>" ;
		$g_html .= "</form>"  ;
		echo $g_html ;
		die ;
	}
	echo "$nformato1 $g_fecha<br>" ;
	echo "$nformato2 $g_fecha2<br>" ;

	

/*	
  if ( ! isset($g_fecha)){
    if ( $g_fecha_link != '' ){
      $g_fecha = $g_fecha_link ;
    }else{
      $g_fecha = date('Y-m-d',mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"))) ;
    }
    $g_html = "<form  action='$_SERVER[PHP_SELF]' method='GET'><INPUT TYPE='hidden' NAME='pag' VALUE='$pag'>" ;
    $g_html .="<table border=\"0\" width=\"300\">" ;
    $g_html .= "<tr><td>$g_nformato1</td><td><INPUT TYPE='text' name='g_fecha' VALUE='$g_fecha'></td></tr> " ;
    $g_html .= "<tr><td>$g_nformato2</td><td><INPUT TYPE='text' name='g_portafolio' VALUE='0'></td></tr> " ;
    $g_html .= "<tr><td>$g_nformato3</td><td><INPUT TYPE='text' name='g_form3' VALUE='0'></td></tr> " ;
    $g_html .= "<tr><td colspan=2 align=right><INPUT TYPE='submit' name='boton' VALUE='OK'></td></tr></table>" ;
    $g_html .= "</form>"  ;
    echo $g_html ;
    die ;
  }
*/

  ?>
