<?
/**
    * Descripción Corta
     * Descripción Larga
      * @category   
      * @package    
      * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
      * @license    Leer archivo LICENSE
      */
class Asistencia_IndexController extends Quipu_Controller_Action { 
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
  function turnoAction () { 
    $this->view->subTitle="Turno";
    if ($this->_request->isXmlHttpRequest()){
      try{
        $_REQUEST['APLICA_EXTRA']=(isset($_REQUEST['APLICA_EXTRA']))?$_REQUEST['APLICA_EXTRA']:0;
        $_REQUEST['APLICA_TARDANZA']=(isset($_REQUEST['APLICA_TARDANZA']))?$_REQUEST['APLICA_TARDANZA']:0;
        switch ($_REQUEST['op']) {
        case 'get' : 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT ID,NOM,APLICA_TARDANZA,APLICA_EXTRA FROM AS_TURNO WHERE ID=?",array($_REQUEST['idx']));
          else
            $rs=$this->getTurno();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));

          break;
        
        case 'ins' : 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->insert("AS_TURNO",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getTurno()));

          break;
        case 'upd' : 
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','op'));
          $this->db->update('AS_TURNO',$r,"ID= '$idx'");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getTurno()));

          break;
        case 'del' :
          $rs=$this->db->delete('AS_TURNO',"ID='{$_REQUEST['idx']}'");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getTurno()));        
          break;
        default: break;
        } //end switch
      }
      catch(Exception $e){
        $this->json(array('code'=>1,'msg'=>$e->getMessage()));
      }  
    }
  } //end functionb
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function horarioAction () { 
    $this->view->subTitle="Horario de Trabajo";
    $this->view->dias=$this->getDias();
    $this->view->turnos=$this->getTurno();
    $this->view->tipoEmpleados=$this->getTipoEmpleado();

    if ($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['op']) {
      case 'ins' : 
        $this->db->beginTransaction();
        try{
          //insertamos le horario general          
          $this->db->query('EXECUTE PROCEDURE AS_SP_HGENERAL_INS(?,?,?,?)',
                           array($_REQUEST['COD'],
                                 $_REQUEST['TIPO_EMPLEADO_COD'],
                                 $_REQUEST['FEC_INI'],
                                 $_REQUEST['FEC_FIN'],
                                 ));
          //Insertamos los horarios de los turnos
          foreach($this->view->turnos as $turno){          
            foreach($this->view->dias as $dia){
              if (($_REQUEST['H_'.$turno->ID.'_'.$dia['ID'].'_I']!='0:00:00') &&
                  ($_REQUEST['H_'.$turno->ID.'_'.$dia['ID'].'_I']!='0:00:00')){
                $this->db->query('EXECUTE PROCEDURE AS_SP_HGENERAL_TE_INS(?,?,?,?,?)',
                                 array($_REQUEST['COD'],
                                       $turno->ID,
                                       $dia['ID'],
                                       $_REQUEST['H_'.$turno->ID.'_'.$dia['ID'].'_I'],
                                       //'00:00',
                                       $_REQUEST['H_'.$turno->ID.'_'.$dia['ID'].'_S']
                                       ));
              }
            }
          }
          $rs=$this->db->fetchAll('SELECT HG.COD, TE.NOM, HG.FEC_INI, HG.FEC_FIN FROM AS_HGENERAL HG INNER JOIN QP_TIPO_EMPLEADO TE ON HG.TIPO_EMPLEADO_COD=TE.COD');
          $this->db->commit();
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$rs));
        }catch(Exception $e){
          $this->json(array('code'=>1,'msg'=>$e->getMessage()));
        }

        break;
      case 'get': 
        $hg=$this->db->fetchAll("SELECT HG.COD, HG.TIPO_EMPLEADO_COD, HG.FEC_INI, HG.FEC_FIN FROM AS_HGENERAL HG WHERE HG.COD=?",array($_REQUEST['COD']));
        $hgdt=$this->db->fetchAll("SELECT DISTINCT(TURNO_ID),DIA,HOR_ING,HOR_SAL FROM AS_HORARIO WHERE HGENERAL_COD=?",array($_REQUEST['COD']));
        for($i=0;$i<count($hgdt);$i++){
          $hgd['H_'.$hgdt[$i]->TURNO_ID.'_'.$hgdt[$i]->DIA.'_I']=$hgdt[$i]->HOR_ING;
          $hgd['H_'.$hgdt[$i]->TURNO_ID.'_'.$hgdt[$i]->DIA.'_S']=$hgdt[$i]->HOR_SAL;
          //unset($hgd[$i]);
        }
        $this->json(array('code'=>0,'msg'=>'','data'=>$hg[0],'det'=>$hgd));
        break;
      case 'getlista' : 
          
        $rs=$this->db->fetchAll('SELECT HG.COD, TE.NOM, HG.FEC_INI, HG.FEC_FIN FROM AS_HGENERAL HG INNER JOIN QP_TIPO_EMPLEADO TE ON HG.TIPO_EMPLEADO_COD=TE.COD');
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
  function horarioespecialAction () { 
    $this->view->subTitle="Horario Especial de Trabajo";
    $this->view->dias=$this->getDias();
    $this->view->turnos=$this->getTurno();
    $this->view->tipoEmpleados=$this->getTipoEmpleado();
    if ($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['op']) {
      case 'ins' : 
        $this->db->beginTransaction();
        try{
          //Insertamos los horarios de los turnos
          foreach($this->view->turnos as $turno){          
            if (($_REQUEST['H_'.$turno->ID.'_I']!='0:00:00') &&
                ($_REQUEST['H_'.$turno->ID.'_I']!='0:00:00')){
              $this->db->query('EXECUTE PROCEDURE AS_SP_HESPECIAL_INS(?,?,?,?,?)',
                               array($_REQUEST['EMPLEADO_COD'],
                                     $turno->ID,
                                     $_REQUEST['FEC_INI'],
                                     $_REQUEST['H_'.$turno->ID.'_I'],
                                     //'00:00',
                                     $_REQUEST['H_'.$turno->ID.'_S']
                                     ));
            }
          }
          $rs=$this->getHorarioEspecial();
          $this->db->commit();
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$rs));
        }catch(Exception $e){
          $this->json(array('code'=>1,'msg'=>$e->getMessage()));
        }

        break;
      case 'get': 
        $he=$this->db->fetchAll("SELECT FIRST 1 DISTINCT(H.FEC_INI),H.EMPLEADO_COD,P.NOM_LARGO AS EMPLEADO_NOM FROM AS_HORARIO H INNER JOIN QP_EMPLEADO E ON H.EMPLEADO_COD=E.COD INNER JOIN QP_PERSONA P ON E.COD=P.COD WHERE H.TIP='E' AND H.EMPLEADO_COD=? AND H.FEC_INI=?",array($_REQUEST['COD'],$_REQUEST['FEC']));
        $hedt=$this->db->fetchAll("SELECT DISTINCT(TURNO_ID),HOR_ING,HOR_SAL FROM AS_HORARIO WHERE EMPLEADO_COD=? AND FEC_INI=? AND TIP='E'",array($_REQUEST['COD'],$_REQUEST['FEC']));
        for($i=0;$i<count($hedt);$i++){
          $hed['H_'.$hedt[$i]->TURNO_ID.'_I']=$hedt[$i]->HOR_ING;
          $hed['H_'.$hedt[$i]->TURNO_ID.'_S']=$hedt[$i]->HOR_SAL;
          //unset($hgd[$i]);
        }
        $this->json(array('code'=>0,'msg'=>'','data'=>$he[0],'det'=>$hed));
        break;
      case 'getlista' : 
        $rs=$this->getHorarioEspecial();
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
  function getHorarioEspecial () { 
    return $this->db->fetchAll("SELECT DISTINCT(H.FEC_INI),H.EMPLEADO_COD,P.NOM_LARGO FROM AS_HORARIO H INNER JOIN QP_EMPLEADO E ON H.EMPLEADO_COD=E.COD INNER JOIN QP_PERSONA P ON E.COD=P.COD WHERE H.TIP='E' ORDER BY H.FEC_INI");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function marcarAction () { 
    $this->view->subTitle="Marcar Asistencia";
    if ($this->_request->isXMLHttpRequest()){
      switch ($_REQUEST['op']) {
      case 'ins' : 
        try{
          $_REQUEST['PERSONA_COD']=(isset($_REQUEST['PERSONA_COD']))?$_REQUEST['PERSONA_COD']:$this->identity->COD;
          $this->db->query("EXECUTE PROCEDURE AS_SP_MARCA_ASISTENCIA_RET(?,?,?,?)",
                           array($_REQUEST['PERSONA_COD'],
                                 $_REQUEST['FEC'],
                                 $_REQUEST['HOR'],
                                 $_REQUEST['PERMISO']));
          
          $this->json(array('code'=>'0','msg'=>'Registro Guardado','data'=>$this->getAsistencia(date('d.m.Y'))));
        } catch (Exception $e){
          $this->json(array('code'=>'1','msg'=>$e->getMessage()));
        }
        break;
      case 'get' : 
        $rs=$this->getAsistencia(date('d.m.Y'));
        $this->json(array('code'=>'0','msg'=>'','data'=>$rs));
        break;
      case 'persona' : 
        $rs=$this->db->fetchAll("SELECT * FROM AS_SP_CAPTURAR_PERSONA_QRY(?,?)",array($_REQUEST['COD'],$_REQUEST['FEC']));
        $this->json(array('code'=>'0','msg'=>'','data'=>$rs[0]));
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
  function buscarempleadoAction () { 
    $this->view->subTitle="Buscar Empleado";
    $this->view->f=$_REQUEST['f'];
    if ($this->_request->isXMLHttpRequest()) {
      $rs=$this->db->fetchAll("SELECT * FROM QP_SP_BUSCAR_EMPLEADO_QRY(?,?)",array($_REQUEST['opc'],$_REQUEST['valor']));
      $this->json(array('code'=>0,'msg','data'=>$rs));
    } 
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function datosclienteAction() { 
    if ($this->_request->isXMLHttpRequest()){
      $rs=$this->db->fetchAll(sprintf("SELECT COD,NOM_LARGO FROM QP_PERSONA WHERE COD=LPAD('%s',12,'0')",
                                      $_REQUEST['COD']));
      $this->json(array('code'=>0,'msg'=>'','data'=>$rs[0]));
    }
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getAsistencia ($fec) { 
    return $this->db->fetchAll('SELECT * FROM AS_SP_LISTA_ASISTENCIA_QRY(?)',$fec);
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getTurno () { 
    return $this->db->fetchAll("SELECT ID, NOM FROM AS_TURNO WHERE ID>0 ORDER BY ID ");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getDias () { 
    return array( array('ID'=>1,'VALUE'=>'LUNES'),
                  array('ID'=>2,'VALUE'=>'MARTES'),
                  array('ID'=>3,'VALUE'=>'MIERCOLES'),
                  array('ID'=>4,'VALUE'=>'JUEVES'),
                  array('ID'=>5,'VALUE'=>'VIERNES'),
                  array('ID'=>6,'VALUE'=>'SABADO'),
                  array('ID'=>7,'VALUE'=>'DOMINGO')
                  );
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getTipoEmpleado () { 
    return $this->db->fetchAll("SELECT COD, NOM FROM QP_TIPO_EMPLEADO ORDER BY NOM");
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function vacacionesAction () { 
    $this->view->subTitle="Vacaciones";
    $this->view->cod=$this->db->fetchOne("SELECT LPAD(GEN_ID(AS_GN_VACACIONES,0)+1,12,'0') FROM RDB\$DATABASE");
    if ($this->_request->isXMLHttpRequest()){
      try{
        switch ($_REQUEST['op']) {
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','EMPLEADO_NOM','op'));
          $this->db->insert("AS_VACACIONES",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getVacaciones()));
          break;
        case 'upd' :
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','EMPLEADO_NOM','op'));
          $this->db->update('AS_VACACIONES',$r,"ID= $idx");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getVacaciones()));
          break;
        case 'del' : 
          $rs=$this->db->delete('AS_VACACIONES',"ID={$_REQUEST['idx']}");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getVacaciones()));        
          break;
        case 'get' : 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT LPAD(V.ID,12,'0') AS ID,V.EMPLEADO_COD,P.NOM_LARGO AS EMPLEADO_NOM,V.ANIO,V.DES,V.HAS FROM AS_VACACIONES V INNER JOIN QP_PERSONA P ON V.EMPLEADO_COD=P.COD WHERE V.ID=?",array($_REQUEST['idx']));
          else
            $rs=$this->getVacaciones();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));

          break;
        case 'getid' : 
              $cod=$this->db->fetchOne("SELECT LPAD(GEN_ID(AS_GN_VACACIONES,0)+1,12,'0') FROM RDB\$DATABASE");
              $this->json(array('code'=>0, 'msg'=>'','data'=> $cod));
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
  function getVacaciones () { 
    return $this->db->fetchAll("SELECT LPAD(V.ID,12,'0'), E.NOM_LARGO, V.ANIO FROM AS_VACACIONES V INNER JOIN QP_PERSONA E ON V.EMPLEADO_COD=E.COD ORDER BY V.ID");    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function permisodiaAction () { 
    $this->view->subTitle="Permiso de Día";
    $this->view->cod=$this->db->fetchOne("SELECT LPAD(GEN_ID(AS_GN_PERMISO_DIA,0)+1,12,'0') FROM RDB\$DATABASE");
    if ($this->_request->isXMLHttpRequest()){
      try{
        switch ($_REQUEST['op']) {
        case 'ins': 
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','EMPLEADO_NOM','op'));
          $this->db->insert("AS_PERMISO_DIA",$r);
          $this->json(array('code'=>0,'msg'=>'Registro Guardado','data'=>$this->getPermisoDia()));
          break;
        case 'upd' :
          $idx=$_REQUEST['idx'];
          $r=$this->clearArray($_REQUEST,array('idx','PHPSESSID','EMPLEADO_NOM','op'));
          $this->db->update('AS_PERMISO_DIA',$r,"ID= $idx");
          $this->json(array('code'=>0,'msg'=>'Registro Modificado','data'=>$this->getPermisoDia()));
          break;
        case 'del' : 
          $rs=$this->db->delete('AS_PERMISO_DIA',"ID={$_REQUEST['idx']}");
          $this->json(array('code'=>0, 'msg'=>'Registro Borrado', 'data'=>$this->getPermisoDia()));        
          break;
        case 'get' : 
          if (isset($_REQUEST['idx']))
            $rs=$this->db->fetchAll("SELECT LPAD(V.ID,12,'0') AS ID,V.EMPLEADO_COD,P.NOM_LARGO AS EMPLEADO_NOM,V.FEC,V.HOR_DESDE,V.HOR_HASTA,V.MOT FROM AS_PERMISO_DIA V INNER JOIN QP_PERSONA P ON V.EMPLEADO_COD=P.COD WHERE V.ID=?",array($_REQUEST['idx']));
          else
            $rs=$this->getPermisoDia();
          $this->json(array('code'=>0, 'msg'=>'','data'=> $rs));

          break;
        case 'getid' : 
              $cod=$this->db->fetchOne("SELECT LPAD(GEN_ID(AS_GN_PERMISO_DIA,0)+1,12,'0') FROM RDB\$DATABASE");
              $this->json(array('code'=>0, 'msg'=>'','data'=> $cod));
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
  function getPermisoDia () { 
    return $this->db->fetchAll("SELECT LPAD(V.ID,12,'0'), E.NOM_LARGO, V.FEC FROM AS_PERMISO_DIA V INNER JOIN QP_PERSONA E ON V.EMPLEADO_COD=E.COD ORDER BY V.ID");    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function relojAction () { 
    $this->json(array('code'=>'0','msg'=>'','data'=>date('H:i:s')));
  } //end function
  
} //end class

?>