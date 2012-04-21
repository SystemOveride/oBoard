<?php

/*
 * User Class
 */

class User 

{
	function __construct() {
		
		require_once '../includes/config.php';
		require_once 'db.php';
	
		$this->db = new MySQL($db_host, $db_name, $db_user, $db_password);
	}
	
	function login($email, $password) {
	
		$query = "SELECT * FROM " . $db_name . " WHERE email = " .$email . " AND password = " .$password .";";
		if ($result = $this->db->query ($query)) {
			$row = mysql_fetch_row($result);
			if ($row > 0) {
				return 1;
			}
			else {
				return 0;
			}
		}
	}
}


?>