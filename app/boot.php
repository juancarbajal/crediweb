<?php
//Manejo de Errores
  //error_reporting(E_ALL);
  //error_reporting(~E_ALL);
ini_set('display_startup_errors', 1);  
ini_set('display_errors',1);
//ini_set('ibase.dateformat', '%d.%m.%Y');
//ini_set('ibase.timestampformat','%d.%m.%Y %H:%M:%S');
ini_set('ibase.dateformat', '%Y-%m-%d');
ini_set('ibase.timestampformat','%Y-%m-%d %H:%M:%S');
ini_set("session.gc_maxlifetime", "1800"); //session a 30 minutos
set_time_limit(0); //Tiempo en segundos de demora de una operacion

/*
function Error_Handler($error,$mensaje,$fichero,$linea){
	$textoError="<div style='border:1'><b>ERROR : </b><br/>".
			"Sentimos comunicarle que se ha producido un error<br/>".
			"Tipo de error: <font color='red'>$error: $mensaje</font><br/> Archivo:<b>$fichero</b> l&iacute;nea <b>$linea</b>".
			"</div>";
	echo $textoError;
}*/
function Error_Handler($error,$mensaje,$fichero,$linea){
	$textoError=sprintf("<description>%s</description>\n",htmlentities("ERROR : Sentimos comunicarle que se ha producido un error.".
			"\nTipo de error: $error \n$mensaje \nArchivo: $fichero linea $linea"));
	echo $textoError;
}
//set_error_handler("Error_Handler");
//Configuracion de Directorios y de Zonas Horarias
define('ROOT_DIR', dirname(dirname(__FILE__)));
define('CONFIG_DIR', ROOT_DIR.'/app/config');
date_default_timezone_set('America/Lima');
//date_default_timezone_set('UTC');
set_include_path(ROOT_DIR.'/lib'
				 .PATH_SEPARATOR.ROOT_DIR.'/app/models'
                 .PATH_SEPARATOR.ROOT_DIR.'lib/dompdf-0.5.1'
				 );

//Cargador de Clases
require_once('Zend/Loader.php');
Zend_Loader::registerAutoload();
//Zend_Loader::registerAutoload();
//Zend_Loader_Autoloader();
//require_once('dompdf-0.5.1/dompdf_config.inc.php');
//spl_autoload_register('DOMPDF_autoload');
//Zend_Loader_Autoloader::getInstance()->pushAutoloader('DOMPDF_autoload','DOMPDF_'); 
//$autoloader->pushAutoloader(array('DOMPDF', 'autoload'), 'DOMPDF_');
//Configuración General:

$appConfig=new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'app');
if (isset($appConfig->property->title))
define('APP_TITLE',$appConfig->property->title);
//define('THEME',$appConfig->theme);


//Configuración de Conexion:
//Doctrine::debug(true);
$dbConfig = new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'db'); 
//$dsn = $dbConfig->type.':dbname='.$dbConfig->config->dbname.';host='.$dbConfig->config->host;
//$dbh = new PDO($dsn, $dbConfig->config->username, $dbConfig->config->password);
//$db = Doctrine_Manager::connection($dbh);

$db = Zend_Db::factory($dbConfig->adapter,$dbConfig->config->toArray()); 
Zend_Db_Table::setDefaultAdapter($db);
$db->setFetchMode(Zend_Db::FETCH_OBJ);
//$db->setFetchMode(Zend_Db::FETCH_ASSOC);

//Sesiones
/*
$sessionConfig = array(
                'name'           => 'QP_SESSION',
                'primary'        => 'ID',
                'modifiedColumn' => 'MOD',
                'dataColumn'     => 'DATA',
                'lifetimeColumn' => 'LIFE'
                );*/
$sessionConfig=new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'session');
Zend_Session::setOptions($sessionConfig->toArray());
//Zend_Db_Table_Abstract::setDefaultAdapter($db);
//Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($sessionConfig));
Zend_Session::start();

//Log
/*$logger = new Zend_Log(
	//new Zend_Log_Writer_Db($db, 'lg_bitacora', 	array('lvl' => 'priority', 'msg' => 'message'))
	new Zend_Log_Writer_Db($db, $dbConfig->log->tablename, 	array('lvl' => $dbConfig->log->field->lvl, 'msg' => $dbConfig->log->field->msg ))
	);
*/

//Configuración de Usuarios
$userConfig = new Zend_Config_Ini(CONFIG_DIR.'/config.ini', 'users'); 
//Autentificacion de Usuarios
$auth=Zend_Auth::getInstance();
$acl=new Quipu_Acl();

#Zend_Layout::startMvc(array('layout'=>'layout','layoutPath'=>ROOT_DIR.'/app/layouts'));
$controller=Zend_Controller_Front::getInstance();
$controller->registerPlugin(new Quipu_Controller_Plugin_Authorization($auth,$acl)); //REVISAR
$controller->registerPlugin(new Quipu_Controller_Plugin_LogView($db,$auth)); //Guardamos en el log 

$controller->addModuleDirectory(ROOT_DIR.'/app/modules')
	->throwExceptions(true);


//$controller->throwExceptions(true);
try{
  $controller->dispatch();
} catch(Exception $e){
  echo nl2br($e->__toString());
}