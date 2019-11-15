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
class Credito_LegalController extends Quipu_Controller_Action { 
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
  
} //end class

?>