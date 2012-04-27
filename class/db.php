<?php

/*
 * Database Connection Class
 */

require_once '../config.php';

class MySQL {
	protected $db = NULL;

	public function __construct(){
		if(!$this->db = mysql_connect($dbinfo['host'], $dbinfo['user'], $dbinfo['password'])){
			die ("Impossibile connettersi all'host." . $db_host);
		} else if(!mysql_select_db($dbinfo['name'], $this->db)) {
			die ("Impossibile connettersi al database.". $db_name);
		}
	}

	public function sanitize($value){
		return mysql_real_escape_string(htmlentities(stripslashes($value, $this->db)));
	}

	public function sanitize_array(&$values){
		foreach ($values as &$value){
			$value = $this->sanitize($value);
		}
	}

	public function query($query_string){
		if (!$res = mysql_query($query_string, $this->db)) {
			die(mysql_error());
		} else{
			return $res;
		}
	}
	public function mkpasswd($plain){
		$salt = "";
		$asctab = Array();
		foreach (range(32, 127) as $code){
			array_push($asctab, chr($code));
		}
		shuffle($asctab);
		for($i=0; $i < 31; $i++){
			$r = mt_rand(32, 127);
			$salt .= $asctab[r];
		}
		$salt = sha1($salt);
		$passwd = md5($plain . $salt) . "." . $salt;
		return $passwd;
	}
	public function __destruct() {
		@mysql_close($this->db);
	}
}

?>
