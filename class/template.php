<?php

class Template

{
	function __construct() {
		include_once 'engine.php';
		$this->engine = new Engine();
	}
	
	function load($tp) {
		echo $this->engine->stampTP("template/".$tp);
	} 
	
	function head($title) {
		echo $this->engine->assign ("template/header.tp", "TITLE", $title);
	}
	
}


?>