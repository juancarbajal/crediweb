<?php
/**
 * Descripci�n Corta
 * 
 * Descripci�n Larga
 * 
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer el archivo LICENSE
 * @version    1.0
 * @since      Archivo disponible desde su version 1.0
 */
/**
 * Descripci�n Corta
 * Descripci�n Larga
 * @category   
 * @package    
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer archivo LICENSE
 */
class Credito_GerenciaController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () {
	if ($this->identity->ROL!='GERENCIA'){
	$this->_redirect('auth');
	} 
	else{
    	$this->view->subTitle="Gerencia";
	}
  } //end function
  
} //end class

?>
