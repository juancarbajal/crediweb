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
class Legal_CreditoController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function contratoclienteAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $empresa=new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    $credito=new Credito();
    $this->view->cliente=$credito->getCliente($r['COD']);
    $this->view->clienteLocalidad=explode(' - ',$this->view->cliente->LOCALIDAD);
    $this->view->clienteLocalidadNegocio=explode(' - ',$this->view->cliente->LOCALIDAD_NEGOCIO);
    $this->view->aval=$credito->getAval($r['COD']);
    $this->view->avalLocalidad=explode(' - ',$this->view->aval->LOCALIDAD);
    $this->view->credito=$credito->getDatosBasicosCredito($r['COD']);
    $this->view->moneda=$credito->getMoneda($r['COD']);

    $numlet=new NumLet();
    $this->view->fecha=$numlet->convertirFecha($this->view->credito->FEC);
    $this->view->montoTexto=$numlet->convertir($this->view->credito->MONTO, $this->view->moneda->NOM);
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function solicitudclienteAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $empresa=new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    $credito=new Credito();
    $this->view->cliente=$credito->getCliente($r['COD']);
    $this->view->clienteLocalidad=explode(' - ',$this->view->cliente->LOCALIDAD);
    $this->view->clienteLocalidadNegocio=explode(' - ',$this->view->cliente->LOCALIDAD_NEGOCIO);
    $this->view->aval=$credito->getAval($r['COD']);
    $this->view->avalLocalidad=explode(' - ',$this->view->aval->LOCALIDAD);
    $this->view->credito=$credito->getDatosBasicosCredito($r['COD']);
    $this->view->moneda=$credito->getMoneda($r['COD']);
    $this->view->solicitud=$credito->getSolicitud($r['COD']);
    $this->view->solicitudDetalle=$credito->getSolicitudDetalle($r['COD']);
    $this->view->actividad=$credito->getActividad($r['COD']);
    $this->view->ciiu=$credito->getCiiu($r['COD']);
    $this->view->analista=$credito->getAnalista($r['COD']);
    $this->view->cobrador=$credito->getCobrador($r['COD']);
    $numlet=new NumLet();
    $this->view->montoTexto=$numlet->convertir($this->view->credito->MONTO, $this->view->moneda->NOM);
    $this->view->inversion=$credito->getInversion($r['COD']);
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function pagareclienteAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $empresa=new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    $credito=new Credito();
    $this->view->cliente=$credito->getCliente($r['COD']);
    $this->view->clienteLocalidad=explode(' - ',$this->view->cliente->LOCALIDAD);
    $this->view->clienteLocalidadNegocio=explode(' - ',$this->view->cliente->LOCALIDAD_NEGOCIO);
    $this->view->aval=$credito->getAval($r['COD']);
    $this->view->avalLocalidad=explode(' - ',$this->view->aval->LOCALIDAD);
    $this->view->credito=$credito->getDatosBasicosCredito($r['COD']);
    $this->view->moneda=$credito->getMoneda($r['COD']);
    $numlet=new NumLet();
    $this->view->montoTexto=$numlet->convertir($this->view->credito->MONTO, $this->view->moneda->NOM);
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function cartanotarialAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $this->view->cod=$r['COD'];
    $empresa=new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    $credito=new Credito();
    $this->view->cliente=$credito->getCliente($r['COD']);
    $this->view->clienteLocalidad=$this->view->cliente->LOCALIDAD;
    $this->view->aval=$credito->getAval($r['COD']);
    $this->view->avalLocalidad=$this->view->aval->LOCALIDAD;
    $this->view->credito=$credito->getDatosBasicosCredito($r['COD']);
    $this->view->moneda=$credito->getMoneda($r['COD']);
    $numlet=new NumLet();
    $this->view->fecha=$numlet->convertirFecha($this->view->credito->FEC,1);
    $this->view->fechaHoy=$numlet->convertirFecha(date('Y-m-d'),1);
    $this->view->montoTexto=$numlet->convertir($this->view->credito->MONTO, $this->view->moneda->NOM);
    $this->view->ultimoPago=$credito->getUltimoPago($r['COD']);
    $this->view->ultimoPago->FEC=$numlet->convertirFecha($this->view->ultimoPago->FEC,1);
    $this->view->intervaloSinPago=$credito->getIntervaloSinPago($r['COD']);
    $this->view->intervaloSinPago['ini']->FEC=$numlet->convertirFecha($this->view->intervaloSinPago['ini']->FEC,1);
    $this->view->intervaloSinPago['fin']->FEC=$numlet->convertirFecha($this->view->intervaloSinPago['fin']->FEC,1);

  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function transaccionextrajudicialrepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $this->view->cod=$r['COD'];
    $empresa=new Empresa();
    $this->view->empresa=$empresa->datosBasicos();    
    $credito=new Credito();
    $this->view->cliente=$credito->getCliente($r['COD']); 
    $this->view->clienteLocalidad=explode(' - ',$this->view->cliente->LOCALIDAD);
    $this->view->clienteLocalidadNegocio=explode(' - ',$this->view->cliente->LOCALIDAD_NEGOCIO);
    $this->view->aval=$credito->getAval($r['COD']);
    
    $this->view->avalEstadoCivil=$this->db->fetchOne("SELECT NOM FROM QP_ESTADO_CIVIL WHERE COD='{$r['COD']}'");
    $this->view->avalLocalidad=explode(' - ',$this->view->aval->LOCALIDAD);
    
    $this->view->credito=$credito->getDatosBasicosCredito($r['COD']);
    $this->view->moneda=$credito->getMoneda($r['COD']);
    
    $numlet=new NumLet();
    $this->view->fecha=$numlet->convertirFecha($this->view->credito->FEC);
    $this->view->montoTexto=$numlet->convertir($this->view->credito->MONTO, $this->view->moneda->NOM);
    $mora=$this->db->fetchAll("SELECT MONTO,PLAZO,FEC_INI,FEC_EMISION FROM MO_MORA WHERE CREDITO_COD=?",array($r['COD']));
    $this->view->mora=$mora[0];
    $this->view->mora->MONTO_TEXTO=$numlet->convertir($this->view->mora->MONTO,$this->view->moneda->NOM);
    $this->view->mora->PLAZO_TEXTO=$numlet->convertirEntero($this->view->mora->PLAZO);
    $this->view->mora->FECHA_TEXTO=$numlet->convertirFecha($this->view->mora->FEC_INI,1);
    $this->view->mora->FECHA_EMISION_TEXTO=$numlet->convertirFecha($this->view->mora->FEC_EMISION,1);
    $this->view->mora->FECHA_EMISION_TEXTO2=$numlet->convertirFecha($this->view->mora->FEC_EMISION,0);
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function transaccionextrajudicialAction () { 
    $this->view->subTitle='Transacción Extrajudicial';
    if ($this->_request->isXmlHttpRequest()) {
      
      switch ($_REQUEST['op']) {
      case 'getcredito' : 
        $rs=$this->db->fetchAll("SELECT * FROM MO_SP_CAPTURAR_CLIENTE_QRY(?)",array($_REQUEST['COD']));
        $this->json(array('code'=>0,'msg'=>'','data'=>$rs[0]));
        break;
      case 'ins' : 
        try{
          $rs=$this->db->query("EXECUTE PROCEDURE MO_SP_MORA_INS('{$_REQUEST['COD']}',{$_REQUEST['MONTO']},{$_REQUEST['PLAZO']},'{$_REQUEST['FEC_INI']}','{$_REQUEST['FEC_EMISION']}')");          
          /* $rs=$this->db->query("EXECUTE PROCEDURE MO_SP_MORA_INS(?,?,?,?)", */
          /*                      $_REQUEST['COD'], */
          /*                      $_REQUEST['MONTO'], */
          /*                      $_REQUEST['PLAZO'], */
          /*                      $_REQUEST['FEC_INI'] */
          /*                      ); */
          $this->json(array('code'=>0,'msg'=>'Registro Guardado'));
        } catch (Exception $e){
          $this->json(array('code'=>1,'msg'=>$e->getMessage()));
        }
        break;
      default: break;
      } //end switch
    } 
  } //end function
  
} //end class

?>