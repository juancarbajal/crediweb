<?php
/**
 * Descripción Corta
 * 
 * Descripción Larga
 * 
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer el archivo LICENSE
 * @version    1.0
 * @since      Archivo disponible desde su version 1.0
 */
/**
 * Descripción Corta
 * Descripción Larga
 * @category   
 * @package    
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer archivo LICENSE
 */
class Empresa extends  Quipu_Db_Table { 
  protected $_name='QP_EMPRESA';
  protected $_primary='COD';
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function datosBasicos () { 
    $rs= $this->_db->fetchAll('SELECT COD,NOM,DIRE,FONO1,FONO2 FROM '.$this->_name);
    return $rs[0];
  } //end function
  
} //end class

?>