<?php
  /****************************************************************************
  20141012  >> header.php
  Proyecto: Encuestas Uniandes
  Encabezado que hace el llamado a los Required Scripts, a ser incluid al comienzo
  los Scripts funcionales
  ******************************************************************************/

  require_once "conf/config.php" ;	//incialización de variables específicas de prueba/producción
  require_once "config_gen.php" ;	//inicialización de variables independientes del ambiente, relacionadas con las ecuaciones usadas en el aplicativo
  require_once "funciones.php" ;	//definicón de funciones genéricas e.g. depuración de texto
  require_once "funciones_propias.php" ;	//definición de funciones específicias del aplicativo como consultas a la base de datos
  //require_once "funcionesmenu.php" ;
  require_once "conexion_bd.php" ;	//inicalización de la conexión a la base de datos
  require_once "cl_formulario.php" ;	//definición de clase para uso de formularios web
  
  require_once "conf/header.htm" ;	//encabezado html de la página
  //include 'menu.html' ;
  include 'menu2.html' ;	//menu de opciones
  
  //echo "FIN Header <br>" ;
  
?>
