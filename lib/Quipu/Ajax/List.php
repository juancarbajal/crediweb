<?
class Quipu_Ajax_List{
public $identifier;
public $items;
public function __construct($list,$keyValue){
	$this->items=$list;
	$this->identifier=$keyValue;
}
}
?>
