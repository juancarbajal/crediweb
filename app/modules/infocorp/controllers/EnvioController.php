<?
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
class Infocorp_EnvioController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function equifaxmesAction () { 

    $this->view->subTitle='Equifax Mensual';
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function equifaxmesenvioAction () { 
    if ($this->_request->isXmlHttpRequest()){
      try{
        $mes=$_REQUEST['MES'];
        $anio=$_REQUEST['ANIO'];
        $filename="equifax_{$mes}_{$anio}.txt";
        $files= new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'files'); 
        $name=$files->app."/files/infocorp/".$filename;
        if (!file_exists($name)){
          $f=new Quipu_PlaneFile($name,
                                 array(
                                       'PER'=>array('i',6),
                                       'COD'=>array('i',6),
                                       'COD_CLIENTE'=>array('n',30),
                                       'DOC_TIPO'=>array('s',1),
                                       'DOC_NUM'=>array('s',12),
                                       'RAZON'=>array('s',100),
                                       'APE_PAT'=>array('s',20),
                                       'APE_MAT'=>array('s',20),
                                       'NOM'=>array('s',30),
                                       'TIP_PER'=>array('s',1),//TIPO DE PERSONA
                                       'MN_DDV'=>array('n',13),//DEUDA DIRECTA VIGENTE
                                       'MN_DDR'=>array('n',13),//DEUDA DIRECTA REFINANCIADA
                                       'MN_DDV_MEN_30'=>array('n',13), //DEUDA DIRECTA VENCIDA MENOR O IGUAL A 30 DIAS
                                       'MN_DDV_MAY_30'=>array('n',13), //DEUDA DIRECTA VENCIDA MAYOR A 30 DIAS
                                       'MN_DD_COB_JUD'=>array('n',13), //DEUDA DIRECTA EN COBRANZA JUDICIAL
                                       'MN_DI'=>array('n',13), //DEUDA INDIRECTA
                                       'MN_DA'=>array('n',13), //DEUDA AVALADA
                                       'MN_LC'=>array('n',13),//LINEA DE CREDITO
                                       'MN_CC'=>array('n',13),//CREDITOS CASTIGADOS
                                       'ME_DDV'=>array('n',13),//DEUDA DIRECTA VIGENTE
                                       'ME_DDR'=>array('n',13),//DEUDA DIRECTA REFINANCIADA
                                       'ME_DDV_MEN_30'=>array('n',13), //DEUDA DIRECTA VENCIDA MENOR O IGUAL A 30 DIAS
                                       'ME_DDV_MAY_30'=>array('n',13), //DEUDA DIRECTA VENCIDA MAYOR A 30 DIAS
                                       'ME_DD_COB_JUD'=>array('n',13), //DEUDA DIRECTA EN COBRANZA JUDICIAL
                                       'ME_DI'=>array('n',13), //DEUDA INDIRECTA
                                       'ME_DA'=>array('n',13), //DEUDA AVALADA
                                       'ME_LC'=>array('n',13),//LINEA DE CREDITO
                                       'ME_CC'=>array('n',13), //CREDITOS CASTIGADOS
                                       'CAL'=>array('s',20)//CALIFICACION
                                       )
                                 );
          $rs=$this->db->fetchAll("SELECT * FROM IC_SP_REP_EQUIFAX_MES_QRY(?,?) WHERE K_MN_DDV>0",array($anio,$mes));
          foreach($rs as $row){
            $f->writeln(
                        array(
                              'PER'=>$anio.$mes,
                              'COD'=>$row->K_COD,
                              'COD_CLIENTE'=>$row->K_COD_CLIENTE,
                              'DOC_TIPO'=>$row->K_DOC_TIPO,
                              'DOC_NUM'=>$row->K_DOC_NUM,
                              'RAZON'=>$row->K_RAZON,
                              'APE_PAT'=>$row->K_APE_PAT,
                              'APE_MAT'=>$row->K_APE_MAT,
                              'NOM'=>$row->K_NOM,
                              'TIP_PER'=>$row->K_TIPO_PER,//TIPO DE PERSONA
                              'MN_DDV'=>$row->K_MN_DDV,//DEUDA DIRECTA VIGENTE
                              'MN_DDR'=>$row->K_MN_DDR,//DEUDA DIRECTA REFINANCIADA
                              'MN_DDV_MEN_30'=>$row->K_MN_DDV_MEN_30, //DEUDA DIRECTA VENCIDA MENOR O IGUAL A 30 DIAS
                              'MN_DDV_MAY_30'=>$row->K_MN_DDV_MAY_30, //DEUDA DIRECTA VENCIDA MAYOR A 30 DIAS
                              'MN_DD_COB_JUD'=>$row->K_MN_DD_COB_JUD, //DEUDA DIRECTA EN COBRANZA JUDICIAL
                              'MN_DI'=>$row->K_MN_DI, //DEUDA INDIRECTA
                              'MN_DA'=>$row->K_MN_DA, //DEUDA AVALADA
                              'MN_LC'=>$row->K_MN_LC,//LINEA DE CREDITO
                              'MN_CC'=>$row->K_MN_CC,//CREDITOS CASTIGADOS
                              'ME_DDV'=>$row->K_ME_DDV,//DEUDA DIRECTA VIGENTE
                              'ME_DDR'=>$row->K_ME_DDR,//DEUDA DIRECTA REFINANCIADA
                              'ME_DDV_MEN_30'=>$row->K_ME_DDV_MEN_30, //DEUDA DIRECTA VENCIDA MENOR O IGUAL A 30 DIAS
                              'ME_DDV_MAY_30'=>$row->K_ME_DDV_MAY_30, //DEUDA DIRECTA VENCIDA MAYOR A 30 DIAS
                              'ME_DD_COB_JUD'=>$row->K_ME_DD_COB_JUD, //DEUDA DIRECTA EN COBRANZA JUDICIAL
                              'ME_DI'=>$row->K_ME_DI, //DEUDA INDIRECTA
                              'ME_DA'=>$row->K_ME_DA, //DEUDA AVALADA
                              'ME_LC'=>$row->K_LC,//LINEA DE CREDITO
                              'ME_CC'=>$row->K_CC, //CREDITOS CASTIGADOS
                              'CAL'=>$row->K_CAL //CALIFICACION
                              )
                        );
      
          }
          //$this->_redirect($filename);
          $this->json(array('code'=>'0','msg'=>'Archivo Generado','data'=>$filename));
        }
        else 
          $this->json(array('code'=>'0','msg'=>'Archivo ya existe','data'=>$filename));
      } // end try
      catch(Exception $e){
        $this->json(array('code'=>'1','msg'=>$e->getMessage()));
      }

    } //end if
  } //end function
  
} //end class
?>