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
class CalendarController extends Quipu_Controller_Action { 
  public $meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $this->view->meses=$this->meses;
  } //end function
  
} //end class

?>