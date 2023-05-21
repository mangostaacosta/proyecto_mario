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
  //$g_bd = 0 ;  //0:Solo muestra SQL no lo ejecuta
  
  $bd_nombre = 'letsgobi_ru_prueba' ;
  $bd_usuario = 'letsgobi_RU' ;
  $bd_clave = 'pepe9124' ;  
    
?>
