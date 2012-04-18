<?php
session_start();
class Session
{
	protected static $instance = null;
	
	public static function getInstance()
	{
		if( !self::$instance )
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct()
	{
	}
  
  public function set($name,$value)
  {
		$_SESSION[$name] = $value;
  }
  
  public function get($name)
  {
		return $_SESSION[$name];
  }
  
  public function remove($name)
  {
    unset($_SESSION[$name]);
  }  
  
  /*
   * @return Boolean
   **/
  public function has($name)
  {
    return !empty($_SESSION[$name]);
  }  
  
  public function getAll()
  {
    return $_SESSION;
  }   
  
}
