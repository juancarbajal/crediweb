<?php
  /**
    * Descripción Corta
     * Descripción Larga
      * @category   
      * @package    
      * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
      * @license    Leer archivo LICENSE
      */
class Credito_ReporteController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function igvitfAction () { 
    $this->view->subTitle="Reporte General de Impuestos"; //IGV y ITF
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function igvitfrepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $this->view->subTitle="REPORTE GENERAL DE ".$r['IMPUESTO'];
    $this->view->desde=$r['DESDE'];
    $this->view->hasta=$r['HASTA'];
    $this->view->impuesto=$r['IMPUESTO'];
    switch($r['IMPUESTO']){
    case 'ITF':
      $this->view->lista=$this->db->fetchAll("SELECT * FROM CR_SP_REPORTE_ITF_QRY(?,?)",array($r['DESDE'],$r['HASTA']));
      $this->view->total=$this->db->fetchOne("SELECT SUM(K_ITF_TOTAL) FROM CR_SP_REPORTE_ITF_QRY(?,?)",array($r['DESDE'],$r['HASTA']));
      break;
    case 'IGV':
      $rs=$this->db->fetchAll("SELECT * FROM CR_SP_REPORTE_IGV_QRY(?,?)",array($r['DESDE'],$r['HASTA']));
      $this->view->total=$rs[0];
      break;
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function capitalAction () { 
    $this->view->subTitle="Reporte de Capital"; //IGV y ITF
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function capitalrepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $this->view->subTitle="REPORTE DE CAPITAL"; //IGV y ITF
    $this->view->desde=$r['DESDE'];
    $this->view->hasta=$r['HASTA'];
    $rs=$this->db->fetchAll("SELECT * FROM CR_SP_REPORTE_CAPITAL_QRY(?,?);",array($r['DESDE'],$r['HASTA']));
    $this->view->capital=$rs[0];
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function carteraactivaAction () { 
    $this->view->subTitle="CARTERA ACTIVA";
    $this->view->lineas=$this->getLineas();
    $this->view->actividades=$this->getActividades();

  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function carteraactivarepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $r['DESDE']=$this->db->fetchOne("SELECT FIRST 1 FEC_INI FROM QP_EMPRESA");
    $this->view->desde=$r['DESDE'];
    $this->view->hasta=$r['HASTA'];
    $this->view->subTitle="CARTERA ACTIVA";
    
    $sql="SELECT * FROM CR_SP_CARTERA_ACTIVA_QRY('{$r['DESDE']}','{$r['HASTA']}')";
    $u=new Quipu_SqlUtils();
    if ($r['LINEA']!='0') $sql=$u->addWhere($sql," K_LINEA_COD='{$r['LINEA']}' ");
    if ($r['SEXO_OPC']=='1'){  
      $sql=$u->addWhere($sql," K_SEXO='{$r['SEXO']}' ");
    }
    if ($r['ACTIVIDAD_OPC']=='1'){  
      $sql=$u->addWhere($sql," K_ACTIVIDAD='{$r['ACTIVIDAD']}' ");
    }
    if ($r['MONTO_OPC']=='1'){  
      $sql=$u->addWhere($sql," K_MONTO='{$r['MONTO']}' ");
    }
    $this->view->cartera=$this->db->fetchAll($sql);
    //echo $sql;
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getActividades () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM CR_ACTIVIDAD ORDER BY COD ");
  } //end function

  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getLineas () { 
    return $this->db->fetchAll("SELECT COD,NOM FROM CR_LINEA");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function reciboAction () { 
    $this->view->subTitle='Recibos Emitidos';
    if ($this->_request->isXMLHTTPRequest()){
      $rs=$this->db->fetchAll("SELECT * FROM CJ_SP_GET_RECIBO_QRY(?,?)",
                              array($_REQUEST['TIPO'],$_REQUEST['COD']));
      if (count($rs)>0){
        switch ($rs[0]->K_TIPO_RECIBO) {
        case 1 : 
          $url=$this->view->url(array('module'=>'credito','controller'=>'caja','action'=>'pagocuotarep')).'/COD/'.str_pad($_REQUEST['COD'],12,'0',STR_PAD_LEFT);
          break;
        case 2 :
          $url=$this->view->url(array('module'=>'credito','controller'=>'caja','action'=>'reciborep')).'/COD/'.str_pad($_REQUEST['COD'],12,'0',STR_PAD_LEFT);
 
            break;
        case 3 : 
          $url=$this->view->url(array('module'=>'credito','controller'=>'caja','action'=>'docprovrep')).'/ID/'.str_pad($_REQUEST['COD'],12,'0',STR_PAD_LEFT);

          break;
          
        default: break;
        } //end switch
        $this->json(array('code'=>0,'msg'=>'','data'=>$rs[0],'url'=>$url));
      }
      else
        $this->json(array('code'=>1,'msg'=>'No Existe Recibo'));
    }
    
  } //end function
  
  } //end class

?>