<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseConfig extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('qp_config');
    $this->hasColumn('empresa', 'string', 12, array('type' => 'string', 'length' => '12'));
  }

}