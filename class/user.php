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
	
	function __construct() 
	
	{	
		include_once('db.php');
		
		if (include("config.php")) {
			include ("config.php");
		}
		else{
			include("../config.php");
		}
		
		$this->db = new MySQL($date['db_host'], $date['db_name'], $date['db_user'], $date['db_password']);
	}
	
	function is_logged () 
	
	{
		if ( isset($_COOKIE['email']) ) {
			$email = $_COOKIE['email'];
		}
		else {
			return FALSE;
		}
		
		$query = "SELECT sessid FROM users WHERE email = '$email'";
		
		if ( $send = $this->db->sendQuery ($query) ) {
			
			$result = mysql_fetch_array($send, MYSQL_ASSOC);
		
			if($result['sessid'] == @$_COOKIE['auth_key']) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		
		}
		else {
			echo mysql_error();
		}
	}
	
	function login($email, $password) 
	
	{
		$query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
		
		if ( $result = $this->db->sendQuery ($query) ) {
			$row = mysql_fetch_row($result);
			if ($row > 0) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
	}
	
	function register($email, $username, $password)

	{
		
		$query = "INSERT INTO users (id,email,username,password) VALUES ('NULL', '$email', '$username', '$password')";
		if( $this->db->sendQuery($query) ) {
			return TRUE;
		}
		else {
		    return FALSE;
		}
	}
	
	function setSession ($email) {
		$id = session_id();
		$this->db->sendQuery("UPDATE users SET sessid = '$id' WHERE email = '$email'");
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
		header("Location: ../index.php");
	}
	else{
		echo "Errore email/password";
	}
}

?>