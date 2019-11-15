<?
/**
    * Descripción Corta
     * Descripción Larga
      * @category   
      * @package    
      * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
      * @license    Leer archivo LICENSE
      */
class Asistencia_ReporteController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function historialAction () { 
    $this->view->subTitle='Historial de Asistencia';
    $this->view->empleados=$this->db->fetchAll("SELECT E.COD,P.NOM_LARGO FROM QP_EMPLEADO E INNER JOIN QP_PERSONA P ON E.COD=P.COD WHERE E.COD<>'000000000000'ORDER BY COD");
  } //end function
  function fechasAction(){
    $this->view->subTitle='Reporte Mensual';
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function historialrepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $empresa= new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    $this->view->desde=$r['DESDE'];
    $this->view->hasta=$r['HASTA'];
    $rsEmpleado=$this->db->fetchAll("SELECT COD,NOM_LARGO FROM QP_PERSONA WHERE COD=?",array($r['EMPLEADO_COD']));
    $this->view->empleado=$rsEmpleado[0];
    $this->view->turnos=$this->db->fetchAll("SELECT ID,NOM FROM AS_TURNO WHERE ID>0");
    $this->view->base=$this->db->fetchAll("SELECT * FROM AS_SP_HISTORIAL_BASE_QRY(?,?,?)",array($r['EMPLEADO_COD'],$r['DESDE'],$r['HASTA']));
    $this->view->baseTotal=$this->db->fetchAll("SELECT * FROM AS_SP_HISTORIAL_BASE_TOTAL_QRY(?,?,?)",array($r['EMPLEADO_COD'],$r['DESDE'],$r['HASTA']));
    $this->view->baseTotal=$this->view->baseTotal[0];
    //    $this->view->baseturno=array();
    for($i=0;$i<count($this->view->base);$i++){
      $this->view->base[$i]->K_HOR_TRABAJADAS=$this->chora($this->view->base[$i]->K_HOR_TRABAJADAS);
      $this->view->base[$i]->K_HOR_ESPECIAL=$this->chora($this->view->base[$i]->K_HOR_ESPECIAL);
      $this->view->base[$i]->K_HOR_TARDANZA=$this->chora($this->view->base[$i]->K_HOR_TARDANZA);
      $this->view->base[$i]->K_HOR_EXTRAS=$this->chora($this->view->base[$i]->K_HOR_EXTRAS);
      $this->view->base[$i]->K_TURNO=array();
      $t=$this->db->fetchAll("SELECT * FROM AS_SP_HISTORIAL_TURNO_QRY(?,?)",array($r['EMPLEADO_COD'],$this->view->base[$i]->K_FEC));
      foreach($t as $ti){
        $this->view->base[$i]->K_TURNO[$ti->K_TURNO_ID][$ti->K_TURNO_MOV]=$ti->K_HOR;
      }
      $p=$this->db->fetchAll("SELECT K_TURNO_MOV, K_HOR FROM AS_SP_HISTORIAL_PERMISO_QRY(?,?)",array($r['EMPLEADO_COD'],$this->view->base[$i]->K_FEC));
      $this->view->base[$i]->K_PERMISO=$p;
    }
    $this->view->baseTotal->K_HOR_TRABAJADAS=$this->chora($this->view->baseTotal->K_HOR_TRABAJADAS);
    $this->view->baseTotal->K_HOR_ESPECIAL=$this->chora($this->view->baseTotal->K_HOR_ESPECIAL);
    $this->view->baseTotal->K_HOR_TARDANZA=$this->chora($this->view->baseTotal->K_HOR_TARDANZA);
    $this->view->baseTotal->K_HOR_EXTRAS=$this->chora($this->view->baseTotal->K_HOR_EXTRAS);
    
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc.
   */
  function fechasrepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $empresa= new Empresa();
    $this->view->empresa=$empresa->datosBasicos();
    //    $this->view->lista=$this->db->fetchAll("SELECT * FROM AS_SP_REP_FECHAS_QRY(?,?)",array($r['DESDE'],$r['HASTA']));
    $this->view->lista=$this->db->fetchAll("SELECT * FROM AS_SP_HISTORIAL_FECHAS_QRY(?,?)",array($r['DESDE'],$r['HASTA']));

    $this->view->desde=$r['DESDE'];
    $this->view->hasta=$r['HASTA'];
    for($i=0;$i<count($this->view->lista);$i++){
      $this->view->lista[$i]->K_HOR_TRABAJADAS=$this->chora($this->view->lista[$i]->K_HOR_TRABAJADAS);
      $this->view->lista[$i]->K_HOR_ESPECIAL=$this->chora($this->view->lista[$i]->K_HOR_ESPECIAL);
      $this->view->lista[$i]->K_HOR_TARDANZA=$this->chora($this->view->lista[$i]->K_HOR_TARDANZA);
      $this->view->lista[$i]->K_HOR_EXTRAS=$this->chora($this->view->lista[$i]->K_HOR_EXTRAS);
      }
    //    $this->db->fetchAll();
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function chora ($num) { 
    $hor=explode('.',$num/(60*60));
    $hor=$hor[0];
    $min=explode('.',($num % (60*60))/60);
    $min=$min[0];
    $seg=$num % 60;
    //    return str_pad($hor,2,'0',STR_PAD_LEFT).':'.str_pad($min,2,'0',STR_PAD_LEFT).':'.str_pad($seg,2,'0',STR_PAD_LEFT);
    return str_pad($hor,2,'0',STR_PAD_LEFT).':'.str_pad($min,2,'0',STR_PAD_LEFT);
  } //end function
  
  
} //end class

?>