<?php
/**
 * Helper para el trabajo con URL
 * 
 * Helper que devuelve la URL actual de la aplicación
 * 
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer el archivo LICENSE
 * @version    1.0
 * @since      Archivo disponible desde su version 1.0
 */
/**
 * Clase para el manejo de la URL
 * 
 * @category   Quipu
 * @package    Quipu_View_Helper_BaseUrl
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer archivo LICENSE
 */
class Quipu_View_Helper_BaseUrl{ 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function baseUrl () { 
    $fc=Zend_Controller_Front::getInstance();
    return $fc->getBaseUrl();
  } //end function
  
} //end class

?>