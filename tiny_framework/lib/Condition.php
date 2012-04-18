<?php
/**
 * CONDITION
 **/
class Condition
{
	public function add($field, $value)
	{
		$this->$field = Helper::cleanUpForSQL($value);
	}
	
	public function remove($field)
	{
		$this->$field = null;
	}	
	
  public function getValues()
	{
		return get_object_vars($this);
	}	
	
	
	
	
}