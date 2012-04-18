<?php

class BaseController
{
  
	public $layout = "layout";
  
  public function dispatch($module, $action, Request $request = null)
	{
	  
	  Context::getInstance()->setModuleName($module);
	  Context::getInstance()->setActionName($action);
	  Context::getInstance()->setRequest($request);
	  
  	if( method_exists($this, $action) )
    {
      
      $this->$action($request);
  		$this->content = $this->setTemplate($module, $action, $request);
  		
    }else{
      
      if( file_exists('modules/'.$module.'/'.$action.'.php') ){
        $this->content = $this->setTemplate($module, $action, $request);
        return false;
      }
      
      if( DEVELOPMENT_ENVIRONMENT ){
        throw new Exception('No such action: ' . strtoupper($action));
      }else{
        $this->redirect404();
      }
    }	  
    
	}
	
  public function setTemplate($module, $action, Request $request = null)
  {
    
    if( ! file_exists('modules/'.$module.'/'.$action.'.php') ){
      exit();
      return false;
    }
    
		$retVal = "";
		$variables = get_object_vars($this);
		
		foreach ($variables as $key => $value)
		{
			$$key = $value;
		}
		
		ob_start();
		include ('modules/'.$module.'/'.$action.'.php');
		$retVal = ob_get_contents();
		ob_end_clean();		
		
		return $retVal;
		
  }
	
	public function getContent()
	{
		return $this->content;
	}
	
	public function setContent($content)
	{
		$this->content = $content ;
	}

	public function redirect($module_action, $params = null,$encrypt = false)
	{
	  if( '404' == $module_action ){
	    header('Location: 404.php');
	    exit();
	  }
	  header('Location: ' . Helper::url_for($module_action, $params,$encrypt));
	  exit();
	}	
	
	public function redirect404()
	{
	  //header("HTTP/1.0 404 Not Found");
    $this->redirect('404');
	}
	
	public function redirect404Unless($condition)
	{
    if( $condition == false || $condition == '' ){
      $this->redirect('404');
    }
	}	
	
  public function redirectReferer(Request $request)
	{
	  header('Location: ' . $request->getReferer());
	  exit();
	}

	public function setSecure($credential = null)
	{
  	if( false == User::getInstance()->isAuthenticated() ){
  	  if( Context::moduleExists('login') ){
        $this->redirect('login/login');
  	  }else{
  	    throw new Exception('--- This action is SECURE ---' );
  	  }
    }
    
	  if( $credential && !User::getInstance()->hasCredential($credential) && !User::getInstance()->hasCredential('superadmin') ){
      header('Location: unauthorized.php');
	    exit();
    }    
	}

	public function hasAction($action)
	{
	  if( method_exists($this, $action) || file_exists($action.'.php') ){
	    return true;
	  }
	  return false;
	}
	
	public function setLayout($layout)
	{
	  $this->layout = $layout;
	}

}
