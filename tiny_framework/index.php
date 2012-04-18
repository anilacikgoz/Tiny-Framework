<?php
define('ACCESSIBLE', true);

spl_autoload_register('autoload');
function autoload($className) {
  if( file_exists('lib/'.$className.'.php') ){
    require_once('lib/'.$className.'.php');
  }
}

require_once '__Config.php';

$url = strchr($_SERVER['REQUEST_URI'], '/?/');

$urlArray = preg_split('/[\/\\\]/',$url);

array_shift($urlArray);
array_shift($urlArray);

$alias  ="";



if (!empty($urlArray[0])){
	$alias .= "/" . $urlArray[0];
	
	

}
if (!empty($urlArray[1])){
	$alias .= "/" . $urlArray[1];
	

}


$router = Rooting::roooterLookUp($alias);


if( strpos($urlArray[0], "VeXu4pevux")  ){
  $urlArray = decrypt($urlArray[0]);
  $urlArray = preg_split('/[\/\\\]/',$urlArray);
}

if( empty($urlArray[0]) ){
  $module = 'default';
}else{
  $module = $urlArray[0];
}

if( empty($urlArray[1]) ){
  $action = 'index';
}else{
  $action = $urlArray[1];
}

$queryString = array();
if( isset($urlArray[2]) ){
  $queryString = $urlArray[2];
}

$postParameters = array();
if( isset($_POST) ){
  $postParameters = $_POST; 
}

$request = new Request($queryString, $postParameters);



if ($router){
	$module  =  $router['module'];
	$action = $router['action'];
}

if( ! is_dir('modules/'.$module) ){
  if( DEVELOPMENT_ENVIRONMENT ){
    throw new Exception('No such MODULE: ' . strtoupper($module));
  }
  exit();
}

require_once('modules/'.$module . '/__Controller.php');
$controller = new Controller();

if( ! $controller->hasAction($action) && ! file_exists('modules/'.$module.'/'.$action.'.php') ){
  if( DEVELOPMENT_ENVIRONMENT ){
    throw new Exception('No such ACTION: ' . strtoupper($module.'/'.$action));
  }
  exit();
}


/*if (!User::getInstance()->isAuthenticated()  ) {

	if ($module!="login" && ($action !=" login" || $action !="register")){
		
		$controller->redirect("login/login",null,false);
		
	}
} */


$controller->dispatch($module, $action, $request);
$content = $controller->getContent();



require_once($controller->layout . '.php');

