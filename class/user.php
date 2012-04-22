<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
 * User Class
 */

class User 

{
	private static $_instance;
	
	function __construct() {
		
		include_once('db.php');
		include("config.php");
		
		$this->db = new MySQL($date['db_host'], $date['db_name'], $date['db_user'], $date['db_password']);
	}
	
	function is_logged () {
		
		if ( isset($_COOKIE['email']) ) {
			$email = $_COOKIE['email'];
		}
		else {
			return FALSE;
		}
		
		$query = "SELECT sessid FROM utenti WHERE email = '$email'";
		
		if ( $send = $this->db->sendQuery ($query) ) {
			
			$result = mysql_fetch_array($send, MYSQL_ASSOC);
			echo $result['sessid'];
		
			if($result['sessid'] == @$_COOKIE['auth_key']) {
				return 1;
			}
			else {
				return 0;
			}
		
		}
		else {
			echo mysql_error();
		}
	}
	
	function login($email, $password) {
	
		$query = "SELECT * FROM utenti WHERE email = '$email' AND password = '$password'";
		
		if ( $result = $this->db->sendQuery ($query) ) {
			$row = mysql_fetch_row($result);
			if ($row > 0) {
				return 1;
			}
			else {
				return 0;
			}
		}
	}
	
	function register($email, $username, $password) {
		
		$query = "INSERT INTO utenti (id,email,username,password) VALUES ('NULL', '$email', '$username', '$password')";
		if( $this->db->sendQuery($query) ) {
			return 1;
		}
		else {
		    return 0;
		}
	}
	
	function setSession ($email) {
		$id = session_id();
		$this->db->sendQuery("UPDATE utenti SET sessid = '$id' WHERE email = '$email'");
		setcookie("email", $email, time() + 3600, '/');
		setcookie("auth_key", $id, time() + 3600, '/');
	}
	
	public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
	
	
}

require_once 'security.php';



if(isset($_POST['signup']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['username'])) {
	if($user->register(secureVar($_POST['email']), secureVar($_POST['username']), secureVar($_POST['password']))) {
		setSession(secureVar($_POST['email']));
		echo "Registrato";
	}
	else {
		echo "Errore nella registrazione .";
	}
}

if(isset($_POST['login']) && isset($_POST['email']) && isset($_POST['password'])) {
	if(User::getInstance()->login(secureVar($_POST['email']), secureVar($_POST['password']))) {
		User::getInstance()->setSession(secureVar($_POST['email']));
		echo "Loggato";
	}
	else{
		echo "Errore email/password";
	}
}

?>