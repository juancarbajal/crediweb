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
class Auth_ConfigController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function personalAction () { 
    $this->view->subTitle="Configuración Personal";
    $id=$this->identity;
    if (!isset($id->USR)) $this->_redirect($this->view->url(array('module'=>'auth','controller'=>'index','action'=>'error')));
    $this->view->USR=$id->USR;
    if ($this->_request->isXmlHttpRequest()){
      try{
        $this->db->query("UPDATE SG_USUARIO SET USR=?, PASS=? WHERE COD=?",
                         array($_REQUEST['USR'],
                               md5($_REQUEST['USR'].$_REQUEST['PASS']),
                               $id->COD
                               ));
        $this->json(array('code'=>0,'msg'=>'Registro modificado'));
      } catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }
    
  } //end function
  
} //end class

?>