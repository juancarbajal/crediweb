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
class Credito_ConfigController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCiiu () { 
    return $this->db->fetchAll("SELECT COD,NOM FROM CR_CIIU ORDER BY COD");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function ciiuAction () { 
    $this->view->subTitle='CIIU';
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM FROM CR_CIIU WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getCiiu();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("CR_CIIU",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getCiiu()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('CR_CIIU',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getCiiu()));
          break;
        case 'del':
          $rs=$this->db->delete('CR_CIIU',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getCiiu()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getDestino () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM CR_DESTINO ORDER BY NOM");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function destinoAction () { 
        $this->view->subTitle='Destino de Crédito';
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM FROM CR_DESTINO WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getDestino();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("CR_DESTINO",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getDestino()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('CR_DESTINO',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getDestino()));
          break;
        case 'del':
          $rs=$this->db->delete('CR_DESTINO',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getDestino()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getLinea () { 
    return $this->db->fetchAll('SELECT COD, NOM FROM CR_LINEA ORDER BY NOM');
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function lineaAction () { 
    $this->view->subTitle='Línea de Crédito';
    $this->view->monedas=$this->getMoneda();
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM,TI_MENSUAL, TI_MORA, TI_COMPENSA,GA,MONEDA_COD,MONTO_MIN, MONTO_MAX, CUOTA_MIN, CUOTA_MAX FROM CR_LINEA WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getLinea();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("CR_LINEA",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getLinea()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('CR_LINEA',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getLinea()));
          break;
        case 'del':
          $rs=$this->db->delete('CR_LINEA',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getLinea()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getModalidad () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM CR_MODALIDAD ORDER BY COD");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function modalidadAction () { 
    $this->view->subTitle='Modalidad de Crédito';
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM FROM CR_MODALIDAD WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getModalidad();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("CR_MODALIDAD",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getModalidad()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('CR_MODALIDAD',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getModalidad()));
          break;
        case 'del':
          $rs=$this->db->delete('CR_MODALIDAD',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getModalidad()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }    
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getMoneda () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM CR_MONEDA ORDER BY COD");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function monedaAction () { 
    $this->view->subTitle='Moneda';
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM,ABREV FROM CR_MONEDA WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getMoneda();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("CR_MONEDA",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getMoneda()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('CR_MONEDA',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getMoneda()));
          break;
        case 'del':
          $rs=$this->db->delete('CR_MONEDA',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getMoneda()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getTipoCredito () { 
    return $this->db->fetchAll("SELECT COD,NOM FROM CR_TIPO_CREDITO ORDER BY COD");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function tipocreditoAction () { 
    $this->view->subTitle='Tipo de Crédito';
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM FROM CR_TIPO_CREDITO WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getTipoCredito();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("CR_TIPO_CREDITO",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getTipoCredito()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('CR_TIPO_CREDITO',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getTipoCredito()));
          break;
        case 'del':
          $rs=$this->db->delete('CR_TIPO_CREDITO',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getTipoCredito()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }     
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getTipoPrestamo () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM CR_TIPO_PRESTAMO ORDER BY COD");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function tipoprestamoAction () { 
    $this->view->subTitle='Tipo de Prestamo';
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM FROM CR_TIPO_PRESTAMO WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getTipoPrestamo();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("CR_TIPO_PRESTAMO",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getTipoPrestamo()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('CR_TIPO_PRESTAMO',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getTipoPrestamo()));
          break;
        case 'del':
          $rs=$this->db->delete('CR_TIPO_PRESTAMO',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getTipoPrestamo()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }    
    
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getImpuesto () { 
    return $this->db->fetchAll("SELECT ID, FEC_INI, FEC_FIN, IGV, ITF, SEGURO, RENTA FROM CN_IMPUESTO");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function impuestoAction () { 
        $this->view->subTitle='Impuesto';
        $this->view->fec_ini=date('d.m.Y');
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT ID, FEC_INI, FEC_FIN, IGV, ITF, RENTA FROM CN_IMPUESTO WHERE ID=?",array($_REQUEST['idx']));
          else
            $rs=$this->getImpuesto();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op','ID'));
          $this->db->insert("CN_IMPUESTO",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getImpuesto()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('CN_IMPUESTO',$r,"ID= $idx");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getImpuesto()));
          break;
        case 'del':
          $rs=$this->db->delete('CN_IMPUESTO',"ID='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getImpuesto()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }     

  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getActividad () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM CR_ACTIVIDAD ORDER BY COD");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getRubros () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM CR_RUBRO_ACTIVIDAD");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function actividadAction () { 
    $this->view->subTitle='Actividad de Crédito';
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM,RUBRO_ACTIVIDAD_COD FROM CR_ACTIVIDAD WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getActividad();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("CR_ACTIVIDAD",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getActividad()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('CR_ACTIVIDAD',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getActividad()));
          break;
        case 'del':
          $rs=$this->db->delete('CR_ACTIVIDAD',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getActividad()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }    
    $this->view->rubros=$this->getRubros();
    
  } //end function  
} //end class

?>