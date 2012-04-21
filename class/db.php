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
		$this->$db = mysql_connect($db_host, $db_user, $db_password) or die ("Impossibile connettersi all'host" . $db_host);
		mysql_select_db ($db_name,$this->$db) or die ("Impossibile connettersi al database ". $db_name);
	}
	
	public function query($query) {
		
		if (!$this->result = @mysql_query(mysql_real_escape_string($query, $this->db))) {
			return 0;
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