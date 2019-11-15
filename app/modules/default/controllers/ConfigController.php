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
class ConfigController extends  Quipu_Controller_Action{ 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function empresaAction () { 
    $this->view->subTitle="Configurar Empresa";
    if ($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          try{
            $rs=$this->db->fetchAll('SELECT FIRST 1 * FROM QP_EMPRESA');
            $this->_helper->json(array('code'=>0, 'msg'=>'', 'data'=>$rs));
          }
          catch(Exception $e){
            $this->json(array('code'=>1, 'msg'=>$e->getMessage()));
          }
          break;
        case 'ins': 
          unset($_REQUEST['PHPSESSID']);
          unset($_REQUEST['op']);
          $this->db->query("EXECUTE PROCEDURE QP_SP_EMPRESA_INS(?,?,?,?,?,?)",
                           array($_REQUEST['COD'],
                                 $_REQUEST['NOM'],
                                 $_REQUEST['DIRE'],
                                 $_REQUEST['FONO1'],
                                 $_REQUEST['FONO2'],
                                 $_REQUEST['FEC_INI']));
          $this->db->query('UPDATE QP_EMPRESA SET INFOCORP_COD=?',array($_REQUEST['INFOCORP_COD']));
          $this->_helper->json(array('code'=>0, 'msg'=>'Registro Guardado'));
          break;
        }
      }
      catch(Exception $e){
        $this->_helper->json(array('code'=>1, 'msg'=>$e->getMessage()));
      }
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getAgencias () { 
    return $this->db->fetchAll("SELECT COD,NOM FROM QP_AGENCIA ORDER BY COD");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function agenciaAction () { 
    $this->view->subTitle="Agencia";
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM,DIRE,FONO1,FONO2 FROM QP_AGENCIA WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getAgencias();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $rs=$this->db->insert("QP_AGENCIA",$r);
          $agencias=$this->getAgencias();
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$agencias));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('QP_AGENCIA',$r,"COD= '$idx'");
          $agencias=$this->getAgencias();
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$agencias));
          break;
        case 'del':
          $rs=$this->db->delete('QP_AGENCIA',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getAgencias()));        
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
  function localidad () { 
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getEmpleado () { 
    $rs=$this->db->fetchAll("SELECT P.COD, P.NOM_LARGO FROM QP_EMPLEADO E INNER JOIN QP_PERSONA P ON E.COD=P.COD WHERE P.COD<>'000000000000' ORDER BY P.COD");
    return $rs;
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getTipoEmpleado () { 
    $rs=$this->db->fetchAll("SELECT COD, NOM FROM QP_TIPO_EMPLEADO ORDER BY NOM");
    return $rs;
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getRoles () { 
    $rs=$this->db->fetchAll("SELECT COD, NOM FROM SG_ROL ORDER BY NOM");
    return $rs;
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getEmpleadoCod () { 
    return $this->db->fetchOne("SELECT LPAD(K_RET,12,'0') FROM QP_SP_GENERADOR_QRY('EMPLEADO',1)");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function updEmpleadoCod () { 
    $this->db->query("EXECUTE PROCEDURE QP_SP_GENERADOR_UPD('EMPLEADO',1)");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function empleadoAction () { 
    $this->view->subTitle="Empleado";
    $this->view->tipoEmpleado=$this->getTipoEmpleado();
    $this->view->agencias=$this->getAgencias();
    $this->view->roles=$this->getRoles();
    $this->view->cod=$this->getEmpleadoCod();
    if ($this->_request->isXmlHttpRequest()) {
      try{
        switch ($_REQUEST['op']) {
        case 'get':
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT E.COD,NOM,APE_PAT, APE_MAT, DIRE, FONO1, DNI,TIPO_EMPLEADO_COD, AGENCIA_COD, FEC_INI, FEC_TER, USR, ROL  FROM QP_PERSONA P INNER JOIN QP_EMPLEADO E ON P.COD=E.COD LEFT JOIN SG_USUARIO U ON E.COD=U.COD WHERE P.COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getEmpleado();
          $this->json(array('code'=>0,'msg'=>'','data'=>$rs));
          break;
        case 'ins':
          $this->db->beginTransaction();
          try{
            $_REQUEST['COD']=$this->getEmpleadoCod();
            $this->db->insert('QP_PERSONA',array(
                                                 'COD'=>$_REQUEST['COD'],
                                                 'NOM'=>$_REQUEST['NOM'],
                                                 'APE_PAT'=>$_REQUEST['APE_PAT'],
                                                 'APE_MAT'=>$_REQUEST['APE_MAT'],
                                                 'DIRE'=>$_REQUEST['DIRE'],
                                                 'FONO1'=>$_REQUEST['FONO1'],
                                                 'DNI'=>$_REQUEST['DNI']
                                                 ));
            $this->db->insert('QP_EMPLEADO',array(
                                                  'COD'=>$_REQUEST['COD'],
                                                  'TIPO_EMPLEADO_COD'=>$_REQUEST['TIPO_EMPLEADO_COD'],
                                                  'FEC_INI'=>$_REQUEST['FEC_INI'],
                                                  'FEC_TER'=>$_REQUEST['FEC_TER'],
                                                  'AGENCIA_COD'=>$_REQUEST['AGENCIA_COD']
                                                 ));
            $this->db->query("EXECUTE PROCEDURE SG_SP_USUARIO_INS(?,?,?,?)",
                             array($_REQUEST['COD'], 
                                   $_REQUEST['USR'],
                                   md5($_REQUEST['USR'].$_REQUEST['USR']), 
                                   $_REQUEST['ROL']));
            $rs=$this->getEmpleado();
            $this->updEmpleadoCod();
            $this->db->commit();
            $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$rs));
          }
          catch(Exception $e){
            $this->json(array('code'=>1,'msg'=>$e->getMessage()));
            $this->db->rollBack();
          }
          break;
        case 'upd':
          $this->db->beginTransaction();
          $idx=$_REQUEST['idx'];
          try{
            $this->db->update('QP_PERSONA',array(
                                                 'COD'=>$_REQUEST['COD'],
                                                 'NOM'=>$_REQUEST['NOM'],
                                                 'APE_PAT'=>$_REQUEST['APE_PAT'],
                                                 'APE_MAT'=>$_REQUEST['APE_MAT'],
                                                 'DIRE'=>$_REQUEST['DIRE'],
                                                 'FONO1'=>$_REQUEST['FONO1'],
                                                 'DNI'=>$_REQUEST['DNI']
                                                 ),"COD='$idx'");
            $this->db->update('QP_EMPLEADO',array(
                                                  'COD'=>$_REQUEST['COD'],
                                                  'TIPO_EMPLEADO_COD'=>$_REQUEST['TIPO_EMPLEADO_COD'],
                                                  'FEC_INI'=>$_REQUEST['FEC_INI'],
                                                  'FEC_TER'=>$_REQUEST['FEC_TER'],
                                                  'AGENCIA_COD'=>$_REQUEST['AGENCIA_COD']
                                                 ),"COD='{$_REQUEST['COD']}'");            
            $this->db->query("EXECUTE PROCEDURE SG_SP_USUARIO_INS(?,?,?,?)",
                             array($_REQUEST['COD'], 
                                   $_REQUEST['USR'],
                                   md5($_REQUEST['USR'].$_REQUEST['USR']), 
                                   $_REQUEST['ROL']));
            $rs=$this->getEmpleado();
            $this->db->commit();
            $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$rs));
          }
          catch(Exception $e){
            $this->json(array('code'=>1,'msg'=>$e->getMessage()));
            $this->db->rollBack();
          }

          break;
        case 'del':
          $rs=$this->db->delete('QP_PERSONA',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getEmpleado()));
          break;
        case 'cod':
          $this->json(array('code'=>0,'msg'=>'','data'=>$this->getEmpleadoCod()));
          break;
        default: break;
        } //end switch
        
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
  function getLocalidad () { 
    return $this->db->fetchAll("SELECT COD,NOM FROM QP_LOCALIDAD ORDER BY COD");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function localidadAction () {
    $this->view->subTitle='Ubigeo';
    if($this->_request->isXmlHttpRequest()){
      try{
        switch($_REQUEST['op']){
        case 'get': 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT COD,NOM FROM QP_LOCALIDAD WHERE COD=?",array($_REQUEST['idx']));
          else
            $rs=$this->getLocalidad();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));
          break;
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("QP_LOCALIDAD",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getLocalidad()));
          break;
        case 'upd':
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('QP_LOCALIDAD',$r,"COD= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getLocalidad()));
          break;
        case 'del':
          $rs=$this->db->delete('QP_LOCALIDAD',"COD='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getLocalidad()));        
          break;
        }
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }
    }    
  } //end function
} //end class

?>