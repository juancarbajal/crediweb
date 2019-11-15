<?php
Zend_Loader::LoadClass('Zend_Db_Table');
class Quipu_Db_Table extends Zend_Db_Table{
  /*	function clearGarbage($Array){
		$fields=array('PHPSESSID','module','controller','action');
		foreach($fields as $field)
			unset($Array[$field]);
		return $Array;
	}
	public function insert($data){
		parent::insert($this->clearGarbage($data));
	}
	public function update($data,$where){
		parent::update($this->clearGarbage($data),$where);
	}*/
}
?>