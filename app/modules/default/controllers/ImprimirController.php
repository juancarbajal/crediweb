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
class ImprimirController extends Quipu_Controller_Action { 
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function indexAction () { 
    $this->view->subTitle="Imprimir";
    if (isset($_REQUEST['pagina'])){
      $this->view->pagina=$_REQUEST['pagina'];
    }
  } //end function
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  function capturarAction () { 
    if ($this->_request->isXmlHttpRequest()){
      $this->db->insert('LG_AUDITOR',
                        array('OPER'=>'P',
                              'FEC'=>date('d.m.Y'),
                              'USR'=>$this->identity->USR,
                              'URL'=>$_REQUEST['pagina'],
                              'MAQ'=>$this->getIp()));
      $this->json(array('code'=>'0','msg'=>''));
    }
  } //end function
  
  /**
   * 
   * @param type name desc
   * @uses Clase::methodo()
   * @return type desc
   */
  
  function pdfAction () { 
    //$this->getResponse()->setHeader('Content-Type','application/pdf');//text/xml

    $files = new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'files');
    $dompdf = new DOMPDF();
    $dompdf->load_html('http://'.$_SERVER['HTTP_HOST '].$_REQUEST['pagina']);
    $dompdf->set_paper("a4","portrait"); 
    $dompdf->render();
    //    $filename=$files->app."/pdf/documento.pdf";
    //$dompdf->stream($filename);
    //$this->_redirect($this->view->baseUrl().'/files/pdf/documento.pdf');
  } //end function
  
} //end class

?>