<?php
class Quipu_Controller_Plugin_Authorization extends Zend_Controller_Plugin_Abstract {
  private $_auth;
  private $_acl;
  function __construct(Zend_Auth $auth, Zend_Acl $acl) {
    $this->_auth=$auth;
    $this->_acl=$acl;
  } //end function
  /*
  public function preDispatch ( Zend_Controller_Request_Abstract $request )
  {
    // revisa que exista una identidad
    // obtengo la identidad y el "role" del usuario, sino tiene le pone 'guest'
    $role = $this->_auth->hasIdentity() ? $this->_auth->getInstance()->getIdentity()->role : 'guest';
    $resource=null;
    // toma el nombre del recurso actual
    if( $this->_acl->has( $this->getRequest()->getActionName() ) )
      $resource = $this->getRequest()->getActionName();
    elseif( $this->_acl->has( $this->getRequest()->getControllerName() ) )
      $resource = $this->getRequest()->getControllerName();
    elseif( $this->_acl->has( $this->getRequest()->getModuleName() ) )
      $resource = $this->getRequest()->getModuleName();
 
    // Si, la persona no pasa la prueba de autorización y su "role" es 'guest'
    // entonces no ha echo "login" y lo dirigo al controlador "login" del modulo "auth"
    if ( !$this->_acl->isAllowed($role, $resource) && $role == 'guest' )
      {
        $request->setModuleName('auth');
        $request->setControllerName('index');
      }
    // Ahora si la persona tiene un "role" distinto de 'guest' y aun asi no pasa
    // la prueba de identificacion lo mando a una pagina de error.
    elseif (!$this->_acl->isAllowed($role, $resource) )
      {
        $request->setModuleName('auth');
        $request->setControllerName('error');
      }
 
  } //end function
  */
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function preDispatch (Zend_Controller_Request_Abstract $request) { 
    if ((!$this->_auth->hasIdentity()) && ($this->getRequest()->getModuleName()!='auth')){
        $request->setModuleName('auth');
        $request->setControllerName('index');      
        $request->setActionName('error');
    }
    //$identity=$this->_auth->getInstance()->getIdentity();
  } //end function
  
} //end class

?>