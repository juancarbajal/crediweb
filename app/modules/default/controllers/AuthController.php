<?php

class AuthController extends Quipu_Controller_Action{
  function loginAction(){
    $this->view->subTitle='Ingreso de Usuario';
    $form=$this->getLoginForm();
    global $userConfig;    
    if ($this->_request->isPost()){
      if ($form->isValid($_REQUEST)){
        $auth= Zend_Auth::getInstance();        
        $authAdapter=new Quipu_Auth_Adapter($userConfig->table, $userConfig->user, $userConfig->pass);        
        $authAdapter->setIdentity($form->getValue('usuario'));
        $authAdapter->setCredential($form->getValue('password'));
        
        if ($auth->authenticate($authAdapter)->isValid()){
          $auth->getStorage()->write($authAdapter->getResultRowObject(null, $userConfig->pass));
          $this->_redirect('/educacion');
          $this->view->message='Ingreso Satisfactorio';
        }
        else
          $this->view->message='Error en el nombre de usuario y contrase&ntilde;a';
        
	  } // end if isValid
    } // end if isPost
    
    $this->view->loginForm=$form;
  } // end function
  function logoutAction(){
    Zend_Auth::getInstance()->clearIdentity();
    Zend_Session::destroy();
    $this->_redirect('/');		
  }
  function preDispatch(){
  }
  function getLoginForm(){
    $config= new Zend_Config_Ini('app/config/forms/login.ini','form');
    $form=new Quipu_Form($config->registrar);
	return $form;
  }
}
?>