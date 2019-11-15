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
class Credito_ClienteController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    $this->view->subTitle="Nuevo Cliente";
    
    
    if ($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'ins':
          $this->db->beginTransaction();
          try{
            $_REQUEST['COD']=$this->getClienteCod();
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
            $this->updClienteCod();
            $nuevoCodigo=$this->getClienteCod();
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
                                      'ESTADO_CIVIL'=>$_REQUEST['ESTADO_CIVIL'],
                                      'CONYUGE'=>($_REQUEST['CONYUGE']!='')?$_REQUEST['CONYUGE']:null,
                                      'DIR_NEGOCIO'=>$_REQUEST['DIR_NEGOCIO'],
                                      'LOCALIDAD_NEGOCIO'=>$_REQUEST['LOCALIDAD_NEGOCIO'],
                                      'ACTIVIDAD_COD'=>$_REQUEST['ACTIVIDAD_COD']
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
                                      'SIGLAS'=>$_REQUEST['SIGLAS'],
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
            $nuevoCodigo=$this->getClienteCod();
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
    $this->view->actividades=$this->getActividades();
    $this->view->cod=$this->getClienteCod();
    $this->view->tipoDocumentos=$this->getTipoDoc();
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
  function getClienteCod () { 
    return $this->db->fetchOne("SELECT LPAD(K_RET,12,'0') FROM QP_SP_GENERADOR_QRY('CLIENTE',1)");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function updClienteCod(){
    $this->db->query("EXECUTE PROCEDURE QP_SP_GENERADOR_UPD('CLIENTE',1)");
  }
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getTipoDoc () { 
    return $this->db->fetchAll('SELECT COD, NOM FROM QP_TIPO_DOC');
  } //end function
  
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
  function buscarAction () { 
    $this->view->subTitle="Buscar Cliente";
    $this->view->f=$_REQUEST['f'];
    
    if ($this->_request->isXmlHttpRequest()){
      $rs=$this->db->fetchAll('SELECT * FROM CR_SP_BUSCAR_CLIENTE_QRY(?,?)',array($_REQUEST['opc'],$_REQUEST['valor']));
      $this->json(array('code'=>0,'msg'=>'','data'=>$rs));
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function datosAction () { 
    if ($this->_request->isXmlHttpRequest()){
      $rs=$this->db->fetchAll("SELECT * FROM CR_SP_CLIENTE_QRY(?)",array($_REQUEST['COD']));
      $this->json(array('code'=>0,'msg'=>'','data'=>$rs[0]));
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function conyugeAction () { 
    if ($this->_request->isXmlHttpRequest()){
      $rs=$this->db->fetchAll("SELECT COD,NOM_LARGO FROM QP_PERSONA WHERE COD=?",array($_REQUEST['COD']));
      $this->json(array('code'=>0,'msg'=>'','data'=>$rs[0]));
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function tarifaAction () { 
    $this->view->subTitle="Tarifa";
    if ($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'ins': 
          $this->db->query("EXECUTE PROCEDURE CR_SP_TARIFA_INS(?,?,?,?)",
                           array($_REQUEST['COD'],
                                 $_REQUEST['TI_MENSUAL'],
                                 $_REQUEST['TI_MORA'],
                                 $_REQUEST['TI_COMPENSA']
                                 )
                           );
          $this->json(array('code'=>0,'msg'=>'Registro Guardado'));
          break;
        case 'get' : 
          $rs=$this->db->fetchAll("SELECT * FROM CR_VW_TARIFA WHERE COD=LPAD('{$_REQUEST['COD']}',12,'0')");
          if(count($rs)==0)
            $this->json(array('code'=>1,'msg'=>'El Usuario no Existe'));
            else
          $this->json(array('code'=>0,'msg'=>'','data'=>$rs[0]));
          break;
        }        
      } catch (Exception $e){        
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }      
    }
  } //end function
  
} //end class

?>