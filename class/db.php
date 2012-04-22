<?php

/*
 * Database Connection Class
 */

class MySQL 

{
	
	private $db = null;
	private $result = null;
	
	function __construct($db_host, $db_name, $db_user, $db_password) {
		$this->connect($db_host, $db_name, $db_user, $db_password);
	}
	
	public function connect($db_host, $db_name, $db_user, $db_password) {
		if(!$this->db = mysql_connect($db_host, $db_user, $db_password)) {
			die ("Impossibile connettersi all'host" . $db_host);
		}
		
		if(!mysql_select_db ($db_name, $this->db)) {
			die ("Impossibile connettersi al database ". $db_name);
		}
	}
	
	public function sendQuery($query) {
		
		if (!$this->result = mysql_query($query, $this->db)) {
			die(mysql_error());
		}
		else{
			return $this->result;
		}
		
	}
	
	public function __destruct() {
		@mysql_close ($this->db);
	}
	
}

?>