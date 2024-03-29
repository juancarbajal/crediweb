<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseBlComentario extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('bl_comentario');
    $this->hasColumn('id', 'integer', null, array('type' => 'integer', 'primary' => true));
    $this->hasColumn('tema_id', 'integer', null, array('type' => 'integer'));
    $this->hasColumn('fec', 'timestamp', null, array('type' => 'timestamp'));
    $this->hasColumn('persona_cod', 'string', 12, array('type' => 'string', 'length' => '12'));
  }

  public function setUp()
  {
    $this->hasOne('BlTema', array('local' => 'tema_id',
                                  'foreign' => 'id',
                                  'onUpdate' => 'CASCADE'));

    $this->hasOne('Persona', array('local' => 'persona_cod',
                                   'foreign' => 'cod',
                                   'onUpdate' => 'CASCADE'));
  }
}