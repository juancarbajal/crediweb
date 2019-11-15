<?php
class Quipu_Log extends Zend_Log{
	const INSERT=10;
	const UPDATE=11;
	const DELETE=12;
	function __construct(){
		parent::construct();
		$this->addPriority('INSERT', $this.INSERT);
		$this->addPriority('UPDATE', $this.UPDATE);
		$this->addPriority('DELETE', $this.DELETE);
		$formatter = new Zend_Log_Formatter_Simple(' %message%' . PHP_EOL); //aca añadimos la pagina q esta ejecutando la accion
	}
}

?>