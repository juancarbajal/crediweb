<?
/**
  * Descripción Corta
   * Descripción Larga
    * @category   
    * @package    
    * @copyright  Copyright (c) 2008 Juan Carbajal Paxi <juancarbajal@gmail.com>
    * @license    Leer archivo LICENSE
    */
class BitacoraController extends  Quipu_Controller_Action{ 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    $this->view->subTitle="Bitacora";
    
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function porfecharepAction () { 
    $this->getResponse()->setHeader('Content-Type', 'text/html');//text/xml
    $r=$this->_request->getParams();
    $this->view->desde=$r['FEC_DESDE'];
    $this->view->hasta=$r['FEC_HASTA'];
    $this->view->subTitle="Bitacora de Eventos";
    $this->view->eventos=$this->db->fetchAll("SELECT FEC,ID,OPER,URL,USR,USRT,MAQ FROM LG_AUDITOR WHERE (FEC>=?) AND (FEC<=?)",
                                             array($this->view->desde,
                                                   $this->view->hasta));
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function exportarlimpiarAction () { 
    $this->view->subTitle='Exportar y Limpiar Auditoria';
    if ($this->_request->isXMLHTTPRequest()){
      //        $data=$this->db->fetchAll("SELECT * FROM LG_AUDITOR");
      $files = new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'files');
      $dir=$files->app.'/files/log';
      $name=$files->prefix.'_auditor_'.$_REQUEST['ANIO'].$_REQUEST['MES'].'.csv';
      $namecompress=$name.'.tar.bz2';
      $urlcompress=$this->view->baseUrl().'/files/log/'.$namecompress;
      if (file_exists($dir.'/'.$namecompress))
        $this->json(array('code'=>0,'msg'=>'Archivo Generado - '.$urlcompress,'data'=>$urlcompress));

      try{
        $cont=500;
        $f=fopen($dir.'/'.$name,'a+');
        
        $band=true;
        $filas=100;
        $desde=0;
        while ($band){
          $data=$this->db->fetchAll("SELECT FIRST {$filas} SKIP {$desde} * FROM LG_AUDITOR WHERE FEC LIKE '{$_REQUEST['ANIO']}-{$_REQUEST['MES']}-%' ");
          $desde+=$filas;
          if (count($data)==0) $band=false; //si ya no existen registros que revisar
          foreach($data as $d){
            //fwrite($f,'hola mundo');
            fwrite($f,sprintf('%s|%s|%s|%s|%s|%s|%s|%s|%s|%s'."\n",
                              $d->FEC,
                              $d->ID,
                              $d->OPER,
                              $d->TABLA,
                              $d->VAL_ANT,
                              $d->VAL_POST,
                              $d->URL,
                              $d->USR,
                              $d->USRT,
                              $d->MAQ
                              ));
        
          }
        }
        $this->db->query("DELETE FROM LG_AUDITOR WHERE FEC LIKE '{$_REQUEST['ANIO']}-{$_REQUEST['MES']}-%'");
        fclose($f);
        
        exec("cd $dir; tar -jcvf {$namecompress} {$name} ; rm {$name}");
        
        $this->json(array('code'=>0,'msg'=>'Archivo Generado - '.$urlcompress,'data'=>$urlcompress));
      } catch (Exception $e){
        f_close($f);
        $this->json(array('code'=>1,'msg'=>'Error al Generar Archivo'));
      }
    }
  } //end function
  
} //end class

?>