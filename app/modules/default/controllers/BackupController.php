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
class BackupController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    $this->view->subTitle="Copia de Seguridad";
    $this->view->copias=$this->getCopias();
    $files = new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'files');
    $this->view->prefix=$files->prefix;
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function getCopias () { 
    $rs=$this->db->fetchAll('SELECT NOM,FEC FROM SG_COPIA ORDER BY USRT DESC');
    return $rs;
  } //end function
  
  /**
   * Genera copia de seguridad
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function copiaAction () { 
    if ($this->_request->isXmlHttpRequest()){
      try{
        $files = new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'files');
        $dbfiles = new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'db');
        $name=$files->prefix.'_backup_'.date('dmY').'_'.date('His').'.tar.gz';
        $dbarray=explode('/',$dbfiles->config->dbname);
        $dbname=$dbarray[count($dbarray)-1];
        unset($dbarray[count($dbarray)-1]);
        $comando=sprintf('cd %s; rm *.tar.gz ; ./backup.sh %s %s %s %s %s> backup.log',
                         $files->app.'/files/backup',
                         $files->app,
                         implode('/',$dbarray),
                         $dbname,
                         $files->app.'/files/backup',
                         $name);
        exec($comando, $salida);
        $this->json(array('code'=>'0','msg'=>'Copia generada : '.$name,'data'=>$name));
      } catch (Exception $e){
        $this->json(array('code'=>'1','msg'=>$e->getMessage()));
      }
      
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function restaurarAction () { 
    
  } //end function
  
} //end class

?>