<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseUsuario extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('qp_usuario');
    $this->hasColumn('cod', 'string', 12, array('type' => 'string', 'primary' => true, 'length' => '12'));
    $this->hasColumn('username', 'string', 32, array('type' => 'string', 'unique' => true, 'length' => '32'));
    $this->hasColumn('password', 'string', 32, array('type' => 'string', 'notnull' => true, 'length' => '32'));
  }

  public function setUp()
  {
    $this->hasMany('UsuarioRol', array('local' => 'cod',
                                       'foreign' => 'usuario'));
  }
}