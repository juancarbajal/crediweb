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
class Quipu_Calendar{ 
  private $matrixCalendar;
  private $currentDate;
  private $dayLabel;
  private $monthLabel;
  private $options;
  public $format;
  /**
   * @param string $name Nombre del Calendario
   * @param array $currentDate Fecha del Calendario ('day'=>'17','month'=>'11','year'=>'2007')
   * @uses Clase::methodo()
   * @return type desc
   */
  public function Quipu_Calendar($name='calendar',$currentDate,$format='d.m.Y',$dayLabel=null,$monthLabel=null,$options=null){
	($dayLabel==null)?$dayLabel=array('Dom','Lun','Mar','Mie','Jue','Vie','Sab'):$dayLabel;
	($monthLabel==null)?$monthLabel=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'):$monthLabel;
    //	parent::__construct($name);
    $this->name=$name;
    $this->format=$format;
	$this->matrixCalendar=array();
	$this->currentDate=$currentDate;
	$this->dayLabel=$dayLabel;
	$this->monthLabel=$monthLabel;
	$this->options=$options;
	$this->clearMatrixArray();
	$this->generateMatrixArray();
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getUnixDate () { 
    return mktime(0, 0, 0, $this->currentDate['month'], $this->currentDate['day'], $this->currentDate['year']);
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function getDate () { 
    return date($this->format, $this->getUnixDate());
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function getMonth(){ 
    return date('m',$this->getUnixDate());
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function getYear(){
    return date('Y',$this->getUnixDate());
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function setMonthLabel($monthLabel){
	$this->monthLabel=$monthLabel;
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function getMonthLabel(){
	return ($this->monthLabel);
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function setDayLabel($dayLabel){
	$this->dayLabel=$dayLabel;
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function getDayLabel(){
	return $this->dayLabel;
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function clearMatrixArray(){
	$this->matrixCalendar=null;
	$this->matrixCalendar=array();
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function generateMatrixArray(){
	$currentDate=explode('/',$this->currentDate);	
	$i=0;//Primera Fila
	$j=0;	
	$days=date('t',mktime(0,0,0,$this->getMonth(),1,$this->getYear()));//Sacamos los dias del Mes
	for ($day=1;$day<=$days;$day++){//Sacamos todos los dias del Mes
      $date=mktime(0,0,0,$this->getMonth(),$day,$this->getYear());//Sacamos los datos del Dia
      $data=getdate($date);//Sacamos datos		
      if (checkdate($data['mon'], $data['mday'],$data['year'])==true){
        $j=$data['wday'];		
        $this->matrixCalendar[$i][$j]=$day;
      }
      if ($j==6) $i++;//Pasamos a la siguiente Semana
	}
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function getName () { 
    return $this->name;
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function getCell ($data) { 
    return '<td align=center >'.$data.'</td>';
  } //end function

  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function getShow(){	
	$currentDate=explode('.',$this->currentDate);			
	foreach($this->dayLabel as $day) $smonth.="<th>$day</th>";	//escribimos los dias
	$caption=$this->monthLabel[$this->getMonth()-1].' del '.$this->getYear();//cabezera
	$s='<table name="'.$this->getName().'" id="'.$this->getName().'" ><caption>'.$caption.'</caption><tr>'.$smonth.'</tr>';
	if (!$this->options['font-size']) {$this->options['font-size']='10px';}
	for($i=0;$i<count($this->matrixCalendar);$i++){		
      $s.='<tr>';
      for($j=0;$j<7;$j++)
        $s.=$this->getCell($this->matrixCalendar[$i][$j]);
      $s.='</tr>';
	}
	$s.='</table>';
	return $s;
  }
} //end class

?>