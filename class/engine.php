<?php


#####################
# Gamma Engine v0.3 #
#   (Lando rules)   #
#####################


require '../config.php';
require_once 'db.php';

class Engine extends MySQL {
	private $template;
	private $forks = NULL;
	private $prev = NULL;
	private $next = NULL;

	public function __construct($template, $use_db=true){
		$this->template = $this->load_template($template);
		if ($use_db){
			parent::__construct();
		}
	}
	private function load_template($template){
		if (!$tmpl = file_get_contents($template)){
			die("Impossibile caricare il template '$template`");
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
	public function autofill_iter($query){
		($this->db) or die("Non connesso al database.");
		if ($this->forks == NULL){
			$this->forks = Array();
		}
		$res = $this->query($query);
		(mysql_num_rows($res) < 1) and die("Set vuoto passato ad Engine::autofill_iter()");
		while ($row = mysql_fetch_assoc($res)){
			foreach ($row as $name => $value){
				$this->fill_fork($name, $value);
			}
		}
	}
	public function fill($placeholder, $value){
		($this->has_placeholder($placeholder)) or die("Placeholder $placeholder non trovato.");
		$this->template = str_replace("<!-- %$placeholder% -->", $value, $this->template);
	}
	public function fill_fork($placeholder, $value){
		($this->has_placeholder($placeholder)) or die("Placeholder $placeholder non trovato.");
		$fork = str_replace("<!-- %$placeholder% -->", $value, $this->template);
		array_push($this->forks, $fork);
	}
	public function prepend($ei, $request=false){
		($ei instanceof Engine) or die("Non istanza di Engine passata ad Engine::prepend()");
		($request) ? NULL : $ei->append($this, true);

		$this->prev = $ei;
	}
	public function append($ei, $request=false){
		($ei instanceof Engine) or die("Non istanza di Engine passata ad Engine::append()");
		($request) ? NULL : $ei->prepend($this, true);
		$this->next = $ei;
	}
	public function detach_prev($request=false){
		($this->prev) or die("Chiamata Engine::detach_prev() su un'istanza non collegata.");
		($request) ? NULL : $this->prev->detach_next(true);
		$this->prev = NULL;
	}
	public function detach_next($request=false){
		($this->next) or die("Chiamata Engine::detach_next() su un'istanza non collegata.");
		($request) ? NULL : $this->next->detach_prev(true);
		$this->next = NULL;
	}
	public function getprev(){
		return $this->prev;
	}
	public function getnext(){
		return $this->next;
	}
	public function render(){
		return ($this->forks) ? implode("\n", $this->forks) : $this->template;
	}
	public function cascading_render(){
		$rendered = $this->render() . "\n";
		if ($this->next != NULL){
			$tmp = $this->next;
			do {
				$rendered = $tmp->render() . "\n";
				$tmp = $tmp->getnext();
			} while ($tmp->getnext() != NULL);
		}
		return $rendered;
	}
	public function full_render(){
		$first = $this;
		while ($first->getprev() != NULL){
			$first = $first->getprev();
		}
		$rendered = $first->cascading_render();
		return $rendered;
	}
	public function __destruct(){
		$this->prev->detach_next(true);
		$this->next->detach_prev(true);
		$this->prev->append($this->next);
		parent::destruct();
	}
}

?>
