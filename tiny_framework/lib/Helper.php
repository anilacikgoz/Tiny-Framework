<?php
/**
 * HELPER
 **/
class Helper
{
	
	public static function cleanUpForSQL($str)
	{
		$str = trim($str);
		$str = addslashes($str);
		$str = mysql_escape_string($str);
		$str = strip_tags($str);
		return $str;
	}
	
	public static function cleanUpForHTML($str)
	{
	  
	  $str = stripcslashes($str);
		$str = str_replace("&#039;", "'", $str);
		$str = str_replace("&amp;", "&", $str);
		$str = str_replace("<", "&lt;", $str);
		$str = str_replace(">", "&gt;", $str);
		return $str;
	}
	
  public static function getCurrentURL($hostOnly = null) {
    $pageURL = 'http://'.$_SERVER["SERVER_NAME"];
    
    if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= ":".$_SERVER["SERVER_PORT"];
    } 
    
    if( !$hostOnly ){
      $pageURL .= $_SERVER["REQUEST_URI"];
    }
    
    return $pageURL;
  }
	
	public static function formatDateTime($tarih, $onlyDate = false){
		$ret = "";
		if( $tarih ){
			$ret = substr($tarih,8,2).".".substr($tarih,5,2).".".substr($tarih,0,4)." - ".substr($tarih,10);
			if( $onlyDate ){
				$ret = substr($tarih,8,2).".".substr($tarih,5,2).".".substr($tarih,0,4);
			}
		}
		return $ret;			
  }

	public static function include_partial($partial = null, $array = null) {
	  
	  $partial = explode('/', $partial);
	  
		$require = 'modules/'.$partial[0].'/_'.$partial[1].'.php';
		
		if( isset($array) ){
		
			foreach ($array as $key => $value) {
				$$key = $value;
			}
		}
		
		require $require;
	}
	
	public static function url_for($module_action = null, $params = null, $encrypt = false) {
		
		if( $module_action && !$encrypt ) {
		  $url = WEB_ROOT . $module_action;
		}
		else{
			$url = $module_action;
		}
		
		if( $params ){
		  $qs = http_build_query($params);
		  
		  $url .= "/" . $qs;
		}
		
		if( $encrypt ){
		  $url = WEB_ROOT . encrypt($url); 
		}
		
		return $url;
  
	}	
  
}

/*
 * HOOKS
 * */
function url_for($module_action = null, $params = null, $encrypt = true){
  return Helper::url_for($module_action, $params, $encrypt);
}

if ( ! function_exists( 'exif_imagetype' ) ) {
    function exif_imagetype ( $filename ) {
        if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
            return $type;
        }
    return false;
    }
}

function encrypt($string, $key = "VeXu4pevux") {
  $result = '';
  for($i=0; $i<strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)+ord($keychar));
    $result.=$char;
  }
  
  $result = sha1(rand(36,64)).$key.base64_encode($result);

  return $result;
}

function decrypt($string, $key = "VeXu4pevux") {
  $result = '';
  
  $string = explode($key, $string);
  $string = $string[1];
  
  $string = base64_decode($string);

  for($i=0; $i<strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)-ord($keychar));
    $result.=$char;
  }

  return $result;
}


function createGuid()  {
  $guid = "";
  for ($i = 0; ($i < 8); $i++) {
    $guid .= sprintf("%02x", mt_rand(0, 255));
  }
  return $guid;
}
