<?php

require_once 'Database.php';
require_once 'Session.php';

class User
{
	protected static $instance = null;
	public $user = array();
	
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

  public function authenticate($user)
  {
	  Session::getInstance()->set('user', array("id" => $user['id']));
  }  
  
  public function signOut($email, $password)
  {
    Session::getInstance()->remove('user');
  }  
  
  public function isAuthenticated()
  {
    return Session::getInstance()->has('user');
  }
  
  public function setCulture($culture)
  {
    Session::getInstance()->set('culture', $culture);
  }
    
  public function hasCulture()
  {
    return Session::getInstance()->has('culture');
  }
  
  public function getCulture()
  {
    if( Session::getInstance()->has('culture') ){
      return Session::getInstance()->get('culture');
    }
    return "en";
  }  
  
  public function __get($name)
  {
    if( !$this->getUser() ){
      return false;
    }
    $user = $this->getUser();
    
    return $user[$name];
  }    
	
  public function getUser()
  {
    if( !Session::getInstance()->has('user') ){
      return false;
    }
    $user = Session::getInstance()->get('user');
	  return Database::getTable('user')->find($user['id']);
  }
  
  public function hasCredential($credential)
  {
    $user = $this->getUser();
    if( $user['credential'] == $credential ) {
      return true;
    }
    return false;
  }  
	
  // Prevent users to clone the instance
  public function __clone()
  {
    trigger_error('Clone is not allowed.', E_USER_ERROR);
  }  
 
}