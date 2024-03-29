<?php
/**
 * Descripci�n Corta
 * 
 * Descripci�n Larga
 * 
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer el archivo LICENSE
 * @version    1.0
 * @since      Archivo disponible desde su version 1.0
 */
/**
 * Descripci�n Corta
 * Descripci�n Larga
 * @category   
 * @package    
 * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
 * @license    Leer archivo LICENSE
 */
class Credito_CajaController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    
  } //end function
  
  /**
   * Lista los desembolsos pendientes
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function desembolsolistaAction () { 
    $this->view->subTitle="Lista de Desembolsos";
    $this->view->desembolsos=$this->db->fetchAll("SELECT K_COD, K_DOC,  K_NOM, K_PLAZO, K_MONTO FROM CR_SP_BUSCAR_CREDITO_QRY(2,'','A')");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function desembolsocreditoAction () { 
    $this->view->subTitle="Desembolso de Cr�dito";
    if($this->_request->isXmlHttpRequest()){
      $this->db->beginTransaction();
      try{
        $this->db->query('EXECUTE PROCEDURE CJ_SP_DESEMBOLSO_CREDITO_RET(?,?,?)',
                         array($_REQUEST['COD'],
                               $_REQUEST['FEC'],
                               $this->identity->USR));
        $cr=new Credito();
        $crono=$cr->cronogramaCredito($_REQUEST['COD']);
        $cuotas=&$crono['cuotas'];
        //        print_r($cuotas);
        foreach($cuotas as $cuota){
          $this->db->query('INSERT INTO CR_CUOTA(CREDITO_COD, FEC, CUOTA, SALDO_CAPITAL,CAPITAL, INTERES, ITF, SEGURO,OTROS, IGV, TOTAL,TOTAL_ITF, DIA, DIF) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                           array($_REQUEST['COD'],
                                 $cuota->FECHA, 
                                 $cuota->CUOTA, 
                                 number_format($cuota->SALDO_CAPITAL,2,'.',''), 
                                 number_format($cuota->CAPITAL,2,'.',''), 
                                 number_format($cuota->INTERES,2,'.',''), 
                                 number_format($cuota->ITF,2,'.',''),
                                 number_format($cuota->SEGURO,2,'.',''),
                                 number_format($cuota->GA,2,'.',''), 
                                 number_format($cuota->IGV,2,'.',''), 
                                 number_format($cuota->TOTAL,2,'.',''), 
                                 number_format($cuota->TOTAL_ITF,2,'.',''),
                                 $cuota->DIA,
                                 number_format($cuota->DIF,2,'.',''))
                           );
        }
        $this->db->commit();
        $this->db->commit();
        //Verificamos si existe un credito mas
        $renovacion=$this->db->fetchOne("SELECT K_RET FROM CR_SP_ES_RENOVACION_RET(?)",$_REQUEST['COD']);
        $extrajudicial=$this->db->fetchOne("SELECT K_RET FROM CR_SP_TRANS_EXTRA_INS(?)",$_REQUEST['COD']);
        $this->json(array('code'=>'0','msg'=>'Desembolso Realizado','data'=>$renovacion,'extrajudicial'=>$extrajudicial));
      } catch(Exception $e){
        $this->db->rollBack();
        $this->json(array('code'=>'1','msg'=>$e->getMessage()));
      }
    }
    //Datos a mostrar
    $credito=$_REQUEST['COD'];
    $cr=new Credito();
    $this->view->cliente=$cr->getCliente($credito);
    $this->view->credito=$cr->getMontoDesembolsoCredito($credito);
    //print_r($this->view->cliente);
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function pagocuotaAction () { 
    $this->view->subTitle="Pago de Cuota";
    $this->view->cod=$this->getCodRecibo();
    if($this->_request->isXmlHttpRequest()){
      try{
        $recibo=$this->db->fetchOne('SELECT K_COD FROM CR_SP_REALIZAR_PAGO_RET(?,?,?,?)',
                                    array($_REQUEST['FEC'],
                                          $_REQUEST['CREDITO'],
                                          $_REQUEST['MONTO'],
                                          $this->identity->USR));
        $this->json(array("code"=>0,"msg"=>"Pago Realizado",'data'=>array('RECIBO'=>$recibo,'COD'=>$this->getCodRecibo())));
      }
      catch (Exception $e){
        $this->json(array("code"=>1,"msg"=>$e->getMessage()));
      }
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function montocancelarAction () { 
    if ($this->_request->isXmlHttpRequest()){
      $data=$this->db->fetchOne("SELECT SUM(TOTAL_ITF-COALESCE(TOTAL_CANCELADO,0)) FROM CR_CUOTA WHERE CREDITO_COD=? AND ID<=? AND EST='A'",array($_REQUEST['COD'],$_REQUEST['ID']));
      $this->json(array('code'=>0,'msg'=>'','data'=>$data));
    }
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function pagocuotarepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $empresa= new Empresa();
    $credito= new Credito();
    $recibo=$this->db->fetchAll("SELECT * FROM CR_RECIBO WHERE COD=?",array($r['COD']));
    $this->view->plazo=$this->db->fetchOne("SELECT CR.PLAZO FROM CR_RECIBO R INNER JOIN CR_CREDITO CR ON R.CREDITO_COD=CR.COD WHERE R.COD=?",array($r['COD']));
    $this->view->recibo=$recibo[0];
    $this->view->cliente=$credito->getCliente($recibo[0]->CREDITO_COD);
    $this->view->moneda=$credito->getMoneda($recibo[0]->CREDITO_COD);
    $this->view->montoPendiente=$this->db->fetchOne("SELECT * FROM CR_SP_MONTO_PENDIENTE_QRY(?)",array($recibo[0]->CREDITO_COD));
    $this->view->empresa=$empresa->datosBasicos();
    $this->view->detalles=$this->db->fetchAll("SELECT * FROM CR_RECIBO_CUOTA WHERE RECIBO_COD=?",$r['COD']);;
        
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCodRecibo () { 
    return trim($this->db->fetchOne("SELECT LPAD(K_RET,12,'0') FROM QP_SP_GENERADOR_QRY('RECCAJA',1)"));
    
  } //end function
  
  /**
   * Extrae los datos del desembolso
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function creditoestadoAction () { 
    if ($this->_request->isXmlHttpRequest()){
      $_REQUEST['COD']=str_pad($_REQUEST['COD'],12,'0',STR_PAD_LEFT);
      $cr=new Credito();
      $data=$cr->getEstadoCredito($_REQUEST['COD'],$_REQUEST['FEC']);
      $total=array('','TOTAL',0,0,0,0,0,'',0,'');
      for($i=0;$i<count($data);$i++){
        if ($data[$i]->K_EST=='A') {
        $total[2]+=$data[$i]->K_SUBTOTAL;
        $total[3]+=$data[$i]->K_MORA;
        $total[4]+=$data[$i]->K_ITF;
        $total[5]+=$data[$i]->K_TOTAL;
        $total[6]+=$data[$i]->K_A_CUENTA;
        $total[8]+=$data[$i]->K_SALDO;
        }
      }
      $total[2]=number_format($total[2],2,'.','');
      $total[3]=number_format($total[3],2,'.','');
      $total[4]=number_format($total[4],2,'.','');
      $total[5]=number_format($total[5],2,'.','');
      $total[6]=number_format($total[6],2,'.','');
      $total[8]=number_format($total[8],2,'.','');
      array_push($data,$total);
      $cliente=$cr->getCliente($_REQUEST['COD']);
      $this->json(array('code'=>'0','msg'=>'','data'=>array('credito'=>$data,'cliente'=>$cliente)));
    }
  } //end function
    
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function pagocreditoAction () { 
    $this->view->subTitle="Pago de Cr�dito";    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function ingresosalidadiaAction () { 
    $this->view->subTitle="Ingresos y Salidas Diarias";
    $this->view->monedas=$this->getMonedas();
  } //end function
  function ingresosalidadiatotalAction () { 
    $this->view->subTitle="Ingresos y Salidas Diarias";
    $this->view->empleados=$this->getEmpleados();
    $this->view->monedas=$this->getMonedas();
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function ingresosalidafechaAction () { 
    $this->view->subTitle="Ingresos y Salidas entre Fechas";
    $this->view->empleados=$this->getEmpleados();
    $this->view->monedas=$this->getMonedas();
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getMonedas(){
    return $this->db->fetchAll("SELECT COD,NOM FROM CR_MONEDA");
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getIngresosSalidas ($id) { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $this->view->fecha=$r['FEC'];
    if ($id != null) $where=" WHERE K_USR='$id'";
    $this->view->moneda=$this->db->fetchOne('SELECT NOM FROM CR_MONEDA WHERE COD=?',array($r['MON']));
    $this->view->salidas=$this->db->fetchAll("SELECT * FROM CJ_SP_SALIDAS_DIA_QRY(?,?) $where",array($r['FEC'],$r['MON']));
    $this->view->totalSalidas=$this->db->fetchOne("SELECT SUM(K_MONTO) FROM CJ_SP_SALIDAS_DIA_QRY(?,?) $where",array($r['FEC'],$r['MON']));
    $this->view->ingresos=$this->db->fetchAll("SELECT * FROM CJ_SP_INGRESOS_DIA_QRY(?,?) $where",array($r['FEC'],$r['MON']));
    $this->view->ingresosOtro=$this->db->fetchAll("SELECT * FROM CJ_SP_INGRESOS_OTRO_DIA_QRY(?,?) $where",array($r['FEC'],$r['MON']));
    if ($id==null)
      $rs=$this->db->fetchAll('SELECT * FROM CJ_SP_INGRESOS_TOTAL_DIA_QRY(?,?,null)',array($r['FEC'],$r['MON']));
    else
      $rs=$this->db->fetchAll('SELECT * FROM CJ_SP_INGRESOS_TOTAL_DIA_QRY(?,?,?)',array($r['FEC'],$r['MON'],$id));
    $this->view->totalIngresos=$rs[0];
    if ($r['COD']==null)
      $this->view->movimientos=$this->db->fetchAll('SELECT * FROM CJ_SP_MOV_DIA_QRY(?,?,null)',array('001',$r['FEC']));
    else
      $this->view->movimientos=$this->db->fetchAll('SELECT * FROM CJ_SP_MOV_DIA_QRY(?,?,?)',array('001',$r['FEC'],$r['COD']));
    //echo $r['COD'];
    $this->view->ingresosAnulados=$this->db->fetchAll("SELECT * FROM CJ_SP_ANULADOS_DIA_QRY(?,?) $where",array($r['FEC'],$r['MON']));
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function ingresosalidadiarepAction () { 
    $this->getIngresosSalidas($this->identity->USR);
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function ingresosalidadiatotalrepAction () { 
    $r=$this->_request->getParams();
    if ($r['COD']!=''){
      $this->view->empleado=$this->db->fetchOne("SELECT NOM_LARGO FROM QP_PERSONA WHERE COD=?",array($r['COD']));
      $id=$this->db->fetchOne("SELECT USR FROM SG_USUARIO WHERE COD=?",array($r['COD']));
    }
    else $id=null;
    $this->getIngresosSalidas($id);    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function ingresosalidafecharepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml

    $r=$this->_request->getParams();
    $this->view->fecha_desde=$r['FEC_DESDE'];
    $this->view->fecha_hasta=$r['FEC_HASTA'];
    if ($r['COD']!=''){
      $this->view->empleado=$this->db->fetchOne("SELECT NOM_LARGO FROM QP_PERSONA WHERE COD=?",array($r['COD']));
      $id=$this->db->fetchOne("SELECT USR FROM SG_USUARIO WHERE COD=?",array($r['COD']));
    }
    else $id=null;
    if ($id != null) $where=" WHERE K_USR='$id'";
    $this->view->moneda=$this->db->fetchOne('SELECT NOM FROM CR_MONEDA WHERE COD=?',array($r['MON']));
    $this->view->salidas=$this->db->fetchAll("SELECT * FROM CJ_SP_SALIDAS_FECHA_QRY(?,?,?) $where",array($r['FEC_DESDE'],$r['FEC_HASTA'],$r['MON']));
    $this->view->totalSalidas=$this->db->fetchOne("SELECT SUM(K_MONTO) FROM CJ_SP_SALIDAS_FECHA_QRY(?,?,?) $where",array($r['FEC_DESDE'],$r['FEC_HASTA'],$r['MON']));
    $this->view->ingresos=$this->db->fetchAll("SELECT * FROM CJ_SP_INGRESOS_FECHA_QRY (?,?,?) $where",array($r['FEC_DESDE'],$r['FEC_HASTA'],$r['MON']));
    $this->view->ingresosOtro=$this->db->fetchAll("SELECT * FROM CJ_SP_INGRESOS_OTRO_FECHA_QRY(?,?,?) $where",array($r['FEC_DESDE'],$r['FEC_HASTA'],$r['MON']));
    if ($r['COD']==null)
      $rs=$this->db->fetchAll('SELECT * FROM CJ_SP_INGRESOS_TOTAL_FECHA_QRY(?,?,?,null)',array($r['FEC_DESDE'],$r['FEC_HASTA'],$r['MON']));
    else
      $rs=$this->db->fetchAll('SELECT * FROM CJ_SP_INGRESOS_TOTAL_FECHA_QRY(?,?,?,?)',array($r['FEC_DESDE'],$r['FEC_HASTA'],$r['MON'],$id));
    
    $this->view->totalIngresos=$rs[0];
    $this->view->ingresosAnulados=$this->db->fetchAll("SELECT * FROM CJ_SP_ANULADOS_FECHA_QRY(?,?,?) $where",array($r['FEC_DESDE'],$r['FEC_HASTA'],$r['MON']));
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getEmpleados () { 
    return $this->db->fetchAll("SELECT P.COD,P.NOM_LARGO FROM QP_EMPLEADO E INNER JOIN QP_PERSONA P ON E.COD=P.COD WHERE P.COD<>'000000000000' ORDER BY P.NOM_LARGO");

  } //end function
  
  /**
   * Anula un recibo de Pago
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function anulareciboAction () { 
    $this->view->subTitle="Anular Recibo";
    if ($this->_request->isXmlHttpRequest()){
      switch($_REQUEST['op']){
      case 'ins': 
        $this->db->beginTransaction();
        try{
          $this->db->insert('CR_RECIBO_ANULAR',
                            array('RECIBO_COD'=>$_REQUEST['COD'],
                                  'OBS'=>$_REQUEST['OBS'])
                            );
          $this->db->query('EXECUTE PROCEDURE CJ_SP_ANULAR_RECIBO_RET (?)',array($_REQUEST['COD'])); 
          $this->db->commit();
          $this->json(array('code'=>0,'msg'=>'Se Anulo el recibo # '.$_REQUEST['COD']));
        } catch (Exception $e){
          $this->db->rollBack();
          $this->json(array('code'=>1,'msg'=>$e->getMessage()));
        }
        
        break;
      default: $rs=$this->db->fetchAll("SELECT r.COD, R.CREDITO_COD, CR.CLIENTE_COD, CL.NOM_LARGO AS CLIENTE_NOM, R.MONTO FROM CR_RECIBO R LEFT JOIN CR_CREDITO CR ON R.CREDITO_COD=CR.COD LEFT JOIN CR_CLIENTE CL ON CR.CLIENTE_COD=CL.COD WHERE R.COD=LPAD('{$_REQUEST['COD']}',12,'0') AND R.EST=1");
        $this->json(array('code'=>'0','msg'=>'','data'=>$rs[0]));
      }
    }
  } //end function
  /**
   * Sistema de Billetajes
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function billetajeAction () { 
    $this->view->subTitle="Billetaje Diario";
    $this->view->monedas=$this->getMonedas();
    $this->view->cajas=$this->getCajas();
    $this->view->tipoBilletes=$this->getTipoBilletes();
    if ($this->_request->isXmlHttpRequest()){

      switch ($_REQUEST['op']) {
      case 'ins' : 
        $this->db->beginTransaction();
        try{
          foreach($this->view->monedas as $moneda){
            foreach($this->view->tipoBilletes as $tipoBillete){
              $this->db->query("EXECUTE PROCEDURE CJ_SP_BILLETAJE_INS(?,?,?,?,?,?,?,?)",
                               array(
                                     '001',
                                     $_REQUEST['CAJA_COD'],
                                     $_REQUEST['FEC'],
                                     $_REQUEST['TIPO'],
                                     $moneda->COD,
                                     $tipoBillete->COD,
                                     $_REQUEST[$moneda->COD.'_'.$tipoBillete->COD],
                                     '1'
                                     ));
            }
          }
          $this->db->commit();
          $this->json(array('code'=>'0','msg'=>'Registro guardado'));
        } catch (Exception $e){
          $this->db->rollback();
          $this->json(array('code'=>'1','msg'=>$e->getMessage()));
        }
        
        break;
      case 'get' : 
        $rs=$this->db->fetchAll("SELECT MONEDA_COD||'_'||TIPO_BILLETE_COD AS COD,CANT,EST FROM CJ_BILLETAJE WHERE AGENCIA_COD=? AND CAJA_COD=? AND FEC=? AND TIPO=?",
                                array(
                                      '001',
                                      $_REQUEST['CAJA_COD'],
                                      $_REQUEST['FEC'],
                                      $_REQUEST['TIPO']
                                      ));
        $this->json(array('code'=>'0','msg'=>'','data'=>$rs));
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
  function controlcajadiaAction () { 
    $this->view->subTitle="Control de Caja Diario";
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function controlcajadiarepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $this->view->fecha=$r['FEC'];
    $this->view->cajas=$this->db->fetchAll("SELECT * FROM CJ_SP_CAJAS_QRY('001')");
    $this->view->tbi=array();
    $this->view->tbc=array();
    $this->view->total=array();
    foreach($this->view->cajas as $caja){
      $this->view->tbi['i'][$caja->K_COD]=$this->db->fetchAll("SELECT * FROM CJ_SP_BILLETAJE_CAJA_DIA_QRY('001',?,?,'I')",array($caja->K_COD,$r['FEC']));//caja de ingreso
      $this->view->tbc['c'][$caja->K_COD]=$this->db->fetchAll("SELECT * FROM CJ_SP_BILLETAJE_CAJA_DIA_QRY('001',?,?,'C')",array($caja->K_COD,$r['FEC']));//caja de salida
      $this->view->tbi['ti'][$caja->K_COD]=$this->db->fetchAll("SELECT * FROM CJ_SP_BILLETAJE_TOTAL_DIA_QRY('001',?,?,'I')",array($caja->K_COD,$r['FEC']));//caja de ingreso
      $this->view->tbc['tc'][$caja->K_COD]=$this->db->fetchAll("SELECT * FROM CJ_SP_BILLETAJE_TOTAL_DIA_QRY('001',?,?,'C')",array($caja->K_COD,$r['FEC']));//caja de ingreso
    }
    $this->view->total['i']=$this->db->fetchAll("SELECT * FROM CJ_SP_BILLETAJE_DIA_TOTAL_QRY('001',?,'I')",array($r['FEC']));//caja de ingreso
    $this->view->total['c']=$this->db->fetchAll("SELECT * FROM CJ_SP_BILLETAJE_DIA_TOTAL_QRY('001',?,'C')",array($r['FEC']));//caja de salida
    $this->view->total['ti']=$this->db->fetchAll("SELECT * FROM CJ_SP_BILLETAJE_TOTAL_TOTAL_QRY('001',?,'I')",array($r['FEC']));//caja de salida;
    $this->view->total['tc']=$this->db->fetchAll("SELECT * FROM CJ_SP_BILLETAJE_TOTAL_TOTAL_QRY('001',?,'C')",array($r['FEC']));//caja de salida;
    
    $this->view->salida=$this->db->fetchAll("SELECT * FROM CJ_SP_CONTROL_SALIDA_QRY('001',?)",array($r['FEC']));//caja de salida;
    $this->view->ingreso=$this->db->fetchAll("SELECT * FROM CJ_SP_CONTROL_INGRESO_QRY('001',?)",array($r['FEC']));//caja de salida;
    $this->view->recibosIncoherentes=$this->db->fetchAll("SELECT * FROM CJ_SP_RECIBO_INCOHERENTE_QRY('001',?)",array($r['FEC']));//caja de salida;
    $this->view->desembolsos=$this->db->fetchAll("SELECT * FROM CJ_SP_DESEMBOLSOS_DIA_QRY(?)",array($r['FEC']));
    $this->view->recibos=$this->db->fetchAll("SELECT * FROM CJ_SP_RECIBOS_DIA_QRY(?,'0000')",array($r['FEC']));
  } //end functionb
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function reciboAction () { 
    $this->view->subTitle='Recibo de Pago';
    $this->view->igv=$this->db->fetchOne("SELECT IGV*(1.00)/100.00 FROM CN_VW_IMPUESTO");
    if($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['opc']) {
      case 'get': $this->json(array('code'=>'0','msg'=>'','data'=>$this->getCodRecibo()));
        break;
      case 'ins' : 
        $this->db->beginTransaction();
        try{
          $per=$this->db->fetchOne('SELECT K_COD_RET FROM QP_SP_PERSONA_INS(NULL,?,?,NULL, ?,?,?,?,NULL,NULL,NULL)',
                                   array($_REQUEST['TIPO_DOC_COD'],
                                         $_REQUEST['DNI'],
                                         $_REQUEST['NOM'],
                                         $_REQUEST['APE_PAT'],
                                         $_REQUEST['APE_MAT'],
                                         $_REQUEST['DIRE']
                                         )
                                   );
          /*$cod=$this->getCodRecibo();
          $this->db->insert('CR_RECIBO',
                            array('COD'=>$cod,
                                  'PERSONA_COD'=>$per,
                                  'FEC'=>$_REQUEST['FEC'],
                                  'MONEDA_COD'=>$_REQUEST['MONEDA_COD'],
                                  'MONTO'=>$_REQUEST['MONTO'],
                                  'TIPO'=>'O',
                                  'DET'=>$_REQUEST['DET'],
                                  'AFECTO_IGV'=>$_REQUEST['AFECTO_IGV'],
                                  'IGV'=>($_REQUEST['AFECTO_IGV']==1)?$_REQUEST['IGV']:0,
                                  'A_CUENTA'=>'0',
                                  'ITF'=>'0',
                                  'USR'=>$this->identity->USR
                                  ));
          $this->db->query("EXECUTE PROCEDURE QP_SP_GENERADOR_UPD('RECCAJA',1);");
          $nuevo=$this->getCodRecibo();
          */
          $nuevo=$this->db->fetchAll("SELECT * FROM CJ_SP_RECIBO_OTRO_INS(?,?,?,?,?,?,?,?)",array(
                                  $per,
                                  $_REQUEST['FEC'],
                                  $_REQUEST['MONEDA_COD'],
                                  $_REQUEST['MONTO'],
                                  $_REQUEST['DET'],
                                  (isset($_REQUEST['AFECTO_IGV']))?1:0,
                                  (isset($_REQUEST['AFECTO_IGV']))?$_REQUEST['IGV']:0,
                                  $this->identity->USR

));
          $this->db->commit();
          $this->json(array('code'=>'0','msg'=>'Registro guardado','data'=>$nuevo[0]->K_COD_ANT,'nuevo'=>$nuevo[0]->K_COD_NUE));
        }
        catch(Exception $e){
          $this->db->rollBack();
          $this->json(array('code'=>'1','msg'=>$e->getMessage()));
        }
        break;
        
      default: break;
      } //end switch

    }
    $this->view->tipoDocs=$this->getTipoDocs();
    $this->view->monedas=$this->getMonedas();

  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function reciborepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $empresa= new Empresa();
    //
    //        $credito= new Credito();
    $recibo=$this->db->fetchAll("SELECT * FROM CR_SP_EXTRAER_RECIBO_QRY(?)",array($r['COD']));
    $this->view->recibo=$recibo[0];
    //        $this->view->moneda=$credito->getMoneda($recibo[0]->CREDITO_COD);
    $this->view->empresa=$empresa->datosBasicos();    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function docprovAction () { 
    $this->view->subTitle='Pago a Proveedores';
    $this->view->monedas=$this->getMonedas();
    if($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['op']) {
      case 'getcod' : 
        $this->json(array('code'=>'0','msg'=>'','data'=>$this->getIdReciboProveedor()));  
        //$this->db->fetchOne("SELECT K_RET FROM QP_SP_GENERADOR_QRY('DOCPROV',1)");
        break;
      case 'ins' : 
        try{
        
          $this->db->insert('CJ_DOC_PROV',
                            array('AGENCIA_COD'=>'001',
                                  'NUM'=>$_REQUEST['NUM'],
                                  'FEC'=>$_REQUEST['FEC'],
                                  'CLIENTE_COD'=>$_REQUEST['PROVEEDOR_COD'],
                                  'MONEDA_COD'=>$_REQUEST['MONEDA_COD'],
                                  'MONTO'=>$_REQUEST['MONTO'],
                                  'DET'=>$_REQUEST['DET'],
                                  'USR'=>$this->identity->USR
                                  ));
          $cod=$this->getIdReciboProveedor();
          $this->json(array('code'=>'0','msg'=>'Registro guardado','data'=>$cod));  
        } catch (Exception $e){
          $this->json(array('code'=>'0','msg'=>$e->getMessage()));  
        }
        
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
  function tipocajaAction () { 
    $this->view->subTitle='Tipo de Caja';
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM FROM CJ_TIPO_CAJA WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getTipoCaja();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("CJ_TIPO_CAJA",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getTipoCaja()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('CJ_TIPO_CAJA',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getTipoCaja()));
          break;
        case 'del':
          $rs=$this->db->delete('CJ_TIPO_CAJA',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getTipoCaja()));        
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
  function getTipoCaja () { 
    return $this->db->fetchAll("SELECT COD,NOM FROM CJ_TIPO_CAJA");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getIdReciboProveedor () { 
    return $this->db->fetchOne("SELECT LPAD(COALESCE(MAX (ID) ,0)+1,12,'0') FROM CJ_DOC_PROV WHERE AGENCIA_COD='001'");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function docprovrepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $empresa= new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    //$credito= new Credito();
    $recibo=$this->db->fetchAll("SELECT * FROM CJ_SP_DOC_PROV_QRY(?)",array($r['ID']));
    $this->view->recibo=$recibo[0];
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function proveedorAction () { 
    $this->view->subTitle='Proveedor';
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'ins':
          $this->db->beginTransaction();
          try{
            $_REQUEST['COD']=$this->getProveedorCod();
            $_REQUEST['PERSONA_COD']=$this->db->fetchOne('SELECT K_COD_RET FROM QP_SP_PERSONA_INS(?,?,?,?,?,?,?,?,?,?,?)',
                                                         array(
                                                               $_REQUEST['COD'],
                                                               $_REQUEST['TIPO_DOC_COD'],
                                                               $_REQUEST['DNI'],
                                                               $_REQUEST['DNI_VEN'],
                                                               $_REQUEST['NOM'],
                                                               $_REQUEST['APE_PAT'],
                                                               $_REQUEST['APE_MAT'],
                                                               $_REQUEST['DIRE'],
                                                               $_REQUEST['FONO1'],
                                                               $_REQUEST['FEC_NAC'],
                                                               $_REQUEST['SEXO']
                                                               ));
            if ($_REQUEST['TIPO']=='N'){
              //$_REQUEST['COD']=$_REQUEST['PERSONA_COD'];
              $this->db->insert('CR_CLIENTE',array(
                                                   'COD'=>$_REQUEST['COD'],
                                                   'TIPO'=>$_REQUEST['TIPO'],
                                                   'PERSONA_COD'=>$_REQUEST['PERSONA_COD'],
                                                   'DIRE'=>$_REQUEST['DIRE'],
                                                   'LOCALIDAD_COD'=>$_REQUEST['LOCALIDAD_COD'],
                                                   'FONO1'=>$_REQUEST['FONO1'],
                                                   'FONO2'=>$_REQUEST['FONO2'],
                                                   'ESTADO_CIVIL'=>$_REQUEST['ESTADO_CIVIL'],
                                                   'CONYUGE'=>($_REQUEST['CONYUGE']=='')?null:$_REQUEST['CONYUGE'],
                                                   'DIR_NEGOCIO'=>$_REQUEST['DIR_NEGOCIO'],
                                                   'LOCALIDAD_NEGOCIO'=>$_REQUEST['LOCALIDAD_NEGOCIO'],
                                                   'ACTIVIDAD_COD'=>$_REQUEST['ACTIVIDAD_COD']
                                                   ));
            }
            else{
              $this->db->insert('CR_CLIENTE',array(
                                                   'COD'=>$_REQUEST['COD'],
                                                   'TIPO'=>$_REQUEST['TIPO'],
                                                   'PERSONA_COD'=>$_REQUEST['PERSONA_COD'],
                                                   'RUC'=>$_REQUEST['RUC'],
                                                   'NOM_COMERCIAL'=>$_REQUEST['NOM_COMERCIAL'],
                                                   'NOM_LARGO'=>$_REQUEST['NOM_COMERCIAL'],
                                                   'SIGLAS'=>$_REQUEST['SIGLAS'],
                                                   'DIRE'=>$_REQUEST['DIRE'],
                                                   'LOCALIDAD_COD'=>$_REQUEST['LOCALIDAD_COD'],
                                                   'FONO1'=>$_REQUEST['FONO1'],
                                                   'FONO2'=>$_REQUEST['FONO2'],
                                                   'DIR_NEGOCIO'=>$_REQUEST['DIR_NEGOCIO'],
                                                   'LOCALIDAD_NEGOCIO'=>$_REQUEST['LOCALIDAD_NEGOCIO'],
                                                   'ACTIVIDAD_COD'=>$_REQUEST['ACTIVIDAD_COD']
                                                   ));
            }
            //Debemos de revisar la tabla de proveedor
            $this->updProveedorCod();
            $nuevoCodigo=$this->getProveedorCod();
            $this->db->commit();
            $this->json(array('code'=>'0', 'msg'=>'Registro Guardado - '.$_REQUEST['COD'],'data'=>$nuevoCodigo));
                          
          } catch (Exception $e){
            $this->db->rollBack();
            $this->json(array('code'=>'0','msg'=>$e->getMessage()));
          }
          
          break;
        case 'upd':
          $this->db->beginTransaction();
          try{
            $_REQUEST['PERSONA_COD']=$this->db->fetchOne('SELECT K_COD_RET FROM QP_SP_PERSONA_INS(?,?,?,?,?,?,?,?,?,?,?)',
                                                         array(
                                                               $_REQUEST['COD'],
                                                               $_REQUEST['TIPO_DOC_COD'],
                                                               $_REQUEST['DNI'],
                                                               $_REQUEST['DNI_VEN'],
                                                               $_REQUEST['NOM'],
                                                               $_REQUEST['APE_PAT'],
                                                               $_REQUEST['APE_MAT'],
                                                               $_REQUEST['DIRE'],
                                                               $_REQUEST['FONO1'],
                                                               $_REQUEST['FEC_NAC'],
                                                               $_REQUEST['SEXO']
                                                               ));
            if ($_REQUEST['TIPO']=='N'){
              //$_REQUEST['COD']=$_REQUEST['PERSONA_COD'];
              $this->db->update('CR_CLIENTE',
                                array(
                                      'TIPO'=>$_REQUEST['TIPO'],
                                      'PERSONA_COD'=>$_REQUEST['PERSONA_COD'],
                                      'DIRE'=>$_REQUEST['DIRE'],
                                      'LOCALIDAD_COD'=>$_REQUEST['LOCALIDAD_COD'],
                                      'FONO1'=>$_REQUEST['FONO1'],
                                      'FONO2'=>$_REQUEST['FONO2'],
                                      'LOCALIDAD_NEGOCIO'=>$_REQUEST['LOCALIDAD_NEGOCIO'],
                                      ),
                                "COD='{$_REQUEST['COD']}'");
            }
            else{
              $this->db->update('CR_CLIENTE',                                
                                array(
                                      'TIPO'=>$_REQUEST['TIPO'],
                                      'PERSONA_COD'=>$_REQUEST['PERSONA_COD'],
                                      'RUC'=>$_REQUEST['RUC'],
                                      'NOM_COMERCIAL'=>$_REQUEST['NOM_COMERCIAL'],
                                      'NOM_LARGO'=>$_REQUEST['NOM_COMERCIAL'],
                                      'DIRE'=>$_REQUEST['DIRE'],
                                      'LOCALIDAD_COD'=>$_REQUEST['LOCALIDAD_COD'],
                                      'FONO1'=>$_REQUEST['FONO1'],
                                      'FONO2'=>$_REQUEST['FONO2'],
                                      'DIR_NEGOCIO'=>$_REQUEST['DIR_NEGOCIO'],
                                      'LOCALIDAD_NEGOCIO'=>$_REQUEST['LOCALIDAD_NEGOCIO'],
                                      'ACTIVIDAD_COD'=>$_REQUEST['ACTIVIDAD_COD']
                                      ),
                                "COD='{$_REQUEST['COD']}'");
            }
            $nuevoCodigo=$this->getProveedorCod();
            $this->db->commit();
            $this->json(array('code'=>'0', 'msg'=>'Registro Guardado - '.$_REQUEST['COD'],'data'=>$nuevoCodigo));
                          
          } catch (Exception $e){
            $this->db->rollBack();
            $this->json(array('code'=>'0','msg'=>$e->getMessage()));
          }
          
          break;
        }
      } catch (Exception $e){
        $this->json(array('code'=>'0','msg'=>$e->getMessage()));
      }
  
    }
    $this->view->localidades=$this->getLocalidad();
    //    $this->view->actividades=$this->getActividades();
    $this->view->cod=$this->getProveedorCod();
    $this->view->tipoDocumentos=$this->getTipoDoc();
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function movsencilloAction () { 
    $this->view->subTitle="Movimiento de Sencillo";
    $this->view->cajas=$this->getCajas();
    $this->view->cajaFuente=$this->getCajeroUno($this->identity->COD);
    //echo $this->identity->COD;
    if ($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['op']) {
      case 'ins' : 
        $cfa=explode(' - ',$_REQUEST['CAJA_FUENTE']);
        $_REQUEST['CAJA_FUENTE']=$cfa[0];
        $this->db->insert('CJ_MOV_SENCILLO',
                          array('AGENCIA_COD'=>'001',
                                'MONTO'=>$_REQUEST['MONTO'],
                                'CAJA_AFUENTE'=>'001',
                                'CAJA_FUENTE'=>$_REQUEST['CAJA_FUENTE'],
                                'CAJA_ADESTINO'=>'001',
                                'CAJA_DESTINO'=>$_REQUEST['CAJA_DESTINO'],
                                'CAJA_AFUENTE'=>'001'
                                //                            'CAJA_FUENTE'=>
                                ));
        $this->json(array('code'=>'0','msg'=>'Registro Guardado','data'=>$this->getMovSencilloCod('001')));
        break;
      case 'getnum' : 
        $rs=$this->listamovsencillo($this->identity->COD);
        $this->json(array('code'=>'0','msg'=>'','data'=>
                          array('id'=>$this->getMovSencilloCod('001'),
                                'lista'=>$rs)
                          )
                    );
        break;
      case 'aceptar' : 
        $this->db->update('CJ_MOV_SENCILLO',
                          array('EST'=>'A'),
                          "AGENCIA_COD='001' AND ID='{$_REQUEST['ID']}'"
                          );
        $rs=$this->listamovsencillo($this->identity->COD);
        $this->json(array('code'=>'0','msg'=>'Transferencia Aceptada','data'=>
                          array('id'=>$this->getMovSencilloCod('001'),
                                'lista'=>$rs)
                          )
                    );
        
        break;
      case 'anular' : 
        $this->db->update('CJ_MOV_SENCILLO',
                          array('EST'=>'D'),
                          "AGENCIA_COD='001' AND ID='{$_REQUEST['ID']}'"
                          );
        $rs=$this->listamovsencillo($this->identity->COD);
        $this->json(array('code'=>'0','msg'=>'Transferencia Anulada','data'=>
                          array('id'=>$this->getMovSencilloCod('001'),
                                'lista'=>$rs)
                          )
                    );

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
  function listamovsencillo ($cod) { 
    return $this->db->fetchAll('SELECT * FROM CJ_SP_MOV_SEN_ACEPTAR_QRY(?,?)',array($cod,date('Y-m-d')));
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function listadocprovAction () { 
    $this->view->subTitle="Lista de Docs de Pago a Proveedores";
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function listadocprovrepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $empresa= new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    $this->view->lista=$this->db->fetchAll("SELECT * FROM CJ_SP_LISTA_DOC_PROV_QRY(?,?,?)",
                                           array('001',
                                                 $r['FEC_DESDE'],
                                                 $r['FEC_HASTA'])
                                           );
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function listadocclienteAction () { 
    $this->view->subTitle="Lista de Docs Cliente";
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function listadocclienterepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $empresa= new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    $this->view->lista=$this->db->fetchAll("SELECT * FROM CJ_SP_LISTA_DOC_CLIENTE_QRY(?,?,?)",
                                           array('001',
                                                 $r['FEC_DESDE'],
                                                 $r['FEC_HASTA'])
                                           );
  } //end function

  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function cajeroAction () { 
    $this->view->subTitle="Cajeros";
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,EMPLEADO_COD,TIPO_CAJA_COD FROM CJ_CAJA WHERE COD=? AND AGENCIA_COD='001'",array($_REQUEST['idx']));
          else
            $rs=$this->getCajero();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $r['AGENCIA_COD']='001';
          $this->db->insert("CJ_CAJA",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getCajero()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $r['AGENCIA_COD']='001';
          $this->db->update('CJ_CAJA',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getCajero()));
          break;
        case 'del':
          //$this->json(array('code'=>1,'msg'=>'error'));
          $rs=$this->db->delete('CJ_CAJA',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getCajero()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }    
    $this->view->agencias=$this->getAgencias();
    $this->view->empleados=$this->getEmpleados();
    $this->view->tipoCajas=$this->getTipoCajas();

  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCajeroUno ($cod) { 
    return $this->db->fetchAll("SELECT C.COD, P.NOM_LARGO FROM CJ_CAJA C INNER JOIN QP_PERSONA P ON C.EMPLEADO_COD=P.COD WHERE C.AGENCIA_COD='001' AND P.COD='$cod'");
  
  } //end function

  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCajero () { 
    return $this->db->fetchAll("SELECT C.COD, P.NOM_LARGO FROM CJ_CAJA C INNER JOIN QP_PERSONA P ON C.EMPLEADO_COD=P.COD WHERE C.AGENCIA_COD='001'");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getAgencias () { 
    return $this->db->fetchAll("SELECT COD,NOM FROM QP_AGENCIA");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getTipoCajas () { 
    return $this->db->fetchAll("SELECT COD,NOM FROM CJ_TIPO_CAJA ORDER BY NOM");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getMovSencilloCod ($area) { 
    return $this->db->fetchOne("SELECT LPAD(K_NUM,12,'0') FROM CJ_SP_MOV_SENCILLO_NUM_QRY(?)",$area);
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getProveedorCod () { 
    return $this->db->fetchOne("SELECT LPAD(K_RET,12,'0') FROM QP_SP_GENERADOR_QRY('CLIENTE',1)");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function updProveedorCod(){
    $this->db->query("EXECUTE PROCEDURE QP_SP_GENERADOR_UPD('CLIENTE',1)");
  }

  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getLocalidad () { 
    return $this->db->fetchAll('SELECT COD, NOM FROM QP_LOCALIDAD ORDER BY COD');
  } //end function

  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getTipoDoc () { 
    return $this->db->fetchAll('SELECT COD, NOM FROM QP_TIPO_DOC');
  } //end function

  function getTipoDocs(){
    return $this->db->fetchAll("SELECT COD,NOM FROM QP_TIPO_DOC");
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCajas () { 
    return $this->db->fetchAll("SELECT * FROM CJ_SP_CAJAS_QRY('001')");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getTipoBilletes () { 
    return $this->db->fetchAll("SELECT DISTINCT(COD),VAL FROM CJ_TIPO_BILLETE ORDER BY VAL DESC");
  } //end function
  
} //end class

?>