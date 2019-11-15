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
class Credito_CronogramaController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $this->view->subTitle='SIMULACI&Oacute;N';
    $r=$this->_request->getParams();
    $plazo=$r['p'];
    $monto=$r['m'];
    $tasa=$r['t']/100;
    $fecha_inicio=$r['f'];
    $otros=$r['o'];
    $impuestos=$this->getImpuestos();      
    $igv=$impuestos->IGV_DEC;
    $itf=$impuestos->ITF_DEC;
    $renta=$impuestos->RENTA_DEC;
    $seguro=$impuestos->SEGURO_DEC;
    $this->view->monto=$monto;
    $this->view->plazo=$plazo;
    $this->view->tasaAnual=$this->getTasaAnual($tasa)*100.00;
    $this->view->itf=$itf*$monto;
    $this->view->liquidez=$monto-$this->view->itf;
    $this->view->linea=$this->getLinea($r['LINEA_COD']);
    if ($r['CLIENTE_COD']!=null)
      $this->view->cliente=$this->getCliente($r['CLIENTE_COD']);
    $this->view->modalidad=$this->getModalidad($r['MODALIDAD_COD']);
    $empresa=new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    $credito=new Credito();
    $crono=$credito->cronograma($monto,$plazo,$fecha_inicio,$tasa,$otros);
    $this->view->cuotas=$crono['cuotas'];
    $this->view->total=$crono['total'];
  } //end function
  /**
   * Cronograma de Desembolso
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function desembolsoAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $this->view->subTitle='CRONOGRAMA DE PAGOS';
    $r=$this->_request->getParams();
    $cod=$r['COD'];
    $this->view->COD=$cod;
    $credito=new Credito();
    $rs=$credito->getDatosBasicosCredito($cod);
    $this->view->analista=$credito->getAnalista($cod)->K_NOM;
    $this->view->desembolso=$rs->DESEMBOLSO_FEC;
    $plazo=$rs->PLAZO;
    $monto=$rs->MONTO;
    $tasa=$rs->TI_MENSUAL;
    $fecha_inicio=$rs->DESEMBOLSO_FEC;
    $otros=$rs->GA;
    $impuestos=$this->getImpuestos();      
    $igv=$impuestos->IGV_DEC;
    $itf=$impuestos->ITF_DEC;
    $renta=$impuestos->RENTA_DEC;
    $seguro=$impuestos->SEGURO_DEC;
    $this->view->monto=$monto;
    $this->view->plazo=$plazo;
    $this->view->tasaAnual=$this->getTasaAnual($tasa)*100;
    $this->view->itf=$itf*$monto;
    $this->view->liquidez=$monto-$this->view->itf;
    $this->view->linea=$this->getLinea($rs->LINEA_COD);
    $this->view->cliente=$this->getCliente($rs->CLIENTE_COD);
    $this->view->tipoDoc=$this->getTipoDoc($rs->CLIENTE_COD);
    $this->view->modalidad=$this->getModalidad($rs->MODALIDAD_COD);
    $empresa=new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    
    $crono=$credito->cronogramaCredito($cod);
    $this->view->cuotas=$crono['cuotas'];
    $this->view->total=$crono['total'];
    $numLet=new NumLet();
    $this->view->numeroLetras=$numLet->convertir($monto,$this->view->linea->MONEDA);
    //$x=$numLet->find('02');
    //print_r($x);
    //$this->view->numeroLetras=
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getTipoDoc ($cod) { 
    return $this->db->fetchOne('SELECT TD.ABREV FROM CR_CLIENTE CL INNER JOIN QP_PERSONA P ON CL.PERSONA_COD=P.COD INNER JOIN QP_TIPO_DOC TD ON P.TIPO_DOC_COD=TD.COD WHERE CL.COD=?',array($cod));
  } //end function
  
  /**
   * 
   * @tasaMensual decimal Tasa de interes mensual en decimales
   * @uses Clase::methodo()
   * @return decimal Tasa Anual en Decimales
   */
  function getTasaAnual ($tasaMensual) { 
    return pow(1+$tasaMensual,12.00)-1;
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getImpuestos () { 
    $rs=$this->db->fetchAll('SELECT * FROM CN_IMPUESTO WHERE FEC_FIN IS NULL;');
    return $rs[0];
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getItf(){
    return $this->db->fetchOne('SELECT ITF FROM CN_IMPUESTO WHERE FEC_FIN IS NULL;');
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getLinea($cod){
    $rs=  $this->db->fetchAll('SELECT L.NOM AS NOM, M.NOM AS MONEDA, M.ABREV FROM CR_LINEA L INNER JOIN CR_MONEDA M ON L.MONEDA_COD=M.COD WHERE L.COD=?',array($cod));
    return $rs[0];
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getModalidad($cod){
    $rs= $this->db->fetchAll('SELECT NOM FROM CR_MODALIDAD WHERE COD=?',array($cod));
    return $rs[0];
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCliente ($cod) { 
    $rs= $this->db->fetchAll('SELECT * FROM CR_SP_BUSCAR_DOC_CLIENTE_QRY(?)',array($cod));
    return $rs[0];
  } //end function
  
  
} //end class

?>