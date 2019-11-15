<?
/**
    * Descripción Corta
     * Descripción Larga
      * @category   
      * @package    
      * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
      * @license    Leer archivo LICENSE
      */
class Quipu_Config extends Zend_Config { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  
  public function __construct($filename, $section = null, $options = false){
    parent::__construct($filename,$section, $options);
  } //end function

  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function encrypt($filename,$output) { 
    base64_encode();
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function decrypt($filename,$output) { 
    base64_decode();
  } //end function
  
} //end class

?>