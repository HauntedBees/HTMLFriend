<?php
class Template {
	private $template, $values;
	function __construct($f) {
		$this->template = file_get_contents("html/$f");
		$this->values = [];
	}
	public function ClearKeys() { $this->values = []; }
	public function SetKey($k, $v) { $this->values[$k] = $v; }
	public function SetKeys($arr) { foreach($arr as $k => $v) { $this->values[$k] = $v; } }
	public function GetContent() {
		$html = $this->template;
		foreach($this->values as $k => $v) { $html = str_replace("{@$k}", $v, $html); }
		return $html;
	}
	public function GetLoopedContent($keysArray) {
		$this->ClearKeys();
		$this->SetKeys($keysArray);
		return $this->GetContent();
	}
	public function GetForEachContent($array, $func, $args = []) {
		$html = "";
		foreach($array as $element) {
			$html .= $this->GetLoopedContent($func($element, $args));
		}
		return $html;
	}
	public function GetPDOFetchAssocContent($table, $func, $args = []) {
		$html = "";
		while($row = $table->fetch(PDO::FETCH_ASSOC)) {
			$html .= $this->GetLoopedContent($func($row, $args));
		}
		return $html;
	}
}
?>
