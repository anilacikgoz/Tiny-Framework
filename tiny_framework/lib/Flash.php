<?php
require_once 'Session.php';
class Flash
{
  public static function setFlash($name,$value)
  {
    Session::getInstance()->set("flash_" . $name, $value);
  }
  
  public static function getFlash($name)
  {
    $flash = Session::getInstance()->get("flash_" . $name);
    Session::getInstance()->remove("flash_" . $name);
		return $flash;
  }
  
  public static function hasFlash($name)
  {
    return Session::getInstance()->has("flash_" . $name);
  }  
  
}
