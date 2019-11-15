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
class Credito extends Quipu_Db_Table { 
  protected $_name='CR_CREDITO';
  protected $_primary='COD';
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function _cronograma ($monto, $plazo, $fecha, $tasa, $igv, $itf, $renta, $seguro,$otros) { 
    $cuotas=$this->_db->fetchAll("SELECT K_FEC AS FECHA, K_CAPITAL AS CAPITAL, K_SALDO_CAPITAL AS SALDO_CAPITAL, K_CUOTA AS CUOTA, K_INTERES AS INTERES, K_SEGURO AS SEGURO, K_ITF AS ITF, K_IGV AS IGV, K_GA AS GA, K_TOTAL AS TOTAL, K_TOTAL_ITF AS TOTAL_ITF, K_DIA AS DIA,K_DIF AS DIF FROM CR_SP_CRONOGRAMA_DIARIO_QRY(?,?,?,?,?,?,?,?,?)",array($monto,$plazo,$fecha,$tasa, $igv, $itf, $renta,$seguro,$otros));
    //    $sumaTotal=$this->_db->fetchAll("SELECT SUM(K_INTERES) AS SUMA_INTERES, SUM(K_CAPITAL) AS SUMA_CAPITAL, SUM(K_SEGURO) AS SUMA_SEGURO, SUM(K_ITF) AS SUMA_ITF, SUM(K_IGV) AS SUMA_IMPUESTO, SUM(K_OTRO) AS SUMA_OTRO, SUM(K_TOTAL) AS SUMA_TOTAL FROM CR_SP_CRONOGRAMA_DIARIO_QRY(?,?,?,?,?,?,?,?,?)",array($monto,$plazo,$fecha,$tasa, $igv, $itf, $renta,$seguro,$otros));
    $sumaTotal=$this->_db->fetchAll("SELECT SUM(K_INTERES) AS SUMA_INTERES, CAST(SUM(K_CAPITAL) AS DECIMAL(10,0))  AS SUMA_CAPITAL, SUM(K_SEGURO) AS SUMA_SEGURO, SUM(K_ITF) AS SUMA_ITF, SUM(K_IGV) AS SUMA_IMPUESTO, SUM(K_GA) AS SUMA_GA, SUM(K_TOTAL) AS SUMA_TOTAL, SUM(K_TOTAL_ITF) AS SUMA_TOTAL_ITF FROM CR_SP_CRONOGRAMA_DIARIO_QRY(?,?,?,?,?,?,?,?,?)",array($monto,$plazo,$fecha,$tasa, $igv, $itf, $renta,$seguro,$otros));

    /*    $promedioCuota=($sumaTotal[0]->SUMA_TOTAL_ITF)/($plazo*1.000);
    $promedioCuotaRedondeado=substr($promedioCuota,0, strpos($promedioCuota,'.')+2);
    $promedioCuotaFinal=$sumaTotal[0]->SUMA_TOTAL_ITF-($plazo-1)*$promedioCuotaRedondeado;
    for ($i=0;$i<count($cuotas);$i++){
      if ($i==count($cuotas)-1){
        //$cuotas[$i]->TOTAL=$promedioCuotaFinal;
        $cuotas[$i]->TOTAL_ITF=$promedioCuotaFinal;
        //$cuotas[$i]->ITF=$cuotas[$i]->TOTAL*$itf;
        //$cuotas[$i]->TOTAL_ITF=$cuotas[$i]->TOTAL+$cuotas[$i]->ITF;
      }
      else{
        //$cuotas[$i]->TOTAL=$promedioCuotaRedondeado;
        $cuotas[$i]->TOTAL_ITF=$promedioCuotaRedondeado;
        //$cuotas[$i]->ITF=$cuotas[$i]->TOTAL*$itf;
        //$cuotas[$i]->TOTAL_ITF=$cuotas[$i]->TOTAL+$cuotas[$i]->ITF;
      }
      }
    */
    return array('cuotas'=>$cuotas,'total'=>$sumaTotal[0]);    
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function cronograma ($monto,$plazo, $fecha, $tasa,$otros=0.00) { 
    $impuestos=$this->getImpuestos();
    $igv=$impuestos->IGV_DEC;
    $itf=$impuestos->ITF_DEC;
    $renta=$impuestos->RENTA_DEC;
    $seguro=$impuestos->SEGURO_DEC;
    return $this->_cronograma($monto, $plazo, $fecha, $tasa, $igv, $itf, $renta, $seguro,$otros);
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function cronogramaCredito ($cod) { 
    $cuotas=$this->_db->fetchOne("SELECT COUNT(*) FROM CR_CUOTA WHERE CREDITO_COD=?",array($cod));
    if ($cuotas>0){
      $cuotas=$this->_db->fetchAll("SELECT K_FEC AS FECHA, K_CAPITAL AS CAPITAL, K_SALDO_CAPITAL AS SALDO_CAPITAL, K_CUOTA AS CUOTA, K_INTERES AS INTERES, K_SEGURO AS SEGURO, K_ITF AS ITF, K_IGV AS IGV, K_GA AS GA, K_TOTAL AS TOTAL, K_TOTAL_ITF AS TOTAL_ITF, K_DIA AS DIA, K_TOTAL_CONS AS TOTAL_CONS FROM CR_SP_CRONOGRAMA_CREDITO_QRY(?)",array($cod));
      $sumaTotal=$this->_db->fetchAll("SELECT SUM(K_INTERES) AS SUMA_INTERES, CAST(SUM(K_CAPITAL) AS DECIMAL(10,0))  AS SUMA_CAPITAL, SUM(K_SEGURO) AS SUMA_SEGURO, SUM(K_ITF) AS SUMA_ITF, SUM(K_IGV) AS SUMA_IMPUESTO, SUM(K_GA) AS SUMA_GA, SUM(K_TOTAL) AS SUMA_TOTAL, SUM(K_TOTAL_ITF) AS SUMA_TOTAL_ITF, SUM(K_TOTAL_CONS) AS SUMA_TOTAL_CONS FROM CR_SP_CRONOGRAMA_CREDITO_QRY(?)",array($cod));
      return array('cuotas'=>$cuotas,'total'=>$sumaTotal[0]);    
    }
    else{
      $rs=$this->getDatosBasicosCredito($cod);
      return $this->_cronograma($rs->MONTO, 
                                $rs->PLAZO,
                                $rs->DESEMBOLSO_FEC,
                                $rs->TI_MENSUAL,
                                $rs->IGV_DEC,
                                $rs->ITF_DEC,
                                $rs->RENTA_DEC,
                                $rs->SEGURO_DEC,
                                $rs->GA
                                );
    }
  } //end function
  /**
   * Datos generales del Credito
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getDatosBasicosCredito ($cod) { 
    $rs=$this->_db->fetchAll("SELECT CR.MONTO, CR.PLAZO, CR.FEC,CR.DESEMBOLSO_FEC, CR.FEC_FIN, CR.DESEMBOLSO_FEC, cast(CR.TI_MENSUAL as decimal(10,4))/100.00 AS TI_MENSUAL, IM.IGV_DEC, IM.ITF_DEC, IM.RENTA_DEC, IM.SEGURO_DEC, L.GA, CR.LINEA_COD, CR.MODALIDAD_COD, CR.CLIENTE_COD, CR.OBS FROM CR_CREDITO CR INNER JOIN CN_IMPUESTO IM ON CR.IMPUESTO_ID=IM.ID INNER JOIN CR_LINEA L ON CR.LINEA_COD=L.COD WHERE CR.COD=?",array($cod));
    return $rs[0];
  } //end function
  /**
   * Extrae el analista de un Crédito
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getAnalista($cod){
    $rs= $this->_db->fetchAll("SELECT * FROM CR_SP_ANALISTA_QRY(?)",array($cod));
    return $rs[0];
  }
  /**
   * Extrae el cobrador de un Crédito
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCobrador($cod){
    $rs= $this->_db->fetchAll("SELECT * FROM CR_SP_COBRADOR_QRY(?)",array($cod));
    return $rs[0];
  }
  /**
   * Extrae el monto a desembolsar
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getMontoDesembolso ($monto, $itf=null) { 
    if ($itf==null){
      $impuestos=$this->getImpuestos();
      $itf=$impuestos->ITF_DEC;
    }
    $montoDesembolso=$monto*(1.00-$itf);
    return array('MONTO'=>$monto,'ITF'=>$monto*$itf,'DESEMBOLSO'=>$montoDesembolso);
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getMontoDesembolsoCredito ($credito) { 
    $rsCredito=$this->_db->fetchAll("SELECT MONTO, IMPUESTO_ID FROM CR_CREDITO WHERE COD=?",array($credito));
    $impuesto=$this->getImpuesto($rsCredito[0]->IMPUESTO_ID);
    return $this->getMontoDesembolso($rsCredito[0]->MONTO,$impuesto[0]->ITF_DEC);
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getImpuesto ($id) { 
    $rs=$this->_db->fetchAll('SELECT * FROM CN_IMPUESTO WHERE ID=?;',array($id));
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getImpuestos () { 
    $rs=$this->_db->fetchAll('SELECT * FROM CN_IMPUESTO WHERE FEC_FIN IS NULL;');
    return $rs[0];
  } //end function
  /**
   * Extrae los datos del Cliente según el Crédito
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCliente ($cod) { 
    //    $rs=$this->_db->fetchAll('SELECT K_COD, K_CLIENTE_COD, K_NOM, K_DOC FROM CR_SP_BUSCAR_CREDITO_QRY(?,?,?)',array(1,$cod,'A'));
    $rs=$this->_db->fetchAll("SELECT CR.COD AS CREDITO_COD, CR.CLIENTE_COD, CL.NOM_LARGO, CASE(CL.TIPO) WHEN 'N' THEN P.DNI WHEN 'J' THEN CL.RUC END AS DOC, CL.DIRE, CL.ESTADO_CIVIL, L.NOM AS LOCALIDAD, CL.FONO1, CL.FONO2, CL.DIR_NEGOCIO, N.NOM AS LOCALIDAD_NEGOCIO, TD.ABREV  AS TIPO_DOCUMENTO FROM CR_CREDITO CR INNER JOIN CR_CLIENTE CL ON CR.CLIENTE_COD=CL.COD INNER JOIN QP_PERSONA P ON CL.PERSONA_COD=P.COD INNER JOIN QP_LOCALIDAD L ON CL.LOCALIDAD_COD= L.COD LEFT JOIN QP_LOCALIDAD N ON CL.LOCALIDAD_NEGOCIO=N.COD INNER JOIN QP_TIPO_DOC TD ON P.TIPO_DOC_COD=TD.COD WHERE  CR.COD=?",array($cod));
    return $rs[0];
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCiiu ($cod) { 
    $rs=$this->_db->fetchAll("SELECT C.* FROM CR_CIIU C INNER JOIN CR_CREDITO CR ON CR.CIIU_COD=C.COD WHERE CR.COD=?",array($cod));
    return $rs[0];
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getActividad ($cod) { 
    $rs=$this->_db->fetchAll("SELECT A.* FROM CR_CREDITO CR INNER JOIN CR_CLIENTE CL ON CR.CLIENTE_COD=CL.COD INNER JOIN CR_ACTIVIDAD A ON CL.ACTIVIDAD_COD=A.COD WHERE CR.COD=?",array($cod));
    return $rs[0];
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getAval ($cod) { 
    $rs=$this->_db->fetchAll("SELECT FIRST 1 P.DNI, CL.NOM_LARGO, CL.DIRE, L.NOM AS LOCALIDAD, CL.ESTADO_CIVIL, CL.FONO1, CL.FONO2, TD.ABREV AS TIPO_DOCUMENTO FROM CR_AVAL A INNER JOIN CR_CLIENTE CL ON A.CLIENTE_COD=CL.COD INNER JOIN QP_LOCALIDAD L ON CL.LOCALIDAD_COD=L.COD INNER JOIN QP_PERSONA P ON CL.PERSONA_COD=P.COD INNER JOIN QP_TIPO_DOC TD ON P.TIPO_DOC_COD=TD.COD WHERE A.CREDITO_COD=?",array($cod));
    return $rs[0];
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function  getLinea($cod) { 
    $rs=$this->_db->fetchAll("SELECT L.* FROM CR_CREDITO CR INNER JOIN CR_LINEA L ON CR.LINEA_COD=L.COD WHERE CR.COD=?",array($cod));
    return ($rs[0]);
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getEstadoCredito ($cod,$fec) { 
    $rs=$this->_db->fetchAll("SELECT * FROM CR_SP_ESTADO_CUOTAS_QRY(?,?)",array($cod,$fec));
    return $rs;
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getMoneda ($cod) { 
    $rs=$this->_db->fetchAll("SELECT M.COD, M.NOM, M.ABREV FROM CR_CREDITO CR INNER JOIN CR_LINEA L ON CR.LINEA_COD=L.COD INNER JOIN CR_MONEDA M ON L.MONEDA_COD=M.COD WHERE CR.COD=?",array($cod));    return $rs[0];
  } //end function
  function getSolicitud($cod){
    $rs=$this->_db->fetchAll("SELECT S.*,SB.NOM AS SBS_OPINION FROM CR_SOLICITUD S INNER JOIN SB_OPINION SB ON S.OPINION_SBS=SB.COD WHERE S.CREDITO_COD=?",array($cod));
    return $rs[0];
  }
  function getSolicitudDetalle($cod){
    $rs=$this->_db->fetchAll("SELECT CR.MONTO,CR.PLAZO,CR.FEC_FIN,SD.* FROM CR_SOLICITUD_DETALLE SD INNER JOIN CR_CREDITO CR ON SD.COD=CR.COD WHERE SD.CREDITO_COD=?",array($cod));
    return $rs;
  }
  function getInversion($cod){
    $rs=$this->_db->fetchAll("SELECT D.COD, D.NOM FROM CR_CREDITO CR INNER JOIN CR_DESTINO D ON CR.DESTINO_COD=D.COD WHERE CR.COD=?",array($cod));
    return $rs[0];
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getUltimoPago ($cod) { 
    $rs=$this->_db->fetchAll("SELECT FIRST 1 ID,FEC FROM CR_CUOTA WHERE CREDITO_COD=? AND EST='C' ORDER BY ID DESC",array($cod));
    $rs[0]->FEC=$this->_db->fetchOne("SELECT FIRST 1 R.FEC FROM CR_RECIBO R INNER JOIN CR_RECIBO_CUOTA RC ON R.COD=RC.RECIBO_COD WHERE R.CREDITO_COD=? AND RC.CUOTA_ID=? ORDER BY R.FEC DESC",array($cod,$rs[0]->ID));
    return $rs[0];
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getIntervaloSinPago ($cod) { 
    $rsini=$this->_db->fetchAll("SELECT FIRST 1 ID,FEC FROM CR_CUOTA WHERE CREDITO_COD=? AND EST='A' ORDER BY ID",array($cod));
    $rsfin=$this->_db->fetchAll("SELECT FIRST 1 ID,FEC FROM CR_CUOTA WHERE CREDITO_COD=? AND EST='A' ORDER BY ID DESC",array($cod));
    $rscont=$this->_db->fetchOne("SELECT COUNT(*) FROM CR_CUOTA WHERE CREDITO_COD=? AND EST='A'",array($cod));
    return array('ini'=>$rsini[0],'fin'=>$rsfin[0],'cuotas'=>$rscont);
  } //end function
  
} //end class

?>