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
class Credito_CobranzaController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function planillaAction () { 
    $this->view->subTitle="Planilla de Cobranzas";
    $this->view->cobradores=$this->getCobradores();
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function planillarepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $this->view->empresa=$this->getEmpresa();
    $r=$this->_request->getParams();
    $this->view->planillas=$this->db->fetchAll("SELECT * FROM CR_SP_PLANILLA_COBRO_QRY(?,?)",array($r['COBRADOR'],$r['FEC']));
    $this->view->fec=$r['FEC'];
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function pagoplanillaAction () { 
    $this->view->subTitle='Pago de Planilla de Cobranza';
    $this->view->cobradores=$this->getCobradores();
    if ($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['op']) {
      case 'get' : 
        $rs=$this->db->fetchAll("SELECT K_CREDITO_COD,K_CLIENTE_COD,K_CLIENTE_NOM,K_PLAZO_NO_PAGADO,K_SUBTOTAL,K_ITF,K_MORA,K_TOTAL FROM CR_SP_PLANILLA_COBRO_QRY(?,?)",
                                array($_REQUEST['COBRADOR_COD'],
                                      $_REQUEST['FEC']));
        $this->json(array('code'=>0,'msg'=>'','data'=>$rs));
        break;
      default: break;
      } //end switch
      
    }
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function moraAction () { 
    $this->view->subTitle="Reporte de Mora";
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function morarepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $this->view->fecha=$r['FEC'];
    $this->view->mora=$this->db->fetchAll("SELECT * FROM CJ_SP_REPORTE_MORA_DET_QRY(?)",array($r['FEC']));
    $rs=$this->db->fetchAll("SELECT COALESCE(SUM(K_TOTAL),0.00) AS S1, COALESCE(SUM(K_SUB_TOTAL),0.00) AS S2,COALESCE(SUM(K_CANCELADO),0.00) AS S3, COALESCE(SUM(K_CAPITAL),0.00) AS S4, COALESCE(SUM(K_INTERES),0.00) AS S5, COALESCE(SUM(K_IGV),0.00) AS S6, COALESCE(SUM(K_MORA),0.00) AS S7, COALESCE(SUM(K_ITF),0.00) AS S8, COALESCE(SUM(K_SEGURO),0.00) AS S9, COALESCE(SUM(K_OTROS),0.00) AS S10  FROM  CJ_SP_REPORTE_MORA_DET_QRY(?)",array($r['FEC']));
    $this->view->moraTotal=$rs[0];
  } //end function
  
  function getCobradores () { 
    $rs=$this->db->fetchAll("SELECT * FROM CR_VW_LISTA_COBRADORES");
    return $rs;
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getEmpresa () { 
    $rs=$this->db->fetchAll('SELECT COD, NOM FROM QP_EMPRESA');
    return $rs[0];
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function asignarAction () { 
    $this->view->subTitle="Asignar Cobrador";
    $this->view->cobradores=$this->getCobradores();
    if ($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['op']){
      case 'get' : 
        $rs=$this->db->fetchAll("SELECT COD,COBRADOR_COD FROM CR_CREDITO WHERE COD=LPAD('{$_REQUEST['COD']}',12,'0')" );
        $this->json(array('code'=>'0','msg'=>'','data'=>$rs[0]));
        break;
      case 'ins' : 
        try{
          if ($_REQUEST['COBRADOR_COD']=='')
            $this->db->query("UPDATE CR_CREDITO SET COBRADOR_COD=NULL WHERE COD=?",array($_REQUEST['COD']));  
          else
            $this->db->query("UPDATE CR_CREDITO SET COBRADOR_COD=? WHERE COD=?",array($_REQUEST['COBRADOR_COD'],$_REQUEST['COD']));  
          $this->json(array('code'=>'0','msg'=>'Registro Actualizado'));
        } catch (Exception $e){
          $this->json(array('code'=>'1','msg'=>$e->getMessage()));
        }
        break;
      }
    }
  } //end function
  
} //end class

?>