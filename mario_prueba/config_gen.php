<?php
  /****************************************************************************
  20141012  >> config_gen.php
  Proyecto: Encuestas Uniandes
  Guarda las configuraciones que son inguales en pruebas y producción
  ******************************************************************************/
    
	$g_archivobase = "../mario_data/ofertaM2.dat" ;
	$g_archivofinal = "../mario_data/resultM2.dat" ;
	$g_archivonivel3 = "../mario_data/nivel3.dat" ;
	$g_ruta = "../mario_data/" ;
	$g_indice = "" ;
	
	define("FACTOR_VENTA", 1.22 ) ;   //0.97x1.25
	define("FACTOR_OPTIMISTA", 1.34 ) ;
	define("FACTOR_OBRA", 0.08 ) ;
	define("FACTOR_OTROS", 0.03 ) ;
	define("FIJOS", 2 ) ;
	define("FACTOR_DCTO", 0.9 ) ;	
	define("PESO_MERCADO", 0.75 ) ;
	define("FACTOR_NUEVO", 0.80 ) ;

  //$dirxml = "xml" ;
  //$pathusuario = "usuarios/" ;
    
?>