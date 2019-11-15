<?php
/**
    * Descripción Corta
     * Descripción Larga
      * @category   
      * @package    
      * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
      * @license    Leer archivo LICENSE
      */
class Quipu_Controller_Plugin_LogView extends Zend_Controller_Plugin_Abstract { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function Quipu_Controller_Plugin_LogView (&$db,&$auth) { 
    $this->db=$db;
    $this->auth=$auth;
  } //end function
  
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function preDispatch () { 
    /*    $action=$this->getRequest()->getActionName();
    $controller=$this->getRequest()->getControllerName();
    $module=$this->getRequest()->getModuleName();*/
    $params='';
    foreach($this->getRequest()->getParams() as $key=>$val){
      $params.=$key.'='.$val.'; ';
    }
    //$params=implode('/',$this->getRequest()->getParams());
    //$url=$module.'/'.$controller.'/'.$action;
    $url=$_SERVER['REQUEST_URI '];
    $this->db->insert('LG_AUDITOR',
                      array(
                            'FEC'=>date('Y.m.d'),
                            'URL'=>$params,
                            'OPER'=>'V',
                            'USR'=>$this->auth->getInstance()->getIdentity()->USR,
                            'MAQ'=>$_SERVER["REMOTE_ADDR"]
                            ));
  } //end function
} //end class

?>