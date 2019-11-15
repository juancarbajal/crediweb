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
class Quipu_LogListener extends Doctrine_Record_Listener { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function preInsert (Doctrine_Event $event) {     
    $event->getInvoker()->created=date('Y-m-d H:i:s');    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function preUpdate (Doctrine_Event $event) { 
    $event->getInvoker()->updated=date('Y-m-d H:i:s');    
  } //end function
  
  
} //end class

/**
 * Descripción Corta
 * Descripción Larga
 * @category   
 * @package    
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer archivo LICENSE
 */
class Quipu_LogTemplate extends Doctrine_Template { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function  setTableDefinition() { 
    $this->hasColumn('created','timestamp');
    $this->hasColumn('updated','timestamp');
    $this->hasColumn('created_by','string',32);
    $this->hasColumn('updated_by','string',32);
    $this->setListener(new Quipu_LogListener);
  } //end function
  
} //end class

?>