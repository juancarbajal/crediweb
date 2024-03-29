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
class Credito_CreditoController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    $this->view->subTitle="Nuevo Cr�dito";
    if ($this->_request->isXmlHttpRequest()){
      $this->db->beginTransaction();
      try{ 
        $_REQUEST['COD']=$this->getCreditoCod();
        $this->db->insert('CR_CREDITO',array(
                                             'COD'=>$_REQUEST['COD'],
                                             'FEC'=>$_REQUEST['FEC'],
                                             'CLIENTE_COD'=>$_REQUEST['CLIENTE_COD'],
                                             'CIIU_COD'=>$_REQUEST['CIIU_COD'],
                                             'DESTINO_COD'=>$_REQUEST['DESTINO_COD'],
                                             'MODALIDAD_COD'=>$_REQUEST['MODALIDAD_COD'],
                                             'LINEA_COD'=>$_REQUEST['LINEA_COD'],               
                                             'DOC_VIV'=>($_REQUEST['DOC_VIV'])?$_REQUEST['DOC_VIV']:0,
                                             'DOC_PER'=>($_REQUEST['DOC_PER'])?$_REQUEST['DOC_PER']:0,
                                             'DOC_NEG'=>($_REQUEST['DOC_NEG'])?$_REQUEST['DOC_NEG']:0,
                                             //'DIR_NEGOCIO'=>$_REQUEST['DIR_NEGOCIO'],
                                             'MONTO'=>$_REQUEST['MONTO'],
                                             'PLAZO'=>$_REQUEST['PLAZO'],
                                             'TI_MENSUAL'=>$_REQUEST['TI_MENSUAL'],
                                             'TI_MORA'=>$_REQUEST['TI_MORA'],
                                             'TI_COMPENSA'=>$_REQUEST['TI_COMPENSA'],
                                             'OBS'=>$_REQUEST['OBS'],
                                             'USR'=>$this->identity->USR,
                                             'EST'=>'E',
                                             'ANALISTA_COD'=>$_REQUEST['ANALISTA_COD']
                                             ));
        if ($_REQUEST['AVAL1']!='')
          $this->db->insert('CR_AVAL',array('CREDITO_COD'=>$_REQUEST['COD'], 'CLIENTE_COD'=>$_REQUEST['AVAL1']));
        if ($_REQUEST['AVAL2']!='')
          $this->db->insert('CR_AVAL',array('CREDITO_COD'=>$_REQUEST['COD'], 'CLIENTE_COD'=>$_REQUEST['AVAL2']));

        $this->db->query("EXECUTE PROCEDURE QP_SP_GENERADOR_ANIO_UPD('CREDITO',?,1)",array(date('Y')));
        //Guardamos la Solicitud
        $this->db->insert('CR_SOLICITUD',
                          array('CREDITO_COD'=>$_REQUEST['COD'],
                                'DEP_ADULTO'=>$_REQUEST['DEP_ADULTO'],
                                'DEP_MENOR'=>$_REQUEST['DEP_MENOR'],
                                'VAL_INMUEBLE'=>$_REQUEST['VAL_INMUEBLE'],
                                'VAL_MUEBLE_CLIENTE'=>$_REQUEST['VAL_MUEBLE_CLIENTE'],
                                'VAL_MUEBLE_NEGOCIO'=>$_REQUEST['VAL_MUEBLE_NEGOCIO'],
                                'SAL_TOT_1'=>$_REQUEST['SAL_TOT_1'],
                                'SAL_TOT_2'=>$_REQUEST['SAL_TOT_2'],
                                'ACREEDOR'=>$_REQUEST['ACREEDOR'],
                                'CAP_TRAB_EFECTIVO'=>$_REQUEST['CAP_TRAB_EFECTIVO'],
                                'CAP_TRAB_MERCA'=>$_REQUEST['CAP_TRAB_MERCA'],
                                'VENT_ING'=>$_REQUEST['VENT_ING'],
                                'OTROS_ING'=>$_REQUEST['OTROS_ING'],
                                'COSTO_INSUMO'=>$_REQUEST['COSTO_INSUMO'],
                                'GASTO_FAMILIAR'=>$_REQUEST['GASTO_FAMILIAR'],
                                'GASTO_NEGOCIO'=>$_REQUEST['GASTO_NEGOCIO'],
                                'PAGO_PROM_1'=>$_REQUEST['PAGO_PROM_1'],
                                'PAGO_PROM_2'=>$_REQUEST['PAGO_PROM_2'],
                                'PAGO_PROM_ACREEDOR'=>$_REQUEST['PAGO_PROM_ACREEDOR'],
                                'GASTO_PERSONAL'=>$_REQUEST['GASTO_PERSONAL'],
                                'SALDO'=>$_REQUEST['SALDO'],
                                //'INFORME_RIESGO'=>$_REQUEST['INFORME_RIESGO'],
                                'OPINION_ANALISTA'=>$_REQUEST['OPINION_ANALISTA'],
                                'OPINION_SBS'=>$_REQUEST['OPINION_SBS']
                                )
                          );
        $this->updCreditoCod(); //actualizamos el numero de Credito
        $cod=$this->getCreditoCod();
        $this->db->commit();
        $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>array('cod'=>$_REQUEST['COD'],'nuevo'=>$cod)));
      }
      catch(Exception $e){
        $this->db->rollBack();
        $this->json(array('code'=>1,'msg'=>$this->identity->USR.' '.$e->getMessage()));
      }
    }
    $this->view->lineas=$this->getLineas();
    $this->view->cod=$this->getCreditoCod();
    $this->view->modalidades=$this->getModalidad();
    $this->view->destinos=$this->getDestino();
    $this->view->opiniones=$this->getOpiniones();
    $this->view->analistas=$this->getAnalistas();
  } //end function

  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getOpiniones () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM SB_OPINION");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function buscarAction () { 
    $this->view->subTitle="Buscar Cr�dito";
    $this->view->f=$_REQUEST['f'];
    $this->view->est=$_REQUEST['est'];
    if ($this->_request->isXmlHttpRequest()){
      $rs=$this->db->fetchAll("SELECT K_COD,K_DOC,K_NOM,K_PLAZO,K_MONTO FROM CR_SP_BUSCAR_CREDITO_QRY(?,?,?)",
                          array($_REQUEST['opc'],
                                $_REQUEST['valor'],
                                $_REQUEST['est']));
      $this->json(array('code'=>0,'msg'=>'','data'=>$rs));
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function buscardesembolsoAction () { 
    $this->_redirect('credito/credito/buscar?est=X&f='.$_REQUEST['f']);
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function autorizarAction () { 
    $this->view->subTitle="Aprobar Cr�dito";
    if ($this->_request->isXmlHttpRequest()){
      try{
      $this->db->query('EXECUTE PROCEDURE CR_SP_AUTORIZAR_CREDITO_RET(?,?,?,?,?,?,?,?,?,?,?)',array($_REQUEST['COD'],$_REQUEST['LINEA_COD'],$_REQUEST['MODALIDAD_COD'],$_REQUEST['DESTINO_COD'],$_REQUEST['TI_MENSUAL'],$_REQUEST['TI_MORA'],$_REQUEST['TI_MORA'],$_REQUEST['MONTO'],$_REQUEST['PLAZO'],$_REQUEST['COBRADOR_COD'],$this->identity->USR));
      $this->json(array('code'=>0,'msg'=>'Credito Autorizado'));
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }
    $this->view->modalidades=$this->getModalidad();
    $this->view->destinos=$this->getDestino();
    $this->view->lineas=$this->getLineas();
    $this->view->cobradores=$this->getCobradores();
    $rs=$this->db->fetchAll("SELECT * FROM CR_SP_EXTRAER_CRED_AUTO_QRY(?)",array($_REQUEST['COD']));
    $this->view->cr=$rs[0];
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function simulacionAction () { 
    $this->view->subTitle="Simular Cr�dito";
    $this->view->lineas=$this->getLineas();
    $this->view->modalidades=$this->getModalidad();
    $this->view->destinos=$this->getDestino();
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function avalAction () { 
    
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function listaautorizarAction () { 
    $this->view->subTitle="Aprobar Cr�dito";
    $this->view->lista=$this->db->fetchAll("SELECT * FROM CR_VW_LISTA_CREDITO_SIN_AUTO");
    /*if ($this->_request->isXmlHttpRequest()){
      $rs=$this->db->fetchAll("SELECT * FROM CR_VW_LISTA_CREDITO_SIN_AUTO");
      $this->json(array('code'=>'0','msg'=>'','data'=>$rs));
    }*/
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
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
  function anularAction () { 
    if($this->_request->isXmlHttpRequest()){
      try{
        $this->db->query('EXECUTE PROCEDURE CR_SP_ANULAR_CREDITO_RET(?)',array($_REQUEST['COD']));
        $rs=$this->db->fetchAll("SELECT * FROM CR_VW_LISTA_CREDITO_SIN_AUTO");
        $this->json(array('code'=>'0','msg'=>'Registro Anulado','data'=>$rs));
      }
      catch(Exception $e){
        $this->json(array('code'=>'0','msg'=>$e->getMessage()));
      }
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function denegarAction () { 
    if($this->_request->isXmlHttpRequest()){
      try{
      $this->db->query('EXECUTE PROCEDURE CR_SP_DENEGAR_CREDITO_RET(?)',array($_REQUEST['COD']));
      $rs=$this->db->fetchAll("SELECT * FROM CR_VW_LISTA_CREDITO_SIN_AUTO");
      $this->json(array('code'=>'0','msg'=>'Registro Denegado','data'=>$rs));
      }
      catch(Exception $e){
          $this->json(array('code'=>'0','msg'=>$e->getMessage()));
      }
    }    
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
  function getModalidad () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM CR_MODALIDAD");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getDestino () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM CR_DESTINO");
  } //end function
  
  function lineaAction(){
    if ($this->_request->isXmlHttpRequest()){
      $rs=$this->db->fetchAll("SELECT COD, NOM, TI_MENSUAL, TI_MORA, TI_COMPENSA, GA, CAST(MONTO_MIN AS DECIMAL(10,0)) AS MONTO_MIN, CAST(MONTO_MAX AS DECIMAL(10,0)) AS MONTO_MAX, CUOTA_MIN, CUOTA_MAX FROM CR_LINEA WHERE COD=?",array($_REQUEST['COD']));
      $this->json(array('code'=>0,'msg'=>'','data'=>$rs));
    }
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function clienteAction () { 
    if ($this->_request->isXmlHttpRequest()){
      $rs=$this->db->fetchAll("SELECT FIRST 1 C.COD, C.NOM_LARGO, C.DIRE, C.LOCALIDAD_COD, L.NOM AS LOCALIDAD_NOM, C.DIR_NEGOCIO, C.LOCALIDAD_NEGOCIO, N.NOM AS LOCALIDAD_NEG_NOM, CR.CIIU_COD, CI.NOM AS CIIU_NOM FROM CR_CLIENTE C INNER JOIN QP_LOCALIDAD L ON C.LOCALIDAD_COD=L.COD LEFT JOIN QP_LOCALIDAD N ON C.LOCALIDAD_NEGOCIO=N.COD LEFT JOIN CR_CREDITO CR ON C.COD=CR.CLIENTE_COD LEFT JOIN CR_CIIU CI ON CR.CIIU_COD=CI.COD WHERE C.COD=LPAD('{$_REQUEST['COD']}',12,'0')");
      $rsEdad=$this->db->fetchAll("SELECT * FROM CR_SP_VERIFICAR_EDAD_QRY(LPAD('{$_REQUEST['COD']}',12,'0'),65)");
      $rsDNI=$this->db->fetchAll("SELECT * FROM CR_SP_VERIFICAR_DNI_VEN_QRY(LPAD('{$_REQUEST['COD']}',12,'0'))");
      $rsInforme=$this->db->fetchAll("SELECT * FROM CR_SP_INFORME_2_CRED_QRY(LPAD('{$_REQUEST['COD']}',12,'0'))");
      $this->json(array('code'=>0,'msg'=>'','data'=>$rs,'informe'=>$rsInforme,'edad'=>$rsEdad[0], 'dni'=>$rsDNI[0]));
    }
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCreditoCod () { 
    //return $this->db->fetchOne("SELECT K_RET FROM QP_SP_GENERADOR_ANIO_QRY('CREDITO',?,1)",
    //                           array(date('Y')));
    return $this->db->fetchOne("SELECT LPAD(K_RET,12,'0') FROM QP_SP_GENERADOR_QRY('CREDITO',1)");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function updCreditoCod () { 
    $this->db->query("EXECUTE PROCEDURE QP_SP_GENERADOR_UPD('CREDITO',1)");
    //$this->db->query("EXECUTE PROCEDURE QP_SP_GENERADOR_ANIO_UPD('CREDITO',?,1)",
                     //                     array(date('Y')));
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCiiu () { 
    return $this->db->fetchAll("SELECT COD,COD||' - '||NOM AS NOM_LARGO FROM CR_CIIU ORDER BY COD");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function ciiuAction () { 
    if ($this->_request->isXmlHttpRequest()){
      try{
      $rs=$this->db->fetchAll("SELECT COD, NOM FROM CR_CIIU WHERE COD='{$_REQUEST['COD']}'");
      $this->json(array('code'=>0,'msg'=>'','data'=>$rs));
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
  function buscarciiuAction () { 
    $this->view->subTitle="Buscar CIIU";
    $this->view->f=$_REQUEST['f'];
    
    if ($this->_request->isXmlHttpRequest()){
      $rs=$this->db->fetchAll('SELECT K_COD,K_NOM FROM CR_SP_BUSCAR_CIIU_QRY(?,?)',array($_REQUEST['opc'],$_REQUEST['valor']));
      $this->json(array('code'=>0,'msg'=>'','data'=>$rs));
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getAnalistas () { 
    return $this->db->fetchAll("SELECT * FROM CR_VW_LISTA_ANALISTAS");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function estadocreditoAction () { 
    $this->view->subTitle="Estado de Cr�dito";
    if ($this->_request->isXmlHttpRequest()){
      $rsCliente=$this->db->fetchAll("SELECT CL.COD,P.NOM_LARGO FROM CR_CLIENTE CL INNER JOIN QP_PERSONA P ON CL.PERSONA_COD=P.COD WHERE CL.COD=LPAD('{$_REQUEST['COD']}',12,'0')");
      $rsCredito=$this->db->fetchAll("SELECT * FROM CR_SP_CREDITOS_CLIENTE_QRY(?)",array($_REQUEST['COD']));
      $this->json(array('code'=>'0','msg'=>'','data'=>array('cliente'=>$rsCliente[0],'credito'=>$rsCredito)));
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function estadocreditorepAction () { 
        $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
        $r=$this->_request->getParams();
        $this->view->user=$this->identity->USR;

        $this->view->COD=$r['COD'];
        $cr=new Credito();
        $this->view->credito=$cr->getDatosBasicosCredito($r['COD']);
        $this->view->cliente=$cr->getCliente($r['COD']);
        $this->view->analista=$cr->getAnalista($r['COD']);
        $this->view->cobrador=$cr->getCobrador($r['COD']);
        $this->view->linea=$cr->getLinea($r['COD']);
        $this->view->moneda=$cr->getMoneda($r['COD']);
        $this->view->historia=$this->db->fetchAll("select * FROM CR_SP_HISTORIA_CREDITICIA_QRY(?)",array($r['COD']));
        $this->view->estado=($this->db->fetchOne("SELECT EST FROM CR_CREDITO WHERE COD=?",array($r['COD']))=='C'?'Cancelado':'Activo');
        $rs=$this->db->fetchAll("SELECT SUM(K_SAL_CAPITAL) AS SAL_TOTAL, SUM(K_ING_TOTAL) AS ING_TOTAL, SUM(K_ING_CAPITAL) AS ING_TOTAL_CAPITAL, SUM(K_ING_INTERES) AS ING_TOTAL_INTERES, SUM(K_ING_SEGURO) AS ING_TOTAL_SEGURO, SUM(K_ING_OTROS) AS ING_TOTAL_OTROS, SUM(K_ING_MORA) AS ING_TOTAL_MORA, SUM(K_ING_ITF) AS ING_TOTAL_ITF, SUM(K_ING_IGV) AS ING_TOTAL_IGV FROM CR_SP_HISTORIA_CREDITICIA_QRY(?)",array($r['COD']));
        $this->view->totalHistoria=$rs[0];
    //detalle del Estado de Cr�dito
    $rs=$this->db->fetchAll("SELECT * FROM CJ_SP_RECIBO_CREDITO_QRY(?)",array($r['COD']));
    for ($i=0 ; $i<count($rs) ; $i++ ) {
      $rs[$i]->DETALLE=$this->db->fetchAll("SELECT * FROM CJ_SP_RECIBO_DETALLE_QRY(?)",array($rs[$i]->K_COD));
    } //end for
    $this->view->detPago=$rs;

  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function economicoAction () { 
    $this->view->subTitle="Reporte Economico";
    $this->view->lineas=$this->getLineas();
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function economicorepAction () { 
        $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $this->view->subTitle="REPORTE ECONOMICO";
    $r=$this->_request->getParams();
    $this->view->desde=$r['FEC_INI'];
    $this->view->hasta=$r['FEC_TER'];
    $rs=$this->db->fetchAll("SELECT L.COD AS LINEA_COD,L.NOM AS LINEA_NOM, M.NOM AS MONEDA_NOM FROM CR_LINEA L INNER JOIN CR_MONEDA M ON L.MONEDA_COD=M.COD WHERE L.COD=?",array($r['LINEA_COD']));
    $this->view->linea=$rs[0];
    $this->view->user=$this->identity->USR;
    $this->view->lista=$this->db->fetchAll("SELECT * FROM CR_SP_REP_ECONOMICO_QRY(?,?,?)",array($r['LINEA_COD'],$r['FEC_INI'],$r['FEC_TER']));
    $rs=$this->db->fetchAll("SELECT SUM(K_TOTAL) AS TOTAL, SUM(K_CAPITAL) AS CAPITAL, SUM(K_INTERES) AS INTERES, SUM(K_MORA) AS MORA, SUM(K_SEGURO) AS SEGURO, SUM(K_GA) AS GA, SUM(K_OTROS) AS OTROS, SUM(K_IGV) AS IGV, SUM(K_ITF) AS ITF FROM CR_SP_REP_ECONOMICO_QRY(?,?,?)",array($r['LINEA_COD'],$r['FEC_INI'],$r['FEC_TER']));
    $this->view->total=$rs[0];
    

  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function cambiarmoraAction () { 
    $this->view->subTitle='Cambiar Tasa Moratoria';
    if ($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['opc']) {
        case 'get': 
          $rs=$this->db->fetchAll("SELECT COD, TI_MORA,TI_COMPENSA FROM CR_CREDITO WHERE COD=LPAD('{$_REQUEST['COD']}',12,'0')");
          $this->json(array('code'=>'0','msg'=>'','data'=>$rs[0]));
          break;
        case 'upd': 
          $cod=$_REQUEST['COD'];
          $r=$this->clearArray($_REQUEST,array('COD','PHPSESSID','opc'));
          try{
            $this->db->update('CR_CREDITO',$r,"COD=LPAD('$cod',12,'0')");
            $this->json(array('code'=>'0','msg'=>'Registro modificado'));
          }
          catch(Exception $e){
            $this->json(array('code'=>'1','msg'=>$e->getMessage()));
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
  function redefinircuotaAction () { 
    $this->view->subTitle='Redifinir Cuota';
    if ($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['opc']) {
      case 'get' : 
        $rs=$this->db->fetchAll("SELECT * FROM CR_SP_INFO_CRED_REDEFINIR_QRY(?)",array($_REQUEST['COD']));
        $this->json(array('code'=>'0','msg'=>'','data'=>$rs[0]));
        break;
      case 'ins':
        $this->db->beginTransaction();
        try{
          $cr=new Credito();
          $rs=$this->db->fetchAll("SELECT (CAST('{$_REQUEST['FEC']}' AS DATE)-1) AS FEC,CAST(L.TI_MENSUAL AS DECIMAL(10,4))/100.00 as TI_MENSUAL FROM CR_CREDITO CR INNER JOIN CR_LINEA L ON CR.LINEA_COD=L.COD WHERE CR.COD=?",array($_REQUEST['COD']));
          $tasa=$rs[0]->TI_MENSUAL;
          $fecha=$rs[0]->FEC;
          $otros=$this->db->fetchOne("SELECT L.GA FROM CR_CREDITO CR INNER JOIN CR_LINEA L ON CR.LINEA_COD=L.COD WHERE CR.COD=?",array($_REQUEST['COD']));
          $crono=$cr->cronograma ($_REQUEST['MONTO'],$_REQUEST['PLAZO'], $fecha, $tasa,$otros);          
          $this->db->query("DELETE FROM CR_CUOTA WHERE CREDITO_COD=? AND EST='A'",array($_REQUEST['COD']));
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
                                   number_format($cuota->DIF,2,'.','')
                                   )
                             );
          }          
          $this->db->query("UPDATE CR_CREDITO SET PLAZO=PLAZO-{$_REQUEST['CUOTAS_PENDIENTES']}+{$_REQUEST['PLAZO']} WHERE COD='{$_REQUEST['COD']}'");
          $this->db->commit();
          $this->json(array('code'=>'0','msg'=>'Registro Actualizado'));
        } catch (Exception $e){
          $this->db->rollBack();
          $this->json(array('code'=>'1','msg'=>$e->getMessage()));
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
  function anulardesembolsoAction () { 
    $this->view->subTitle="Anular Desembolso";
    if ($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['op']) {
      case 'get' : 
        $rs=$this->db->fetchAll("SELECT CR.COD,CR.CLIENTE_COD, CL.NOM_LARGO ,CR.MONTO FROM CR_CREDITO CR INNER JOIN CR_CLIENTE CL ON CR.CLIENTE_COD=CL.COD WHERE CR.COD = LPAD('{$_REQUEST['COD']}',12,'0')");
        $this->json(array('code'=>'0','msg'=>'','data'=>$rs[0]));
        break;
      case 'ins' : 
        try{
          $this->db->query("EXECUTE PROCEDURE CR_SP_ANULAR_DESEMBOLSO_RET(?)",array($_REQUEST['COD']));
          $this->json(array('code'=>'0','msg'=>'Desembolso Anulado'));

        } catch (Exception $e){
          $this->json(array('code'=>'0','msg'=>$e->getMessage()));
        }
        break;      
      default: break;

      }
    }
  } //end function
  
} //end class

?>