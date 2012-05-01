<?php

########################
#  Gamma Engine v0.31  #
#    (Lando  rules)    #
######################31

require_once 'db.php';

class Engine extends MySQL {
	private $tname;
	private $template;
	private $forks = NULL;
	private $prev = NULL;
	private $next = NULL;
	private $tmp = NULL;
	/*
	private $inner = NULL;
	private $inherited = FALSE;
	private $query = NULL; */

	/* Internals */
	public function __construct($template, $use_db=true){
		$this->template = $this->load_template($template);
		if ($use_db){
			parent::__construct();
		}
	}
	public function __destruct(){
		($this->prev) && $this->prev->detach_next(true);
		($this->next) && $this->next->detach_prev(true);
		($this->prev && $this->next) && $this->prev->append($this->next);
		parent::__destruct();
	}
	public function getprev(){
		return $this->prev;
	}
	public function getnext(){
		return $this->next;
	}
	/*
	public function setquery($qs){
		$this->query = $qs;
	}
	*/

	/* Template */
	private function load_template($template){
		$this->tname = $template;
		($tmpl = file_get_contents("template/".$template)) or die("Impossibile caricare il template '$template`");
		return $tmpl;
	}
	private function has_placeholder($name){
		return !(strpos($this->template, "<!-- %$name% -->") === false);
	}

	/* SQL-driven Filling */
	public function autofill($query){
		($this->db) or die("Non connesso al database.");
		($query) or die("Invalid query supplied to Engine(\"" . $this->tname . "\")::autofill()");
		$res = $this->query($query);
		(mysql_num_rows($res) != 1) and die("Numero di righe passate ad Engine(\"" . $this->tname . "\")::autofill() diverso da 1");
		$row = mysql_fetch_assoc($res);
		foreach ($row as $name => $value){
			$this->fill($name, $value);
		}
	}
	public function autofill_iter($query){
		($this->db) or die("Non connesso al database.");
		($query) or die("Invalid query supplied to Engine(\"" . $this->tname . "\")::autofill_iter()");
		if ($this->forks == NULL){
			$this->forks = Array();
		}
		$res = $this->query($query);
		(mysql_num_rows($res) < 1) and die("Set vuoto passato ad Engine(\"" . $this->tname . "\")::autofill_iter()");
		while ($row = mysql_fetch_assoc($res)){
			foreach ($row as $name => $value){
				$this->fill_fork($name, $value);
			}
		}
	}
	/*
	public function autofill_recur(){

	}
	*/

	/* Filling */
	public function fill($placeholder, $value){
		($this->has_placeholder($placeholder)) or die("Placeholder $placeholder non trovato.");
		$this->template = str_replace("<!-- %$placeholder% -->", $value, $this->template);
	}
	public function fill_fork($placeholder, $value){
		($this->has_placeholder($placeholder)) or die("Placeholder $placeholder non trovato.");
		$fork = str_replace("<!-- %$placeholder% -->", $value, $this->template);
		array_push($this->forks, $fork);
	}

	/* Linking */
	public function prepend($ei, $request=false){
		($ei instanceof Engine) or die("Non istanza di Engine passata ad Engine(\"" . $this->tname . "\")::prepend()");
		($request) ? NULL : $ei->append($this, true);
		($this->prev == NULL) or die("Chiamata Engine(\"" . $this->tname . "\")::prepend() su un'istanza già collegata.");
		$this->prev = $ei;
	}
	public function append($ei, $request=false){
		($ei instanceof Engine) or die("Non istanza di Engine passata ad Engine(\"" . $this->tname . "\")::append()");
		($request) ? NULL : $ei->prepend($this, true);
		($this->next == NULL) or die("Chiamata Engine(\"" . $this->tname . "\")::append() su un'istanza già collegata.");
		$this->next = $ei;
	}
	public function detach_prev($request=false){
		($this->prev) or die("Chiamata Engine(\"" . $this->tname . "\")::detach_prev() su un'istanza non collegata.");
		($request) ? NULL : $this->prev->detach_next(true);
		$this->prev = NULL;
	}
	public function detach_next($request=false){
		($this->next) or die("Chiamata Engine(\"" . $this->tname . "\")::detach_next() su un'istanza non collegata.");
		($request) ? NULL : $this->next->detach_prev(true);
		$this->next = NULL;
	}

	/* Nesting */
	/*
	public function inherit($ei){
		($ei instanceof Engine) or die("Non istanza di Engine passata ad Engine(\"" . $this->tname . "\")::inherit()");
		($this->next == NULL) or die("Chiamata Engine(\"" . $this->tname . "\")::inherit() su un'istanza già contenente.");
		($ei->isinherited()) and die("Passata istanza già inherited ad Engine(\"" . $this->tname . "\")::inherit");
		$this->inner = $ei;
		$this->inner->isinherited(true);
	}
	public function isinherited($now=false){
		($now) and $this->inherited = true;
		return $this->inherited;
	}
	*/

	/* Rendering */
	public function render(){
		return ($this->forks) ? implode("\n", $this->forks) : $this->template;
	}
	public function cascading_render(){
		$rendered = $this->render() . "\n";
		if ($this->next != NULL){
			$tmp = $this->next;
			do {
				$rendered .= $tmp->render() . "\n";
				$this->tmp = $this->getnext();
			} while ($this->tmp->getnext() != NULL);
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
}

?>
