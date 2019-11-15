<?php 
/**
    * Descripción Corta
     * Descripción Larga
      * @category   
      * @package    
      * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
      * @license    Leer archivo LICENSE
      */
class Quipu_SqlUtils{ 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function addWhere ($sql,$cond,$sep='AND') { 
    
    if (strpos(strtoupper($sql),' WHERE')===false){
      $sql.=' WHERE '.$cond;
    }
    else{
      $sql.=' '.$sep.' '.$cond;
    }
    return $sql;
  } //end function

} //end class

?>