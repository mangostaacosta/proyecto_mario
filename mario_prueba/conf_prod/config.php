<?php
  /****************************************************************************
  20141012  >> config.php
  Proyecto: Encuestas Uniandes
  Guarda las configuraciones referentes a la conexión con la BD, nivel de error
  y rutas de los directorios en caso de ser necesario, así como la muestra de
  mensajes.    
  ******************************************************************************/
  
  ini_set('display_errors', TRUE) ;
  error_reporting(E_ALL) ;  //subir el nivel de error
  //error_reporting(0) ;  //bajar el nivel de error  
  $g_depura = 1 ;
  $g_depura = 0 ;
  
  $bd_nombre = 'renovaci_base' ;
  //$bd_nombre = 'letsgobi_ru_prueba' ;
  $bd_usuario = 'renovaci_base' ;
  $bd_clave = 'yBzVvcHVvFKFSNxQ' ;
  
  $g_archivobase = "mario_data/ofertaM2.dat" ;
  $g_archivofinal = "mario_data/resultM2.dat" ;
  $g_archivonivel3 = "mario_data/nivel3.dat" ;
  $g_ruta = "mario_data/" ;
  
  //$g_bd = 0 ;  //0:Solo muestra SQL no lo ejecuta
  $g_indice = "" ;
  
  
  //$dirxml = "xml" ;
  //$pathusuario = "usuarios/" ;
    
?>
