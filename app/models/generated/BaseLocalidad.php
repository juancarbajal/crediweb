<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseLocalidad extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('qp_localidad');
    $this->hasColumn('cod', 'string', 12, array('type' => 'string', 'primary' => true, 'length' => '12'));
    $this->hasColumn('nom', 'string', 40, array('type' => 'string', 'notnull' => true, 'length' => '40'));
    $this->hasColumn('padre', 'string', 12, array('type' => 'string', 'length' => '12'));
  }

  public function setUp()
  {
    $this->hasOne('Localidad', array('local' => 'padre',
                                     'foreign' => 'string(12)',
                                     'onUpdate' => 'CASCADE'));

    $this->hasMany('CrCliente', array('local' => 'cod',
                                      'foreign' => 'localidad'));
  }
}