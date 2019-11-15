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
class NumLet extends Quipu_Db_Table { 
  protected $_meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre', 'Diciembre');
  protected $_name="CN_NUMLET";
  protected $_primary="COD";
  private $centenas=array(0=>'',1=>'CIEN',2=>'DOSCIENTOS',3=>'TRESCIENTOS',4=>'CUATROCIENTOS',5=>'QUINIENTOS',6=>'SEISCIENTOS',7=>'SETECIENTOS', 8=>'OCHOCIENTOS',9=>'NOVECIENTOS');
  private $miles=array('MIL','MILLON','BILLON');
  /**
   * Convierte un Numero en Letras
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function convertir ($num,$moneda) { 
	$i=0;
	$this->divide(number_format($num,2,'.',''),$entero,$decimal);
	$posBase=strlen($entero);
	$i=-1;
	while ($posBase>0){				
		($posBase<3)?$resta=$posBase:$resta=3;		
        
		$mensaje=$this->analizaCientos(substr($entero,$posBase-$resta,3)).' '.$this->miles[$i].' '.$mensaje;		
        //echo $mensaje.'-';
		$posBase=$posBase-$resta;
		$entero=substr($entero,0,$posBase);
		$i++;
	}
	return $mensaje."CON $decimal/100 $moneda";	
  } //end function
  public function convertirEntero($num){
    $i=0;
	$this->divide(number_format($num,2,'.',''),$entero,$decimal);
	$posBase=strlen($entero);
	$i=-1;
	while ($posBase>0){				
		($posBase<3)?$resta=$posBase:$resta=3;		
        
		$mensaje=$this->analizaCientos(substr($entero,$posBase-$resta,3)).' '.$this->miles[$i].' '.$mensaje;		
		$posBase=$posBase-$resta;
		$entero=substr($entero,0,$posBase);
		$i++;
	}
	return $mensaje;	
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function convertirFecha ($fecha,$op=0) { 
    /*    $fecha=explode('.',$fecha);
    $mes=strtolower($this->_meses[((int)$fecha[1])-1]);
    return strtolower($this->convertirEntero($fecha[0])).' días de '.$mes.' del año '.strtolower($this->convertirEntero($fecha[2]));*/
    $fecha=explode('-',$fecha);
    $mes=strtolower($this->_meses[((int)$fecha[1])-1]);
    if ($op==0){
    return strtolower($this->convertirEntero($fecha[2])).' días del mes de '.$mes.' del  '.strtolower($this->convertirEntero($fecha[0]));
    }
    else{
    return strtolower($fecha[2].' de '.$mes.' del '.$fecha[0]);
    }
  } //end function
  
  /**
   * Divide un numero en parte entera y decimal
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function divide($n, &$number,&$decimal){
	$val=explode('.',$n);
	$number=$val[0];
	$decimal=$val[1];	
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  public function extraerBase ($number) { 
	if (strlen($number)==1) $number='0'.$number;	
    //$rs=$this->find($number);
    return $this->_db->fetchOne("SELECT LETRAS FROM {$this->_name} WHERE COD=?",array($number));
    //return $rs->LETRAS;
  } //end function
  /**
   * Analiza grupos de 3 es decir cientos
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function analizaCientos ($number) { 
    if ((int)$number==0) return '';
	$number=str_pad($number,3,'0',STR_PAD_LEFT); //completamos a 100
	(substr($number,0,1)=='1')?$centenas='CIENTO':$centenas=$this->centenas[substr($number,0,1)];
    //echo substr($number,1,2);
	return $centenas.' '.$this->extraerBase(substr($number,1,2));	
  } //end function
  
} //end class

?>