<?php

class DbConnection {
	var $DbName;
	var $User;
	var $Password;

	var $Host;
	var $Port;
	var $Options;
	var $Tty;

	var $Connection;
	
	var $probando ;
	var $ejecutando ;

	function DbConnection ( $myName, $myUser, $myPassword, $myHost = "", $myPort = "", $myOptions = "", $myTty = "") {
		$this->Dbname = $myName;
		$this->User = $myUser;
		$this->Password = $myPassword;

		$this->Host = $myHost;
		$this->Port = $myPort;
		$this->Options = $myOptions;
		$this->Tty = $myTty;
		
		$this->probando = 0 ;
		$this->ejecutando = 1 ;
		
		//$this->Connection = $this->connect ();
		}

	function connect () {
		// abstract
		}

	function disconnect () {
		//  abstract
		}

	}

	
	/****************************************
	/* class MySQLConnection                    
	/* Conecta a una base de datos MySQL
	/***************************************/

class MySQLConnection extends DbConnection {
	var $Result;
	var $NumRows;
	
	function connect () {

		$this->Connection = mysql_connect($this->Host, $this->User, $this->Password)
			or die (mysql_error()); 
		mysql_select_db($this->Dbname,$this->Connection ); 
		return $this->Connection;
	}
	function conectar () {
	//esta configurado para operar como conexion permanente
		$this->Connection = mysql_pconnect($this->Host, $this->User, $this->Password)
			or die (mysql_error()); 
		mysql_select_db($this->Dbname,$this->Connection ); 
		return $this->Connection;
	}


	function disconnect () {
		$disconnected = mysql_close ( $this->Connection );
		return $disconnected;
	}

  function execute ( $sql ) {		
		if ( $this->ejecutando == 0 ){
			echo( $sql . " SIN EJECUTAR<br>");
			return ;
		}
		if ( $this->probando == 1){
			echo( $sql . "<br>");
			$this->Result = mysql_query($sql,  $this->Connection ) ;
		}else{
			$this->Result = mysql_query($sql,  $this->Connection ) ;
		}			
		//$this->NumRows = mysql_num_rows ( $this->Result );
		return $this->Result;
	}

	function execute_back ( $sql ) {
		
		if ( $this->probando == 1)
			echo( $sql . "<br>");
		$this->Result = mysql_query($sql,  $this->Connection ) or die(mysql_error( $this->Connection ));
		$this->NumRows = mysql_num_rows ( $this->Result );
		return $this->Result;
	}
	
	function affectedRows ( ) {
		//llamamos este metodo para saber las filas afectadas por un UPDATE, INSERT o DELETE.
		//int mysql_affected_rows ([int link_identifier])
		return  mysql_affected_rows ( $this->Connection);
	}
	function returnedRows ( ) {
		//llamamos este metodo para saber las filas retornadas por un SELECT
		//int mysql_num_rows (int result)
		//mysql_num_rows() returns the number of rows in a result set. 
		//This command is only valid for SELECT statements.
		return  mysql_num_rows ( $this->Result );
		//return  $this->NumRows ;
	}
	function fetchRow ( $row ) {
		return  mysql_fetch_array ( $this->Result);
		}
	function fetchObject ( $row ) {
		return  mysql_fetch_object ( $this->Result );
	}
	function muestre( $i = 1 ){
		$this->probando = $i ;
	}
	function fetch(){		//devuvelve arreglo asociativo
		return mysql_fetch_assoc( $this->Result ) ;
	}
	function fetch1(){  //devuvelve arreglo indexado
		return mysql_fetch_row( $this->Result ) ;
	}
	function ultimoID(){
	 $sql = "SELECT LAST_INSERT_ID() AS max " ;
	 $this->execute( $sql ) ;
	 $arr = $this->fetch() ;
	 return $arr['max'] ;
	}
}
?>
