<?php

/*
 * Engine Template System ( Thanks to (and only to) Lando )
 */

require '../config.php';
require_once 'db.php';

class Engine extends MySQL {
	private $template;

	public function __construct($template, $use_db=true){
		$this->template = $this->load_template($template);
		if ($use_db){
			parent::__construct();
		}
	}
	private function load_template($template){
		if (!$tmpl = file_get_contents($template)){
			die("Couldn't load template file '$template`");
		}
		return $tmpl;
	}
	private function has_placeholder($name){
		return !(strpos($this->template, "<!-- $name -->") === false);
	}
	public function autofill($query){
		($this->db) or die("Non connesso al database.");
		$res = $this->query($query);
		(mysql_num_rows($res) != 1) and die("Numero di righe passate ad Engine::autofill() diverso da 1");
		$row = mysql_fetch_assoc($res);
		foreach ($row as $name => $value){
			$this->fill($name, $value);
		}
	}
	public function fill($placeholder, $value){
		($this->has_placeholder($placeholder)) or die("Placeholder $placeholder not found.");
		$this->template = str_replace("<!-- $placeholder -->", $value, $this->template);
	}
	public function render($tp){
		return $this->template;
	}
	public function __destruct(){
		parent::destruct();
	}
}

?>
