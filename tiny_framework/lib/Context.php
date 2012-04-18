<?php

class Context
{
  
	protected static $instance = null;
	public $actionName = "";
	public $moduleName = "";
	public $request = "";
	
	public static function getInstance()
	{
		if( !self::$instance )
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function __construct()
	{
	}

  public function setActionName($action)
  {
	  $this->actionName = $action;
  }

  public function getActionName()
  {
	  return $this->actionName;
  }   
  
  public function setModuleName($module)
  {
	  $this->moduleName = $module;
  }

  public function getModuleName()
  {
	  return $this->moduleName;
  }   
  
  public function getController()
  {
	  return new BaseController();
  } 

  public function setRequest($request)
  {
	  $this->request = $request;
  }  
  
  public function getRequest()
  {
	  return $this->request;
  } 

  public function moduleExists($module)
  {
	  return is_dir('modules/' . $module);
  }   
  
  
  
	
  // Prevent users to clone the instance
  public function __clone()
  {
    trigger_error('Clone is not allowed.', E_USER_ERROR);
  }  
 
}