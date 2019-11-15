<?php
class Quipu_Controller_Action extends Zend_Controller_Action{
  protected $db;
  protected $identity; 
  protected $encoding;
  public function init(){
    global $db;
    $this->db=&$db;
    global $appConfig;
    $this->view->encoding=$appConfig->encoding;
    $this->getResponse()->setHeader('Content-Type', 'application/vnd.mozilla.xul+xml; charset=iso-8859-1');//text/xml
    //Content-Type: application/xhtml+xml; charset=iso-8859-1
    $this->view->addHelperPath('Quipu/View/Helper','Quipu_View_Helper');
    $this->view->title=APP_TITLE;
    $this->identity=Zend_Auth::getInstance()->getIdentity();
    if (!isset($this->identity->USR)){
      if (($this->getRequest()->getModuleName()!='auth') && ($this->getRequest()->getControllerName()!='index')){
        $this->_redirect($this->view->url(array('module'=>'auth','controller'=>'index','action'=>'index')));//'/auth/index'
        }
    }
    else{
      $this->view->user=$this->identity->USR;
      $this->view->role=$this->identity->ROL;
    }
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getIp () { 
    return $_SERVER["REMOTE_ADDR"];
  } //end function
  
  /* public function preDispatch(){
    $auth=Zend_Auth::getInstance();
    if (!$auth->hasIdentity())
      $this->_redirect('/auth');	  
  }*/
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function json ($json) { 
    $this->_helper->json($json);
  } //end function
  /**
   * Limpia UNSET los valores de un array asignados
   * @param array array Arreglo del cual hay q limpiar datos
   * @param array values Datos a Limpiar
   * @uses Clase::methodo()
   * @return type desc
   */
  function clearArray($array,$values){
    foreach($values as $value)
      unset($array[$value]);
    return $array;
  }
}
?>
