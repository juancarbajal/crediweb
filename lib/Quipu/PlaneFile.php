<?
/**
  * Convierte un array en un archivo plano
   * Descripción Larga
    * @category   
    * @package    
    * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
    * @license    Leer archivo LICENSE
    */
class Quipu_PlaneFile { 
  /**
   * @var string
   */
  protected $filename='';
  /**
   * @var array
   */
  protected $format=array();
  /**
   * Indica si el valor nulo se pondra una cadena vacia, cuando es falso, en caso d enumeros se pone 0 y de letras se pone cadena vacia
   * @var boolean
   */
  protected $null_vacio=boolean;
  /**
   * @var handle
   */
  private $file;
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function __construct($filename,$format,$null_vacio=true) { 
    $this->filename=$filename;
    $this->format=$format;
    $this->null_vacio=$null_vacio;
    $this->file=fopen($this->filename,'w');
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function __destruct() { 
    fclose($this->file);
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function dataToStr ($data) { 
    $s='';
    foreach ($this->format as $idx=>$row){
      switch ($row[0]) { //tipo del campo
      case 's' : //cadena
        $data[$idx]=(strlen($data[$idx])>$row[1])?substr($data[$idx],0,$row[1]):$data[$idx];
        $s.=str_pad($data[$idx],$row[1],' ',STR_PAD_RIGHT);
        break;
      case 'n' : //numero
          $data[$idx]=($data[$idx]=='')?0:(float)$data[$idx];
          $s.=str_pad(number_format($data[$idx],2,'',''),$row[1],' ',STR_PAD_LEFT);
      break;
      case 'i': //entero
      	  $data[$idx]=($data[$idx]=='')?0:(int)$data[$idx];
          $s.=str_pad($data[$idx],$row[1],' ',STR_PAD_LEFT);
      break;
        
      default: break;
      } //end switch
      
    }
    return $s;
  } //end function
   
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function writeln($data) { 
    fwrite($this->file,$this->dataToStr($data)."\n");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function write () { 
    fwrite($this->file,$this->dataToStr($data));
  } //end function
  
  
} //end class

?>