<?
/**
    * Descripción Corta
     * Descripción Larga
      * @category   
      * @package    
      * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
      * @license    Leer archivo LICENSE
      */
class SincroController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    $this->view->subTitle="Sincronizar con el sistema";
    $files = new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'files');
    $dbfiles = new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'db');
    if ($this->_request->isXmlHttpRequest()) {
      try{
        $comando=sprintf('cd %s; ./restore_sincro.sh %s > sincro.log',
                         $files->app.'/files/backup',
                         $dbfiles->config->dbname
                         );
        $str=exec($comando, $salida);
        $this->json(array('code'=>'0','msg'=>'Sincronizacion realizada '.$comando));
      } catch (Exception $e){
        $this->json(array('code'=>'1','msg'=>$e->getMessage() . ' '.$comando));
      }
    } 
    
  } //end function
  
} //end class

?>