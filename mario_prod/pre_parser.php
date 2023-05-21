<?php
 /****************************************************************************
  20150122  >> pre_parser.php
  Proyecto: Obsevatorio Mario
  Esta página organiza los nombres del los archivos (o filtros) que se pueden
  configurar para consulta en metrocuadrado. De tal forma
  20160124: se ajusta porque los aptos ya no son de 500 millones sino todos
  ******************************************************************************/
  
	require_once 'header.php' ;
	include "manual_captura_autom.php" ;
	echo TituloPagina( 'Operación > Captura Automática > Datos N1 estándar' ) ;
	
	//Se crea y utiliza el formulario para captruar las variables de entrada a la página
	$forma = new Formulario() ;
	$forma->Insertar('f_fecha','Fecha proceso:',date('Y-m-d')) ;	
	$forma->siempre = TRUE ;
	$forma->Imprimir() ;
	$f_fecha = $forma->Sacar('f_fecha') ;
	
	$ini = substr( $f_fecha , 0 , 4 ) . substr( $f_fecha , 5 , 2 ) . substr( $f_fecha , 8 , 2 ) ;
	
	$g_html = '' ;
	$g_html .= "
		<ul id=\"nav\">
			<li><a href='parser1a.php?fecha=$f_fecha&zona=chap&antig=20a&tipo=apartamento&archivo={$ini}_chap_20a.txt'>Aptos en Chapinero con más de 20 años</a></li>
			<li><a href='parser1a.php?fecha=$f_fecha&zona=chap&antig=10a20&tipo=apartamento&archivo={$ini}_chap_10a20.txt'>Aptos en Chapinero de 10 a 20 años</a></li>
			<li><a href='parser1a.php?fecha=$f_fecha&zona=chap&antig=0a10&tipo=apartamento&archivo={$ini}_chap_0a10.txt'>Aptos en Chapinero de 0 a 10 años</a></li>
			<li><a href='parser1a.php?fecha=$f_fecha&zona=chap&antig=new&tipo=apartamento&archivo={$ini}_chap_new.txt'>Aptos en Chapinero nuevos</a></li>
			<br>			
			<li><a href='parser1a.php?fecha=$f_fecha&zona=nocc&antig=20a&tipo=apartamento&archivo={$ini}_noro_20a.txt'>Aptos en Norocc con más de 20 años</a></li>
			<li><a href='parser1a.php?fecha=$f_fecha&zona=nocc&antig=10a20&tipo=apartamento&archivo={$ini}_noro_10a20.txt'>Aptos en Norocc de 10 a 20 años</a></li>
			<li><a href='parser1a.php?fecha=$f_fecha&zona=nocc&antig=0a10&tipo=apartamento&archivo={$ini}_noro_0a10.txt'>Aptos en Norocc de 0 a 10 años</a></li>
			<li><a href='parser1a.php?fecha=$f_fecha&zona=nocc&antig=new&tipo=apartamento&archivo={$ini}_noro_new.txt'>Aptos en Norocc nuevos</a></li>
			<br>			
			<li><a href='parser1a.php?fecha=$f_fecha&zona=nort&antig=20a&tipo=apartamento&archivo={$ini}_nort_20a.txt'>Aptos en Norte con más de 20 años</a></li>
			<li><a href='parser1a.php?fecha=$f_fecha&zona=nort&antig=10a20&tipo=apartamento&archivo={$ini}_nort_10a20.txt'>Aptos en Norte de 10 a 20 años</a></li>			
			<li><a href='parser1a.php?fecha=$f_fecha&zona=nort&antig=0a10&tipo=apartamento&archivo={$ini}_nort_0a10.txt'>Aptos en Norte de 0 a 10 años</a></li>
			<li><a href='parser1a.php?fecha=$f_fecha&zona=nort&antig=new&tipo=apartamento&archivo={$ini}_nort_new.txt'>Aptos en Norte nuevos</a></li>
			<br>			
			<li><a href='parser1a.php?fecha=$f_fecha&zona=occi&antig=20a&tipo=apartamento&archivo={$ini}_occi_20a.txt'>Aptos en Occidente con más de 20 años</a></li>
			<li><a href='parser1a.php?fecha=$f_fecha&zona=occi&antig=10a20&tipo=apartamento&archivo={$ini}_occi_10a20.txt'>Aptos en Occidente  de 10 a 20 años años</a></li>
			<li><a href='parser1a.php?fecha=$f_fecha&zona=occi&antig=0a10&tipo=apartamento&archivo={$ini}_occi_0a10.txt'>Aptos en Occidente de 0 a 10 años</a></li>			
			<li><a href='parser1a.php?fecha=$f_fecha&zona=occi&antig=new&tipo=apartamento&archivo={$ini}_occi_new.txt'>Aptos en Occidente nuevos</a></li>
		</ul>
	" ;

	echo $g_html ;
	
?>
