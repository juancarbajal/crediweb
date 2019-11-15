<?php
/**
 * Descripción Corta
 * 
 * Descripción Larga
 * 
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer el archivo LICENSE
 * @version    1.0
 * @since      Archivo disponible desde su version 1.0
 */
/**
 * Descripción Corta
 * Descripción Larga
 * @category   
 * @package    
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer archivo LICENSE
 */
class CalendarioFeriado extends Quipu_Calendar { 
  public $feriados;
  public $url;
  private $db;
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function setDb (&$db) { 
    $this->db=$db;
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function esFeriado ($fecha) { 
    return $this->db->fetchOne("SELECT K_RET FROM CR_SP_ES_FERIADO_QRY(?)",array($fecha));
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function setUrl ($url) { 
    $this->url=$url;
  } //end function
  
  function getCell ($day) { 
    if ($day!=''){
      $d=$day.'.'.$this->getMonth().'.'.$this->getYear();
      if ($this->esFeriado($d)){
        $day="<a href='".$this->url."/dia/".$d."'><font color='red'>$day</font></a>";
      }
      else{
        $day="<a href='".$this->url."/dia/".$d."'><font color='blue'>$day</font></a>";
      }
    }
    return parent::getCell($day);
  } //end function
  
} //end class

/**
 * Descripción Corta
 * Descripción Larga
 * @category   
 * @package    
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer archivo LICENSE
 */
class Credito_FeriadoController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    $this->view->subTitle="Calendario de Domingos y Feriados";
    $calendario=new Quipu_Calendar('calendario',array('day'=>'17','month'=>'11','year'=>'2008'));
    $this->view->meses=$calendario->getMonthLabel();
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function calendarioAction () { 
    $p=$this->_request->getParams();
    if ((isset($p['anio']))&&(isset($p['mes']))){
      $d='01';
      $m=$p['mes'];
      $y=$p['anio'];
      if (isset($p['dia'])){
        $this->db->query("EXECUTE PROCEDURE CR_SP_FERIADO_INS(?)",$p['dia']);
        
      }
    }
    else{
      $d='01';
      $m=date('m');
      $y=date('Y');
    }
    $params=array('mes'=>$m,'anio'=>$y);
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $calendario=new CalendarioFeriado('calendario',array('day'=>$d,'month'=>$m,'year'=>$y));
    $calendario->setDb($this->db);
    $calendario->setUrl($this->view->url(array('action'=>'calendario','mes'=>$m, 'anio'=>$y)));
    $this->view->calendario=$calendario->getShow();
  } //end function
} //end class

?>