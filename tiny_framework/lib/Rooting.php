<?php
class Rooting
{
	
	
	static $filename = "rooting.xml";
	function __construct(){
		
	}
	private static function getRooters ($filename){
		if (file_exists($filename)) {
	    	$rooters = simplexml_load_file($filename)	;
	    	return $rooters;
		}
		else 
			return false;
	}
	
	public static function roooterLookUp($alias){
		
		$rooters = self::getRooters(self::$filename);
		$cnt = false;
		foreach ($rooters->children() as $rooter ){
			if ($rooter->url == $alias ){
				return array (
					'module' => (string)$rooter->module,
					'action' => (string)$rooter->action,
				);
			}
		}
		return  false;
		

	}

	

	
}