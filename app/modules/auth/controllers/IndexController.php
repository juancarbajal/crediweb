<?php
/**
 * Descripción Corta
 * Descripción Larga
 * @category   
 * @package    
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer archivo LICENSE
 */
class Auth_IndexController extends Quipu_Controller_Action{  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction(){
    $this->view->subTitle='Ingreso de Usuario';
    Zend_Auth::getInstance()->clearIdentity();
    unset($this->identity);
    $this->view->identity=$this->identity;
    global $userConfig;    
    if ($this->_request->isXmlHttpRequest()){
      $this->json($this->validarUsuario($_REQUEST['u'],$_REQUEST['p']));
    }
  } // end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function validarUsuario ($usuario,$clave) { 
    $auth= Zend_Auth::getInstance();        
    global $userConfig;
    $authAdapter=new Zend_Auth_Adapter_DbTable($this->db,$userConfig->table, $userConfig->user, $userConfig->pass);        
    $authAdapter->setIdentity($usuario);
    $authAdapter->setCredential(md5($usuario.$clave));    
    if ($auth->authenticate($authAdapter)->isValid()){
      $auth->getStorage()->write($authAdapter->getResultRowObject(null, $userConfig->pass));
      //Zend_Auth::getInstance()->getIdentity()
      
      $role=strtolower($auth->getIdentity()->ROL);
      //return array('code'=>0, 'msg'=>'Bienvenido', 'url'=>$this->view->url(array('module'=>'credito', 'controller'=>$role, 'action'=>'index')));
      return array('code'=>0, 'msg'=>'Bienvenido', 'url'=>$this->view->url(array('module'=>'default', 'controller'=>'index', 'action'=>'menu')));
    }
    else
      return array('code'=>1, 'msg'=>'Error en Usuario y Clave');
  } //end function
  
  /**
   * Accion terminar sesion
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function logoutAction(){
    Zend_Auth::getInstance()->clearIdentity();
    unset($this->identity);
    $this->_redirect('/');		
  }
  /**
   * Formulario para cambiar la clave del Usuario
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function cambiarclaveAction () { 
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function errorAction () { 
    $this->view->subTitle="Error de Sesión";
  } //end function
  
  function preDispatch(){
  }
  /*  function getLoginForm(){
    $config= new Zend_Config_Ini('app/config/forms/login.ini','form');
    $form=new Quipu_Form($config->registrar);
	return $form;
    }*/
}
?>