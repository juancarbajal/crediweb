<?php
class IndexController extends Quipu_Controller_Action{
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  /* function init () {  */
  /*   parent::init();         */
  /*   $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml */
  /* } //end function */
  
  function indexAction(){
    if(strpos($_SERVER["HTTP_USER_AGENT"],'Mozilla')!==false){
      $this->_redirect('auth');
      }
  }
  function menuAction(){
    $this->view->subTitle='Menú Principal';    
    $rol=$this->identity->ROL;
    $this->view->menuitems=$this->db->fetchAll("SELECT COD,NOM FROM QP_MENU_ITEM WHERE PADRE_COD IS NULL ORDER BY COD");
    $menu=array();
    foreach($this->view->menuitems as $menuitem){
      $menu[$menuitem->NOM]=$this->db->fetchAll("SELECT MI.COD,MI.NOM,MI.URL,MI.SEP,MR.EST FROM QP_MENU_ITEM MI INNER JOIN QP_MENU_ROL MR ON MR.MENU_ITEM_COD=MI.COD WHERE PADRE_COD=? AND MR.ROL_COD=? AND EST=1 ORDER BY MI.COD",array($menuitem->COD,$rol));
    }
    $this->view->menu=$menu;
  }
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function asignarmenuAction () { 
    $this->view->subTitle='Asignar Roles';    
    $this->view->menuItems=$this->getMenuItems();
    $this->view->roles=$this->getRoles();
    if ($this->_request->isXmlHttpRequest()){
      switch ($_REQUEST['op']) {
      case 'ins' :
        
        try{
          $this->db->beginTransaction();
          $this->db->delete('QP_MENU_ROL');
          foreach($_REQUEST as $key=>$val){
            if (substr($key,0,4)=='MNU_'){
              $key=explode('_',$key);
              $this->db->insert('QP_MENU_ROL',
                                array('MENU_ITEM_COD'=>$key[2],
                                      'ROL_COD'=>$key[1],
                                      'EST'=>$val));
            }
          }
          $this->db->commit();
          $this->json(array('code'=>0,'msg'=>'Registro Guardado'));
        } catch (Exception $e){
          $this->db->rollBack();
          $this->json(array('code'=>1,'msg'=>$e->getMessage()));
        }
        break;
      default: break;
      } //end switch      
    }
    //Extraemos los estados
    $this->view->est=array();
    foreach($this->view->menuItems as $mi){
      foreach($this->view->roles as $rol){
        $this->view->est[$mi->COD][$rol->COD]=$this->db->fetchOne("SELECT EST FROM QP_MENU_ROL WHERE MENU_ITEM_COD=? AND ROL_COD=?",
                                                                  array($mi->COD,$rol->COD));
      }
    }
  } //end fdunction
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getRoles () { 
    return $this->db->fetchAll("SELECT COD,NOM FROM SG_ROL ORDER BY COD");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getMenu () { 
    return $this->db->fetchAll("SELECT COD,NOM FROM QP_MENU_ITEM WHERE PADRE_COD IS NULL");
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getMenuItems () { 
    return $this->db->fetchAll("SELECT COD,NOM,URL,SEP FROM QP_MENU_ITEM WHERE PADRE_COD IS NOT NULL ORDER BY COD");
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function presentacionAction () { 
    $dow=date('w');
    $fec=date('Y-m-d');
    if ($dow==1){
      $this->view->reciboerror=$this->db->fetchAll("SELECT * FROM CJ_SP_RECIBO_INCONSISTENTE_RET('$fec')"); 
    }
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function bienvenidoAction () { 
    
  } //end function
  
} //end class

?>