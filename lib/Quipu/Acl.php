<?php
class Quipu_Acl extends Zend_Acl{
  function __construct() {
    //Roles
    $this->addRole(new Zend_Acl_Role('admin'));
    $this->addRole(new Zend_Acl_Role('teacher'));
    $this->addRole(new Zend_Acl_Role('student'));
    $this->addRole(new Zend_Acl_Role('guest'));

    //Recursos
    $this->add(new Zend_Acl_Resource('default'));
    
    $this->add(new Zend_Acl_Resource('admin'));
    $this->add(new Zend_Acl_Resource('listar'));
    $this->add(new Zend_Acl_Resource('registrar'));
    $this->add(new Zend_Acl_Resource('modificar'));
    $this->add(new Zend_Acl_Resource('eliminar'));
    
    $this->add(new Zend_Acl_Resource('auth'));
    $this->add(new Zend_Acl_Resource('profesor'));
    $this->add(new Zend_Acl_Resource('alumno'));
    $this->add(new Zend_Acl_Resource('error'));
    
    //Permisos
    $this->allow('guest',array('default','auth','error'));
    $this->deny('guest', array('admin','profesor','alumno'));
    
    $this->allow('teacher',array('profesor','auth','error'));
    $this->deny('teacher',array('listar','registrar','modificar'));
    
    $this->allow('student',array('alumno','auth','error'));
    $this->deny('student',array('admin','profesor'));
    
    $this->allow('admin');
  } //end function  
  
} //end class

?>