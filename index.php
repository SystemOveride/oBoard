<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'class/template.php';
include_once("class/user.php");
include_once("class/forums.php");

$template = new Template();

$template->head("Home");
$template->load("login.tp");
$template->load("signup.tp");

if(User::getInstance()->is_logged()) {
	echo "Logged !";
}

Forum::getInstance()->show_forums();

?>