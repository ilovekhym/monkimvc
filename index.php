<?php
set_time_limit(0);
define('APPLICATION_PATH', './applications/');
// use ../library, if library dir is located outside this framework
define('LIBRARY_PATH', '../library/');

$sConfigFile = 'default';
if($_SERVER['SERVER_ADDR']=='127.0.0.1')
{
	/*PHP error settings*/
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set('display_errors','On'); 
	
	$sConfigFile = 'development';
}
//load config
$aConfig = parse_ini_file("applications/{$sConfigFile}.ini", true);

set_include_path(implode(PATH_SEPARATOR,array(
	'.',         
	LIBRARY_PATH, 
	'./applications/controllers',
	'./applications/models',
	get_include_path()
)));
require_once('Monki/Loader.php');
	
//load controller
$controller = $_GET['controller'];
$action = $_GET['action'];

if(!$controller)
	$controller = 'home';
if(!$action)
	$action = 'index';

$oLoader = new Monki_Loader();
$oLoader->loadClass('Monki_View');
$oLoader->loadClass('Monki_Model');
$oLoader->loadClass('Monki_Controller');

$oController = $oLoader->loadClass($controller);
$oController = new $controller();

//constants
$oLoader->loadClass('Constants');

//call Bootstrap
include('applications/Bootstrap.php');

//call action
$oController->$action();

//set variables for view
$aVars = get_object_vars($oController->view);
foreach($aVars as $key=>$val)
{
	$$key = $val;
}

//output the view
ob_start("ob_gzhandler");

if(!$oController->noRender)
	include("/applications/views/{$controller}/$action.php");

$content = ob_get_clean();
echo $content;