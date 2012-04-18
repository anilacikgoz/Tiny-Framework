<?php


/**
 * Database.
 * 
 * @author Kerem Kayhan
 * @copyright UzakYakin(c)
 * @version 2010-10-20
 **/

class Database 
{
	/** CLASS CONSTANTS **/
	
	protected $connection = null;

	protected $table;
	
	protected $fields = "*";

	protected $columns = array();

	protected $sql;
	
	protected static $sqlDebug = "<b>SQL QUERIES</b><br>";
	protected static $sqlCount = 0;
	
  public function __construct()
  {
		$this->connection = ConnectionManager::getInstance()->getConnection('default');
  }	
	
	public function getConnection()
	{
		return $this->connection;
	}	
	
	private function setSqlDebug($sql)
	{
		if( strpos($sql, "DESCRIBE") !== 0 ){
			self::$sqlCount++;
			self::$sqlDebug .= "<small>".self::$sqlCount."- ". $sql . " (".date("H:i:s").")<br></small>";
		}
	}		
	
	public static function getSqlDebug()
	{
		echo "<p style='background: #F7F7F7; border: 2px solid #DDDDDD; padding: 6px;'>".self::$sqlDebug."</p>";
	}	
	
	public function setTable($table)
	{
		$this->table = $table;
	}		
	
  public function retreiveTable()
  {
    return $this->table;
  }	
	
	public static function getTable($table)
	{
		$db = new Database();
		$db->table = $table;
		return $db;
	}
	
	
	public static function executeSQL($sql)
	{
		$db = new Database();
		return $db->run($sql);
	}	
	
	/* MAGICS */
	
	public function findAll($fields = array(), $orderBy = null, $limit = null, $join = null, $on = null)
	{
		$this->select($fields);

		$sql = "SELECT " . $this->fields . " FROM ". $this->table;
		
		if( $join ){
		  $sql = "SELECT " . $this->fields .", ". $this->getFields($join) . " FROM ". $this->table;
		  $sql .= " LEFT JOIN " . $join . " ON " . $this->table . "." . $on . " = " . $join . ".id";
		}
		
		if( $orderBy ){
			$sql .= " ORDER BY " . $orderBy;
		}
		if( $limit ){
			$sql .= " LIMIT " . $limit;
		}
		
		$result = $this->run($sql)->fetchAll(PDO::FETCH_ASSOC); 

		return $result;
	}
	

  public function findColumns()
  {
    return $this->getColumns($this->table);
  }	
	
	public function find($id, $fields = array(), $join = null, $on = null)
	{
		$this->select($fields);
		$sql = "SELECT " . $this->fields . " FROM ". $this->table . " WHERE id = " . $id;
		
		if( $join ){
		  $sql = "SELECT " . $this->fields .", ". $this->getFields($join) . " FROM ". $this->table;
		  $sql .= " LEFT JOIN " . $join . " ON " . $this->table . "." . $on . " = " . $join . ".id";
		  $sql .= " WHERE " . $this->table . ".id = " . $id;
		}		
		
		$result = $this->run($sql)->fetch(PDO::FETCH_ASSOC);
		if( ! $result ){
		  return false;
		}
		return $result;
		//return $this->toRecord($result);		
	}		
	
	public function findBy($field, $value, $fields = array(), $orderBy = null, $limit = null, $join = null, $on = null)
	{
		$this->select($fields);
		
		$sql = "SELECT " . $this->fields . " FROM ". $this->table . " WHERE " . $field . " = '" . $value ."'";
		
		if( $join ){
		  $sql = "SELECT " . $this->fields .", ". $this->getFields($join) . " FROM ". $this->table;
		  $sql .= " LEFT JOIN " . $join . " ON " . $this->table . "." . $on . " = " . $join . ".id";
		  $sql .= " WHERE " . $field . " = '" . $value ."'";
		}			
		
    if( $orderBy ){
      $sql .= " ORDER BY " . $orderBy;
    }
    if( $limit ){
      $sql .= " LIMIT " . $limit;
    }   		
		$result = $this->run($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}	
	
  public function findOneBy($field, $value, $fields = array(), $orderBy = null, $limit = null)
  {
  	$result = $this->findBy($field, $value, $fields = array());
  	if( count($result) == 0 ){
  	  return false;
  	}
  	//return $this->toRecord($result[0]);
  	return $result[0];
  } 	
	
	public function findConditionally(Condition $condition, $fields = array(), $orderBy = null, $limit = null)
	{
		$values = get_object_vars($condition);
		
		$conditionStr = "";
		$conditions = array();
		
		foreach ($values as $key => $value) 
		{
			/* @TODO 
			 * check if this field exists in this table
			 * */
			//var_dump($this->hasColumn($this->table, $key));
			if( $this->hasColumn($this->table, $key) == false ){
				//throw new Exception('Field error in Condition for <font color="white">' . $this->table .'</font> table');
			}
			
			$conditions[] = $key." = '".$value."'";
		}
		
		$conditionStr = implode(" AND ", $conditions);
		
		$this->select($fields);
		$sql = "SELECT " . $this->fields . " FROM ". $this->table . " WHERE " . $conditionStr;
	  
		if( $orderBy ){
      $sql .= " ORDER BY " . $orderBy;
    }
    if( $limit ){
      $sql .= " LIMIT " . $limit;
    }		
		
		$result = $this->run($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}		
	
	public function findOneConditionally(Condition $condition, $fields = array())
	{
    $result = $this->findConditionally($condition, $fields = array());
    if(empty($result)){
      return false;
    }
    //return $this->toRecord($result[0]);
    return $result[0];
	}

	
	private function toRecord($array)
	{
		$columns = $this->getColumns($this->table);
		
		$r = new Record();
		$r->add('table', $this->table);
		foreach ($columns as $column){
		  $r->add($column, $array[$column]);
		}
		return $r;
	}
	
	private function select($fields)
	{
		$retFields = array();

		if( empty($fields) ){
			$fields = $this->getColumns($this->table);
		}
		foreach ($fields as $field) {
			$retFields[] = $this->table.".".$field;
		}			
		$this->fields = implode(', ', $retFields);
	}	

	
	private function getFields($table)
	{
		$retFields = array();

		$fields = $this->getColumns($table);

		foreach ($fields as $field) {
			$retFields[] = $table . "." . $field ." AS " . $table . "_" . $field;
		}
		return implode(', ', $retFields);
	}	

  private function getColumns($table)
  {
  	$sql = "DESCRIBE ". $table;
		$result = $this->run($sql)->fetchAll(PDO::FETCH_ASSOC);
		
		$fields = array();
		foreach ($result as $arr) {
			$fields[] = $arr['Field'];
		}
		return $fields;
  }  
  
  private function hasColumn($table, $column)
  {
		$fields = $this->getColumns($table);
		return array_search($column, $fields);
  }
  
  public function save(Condition $condition)
  {
  	$vars = get_object_vars($condition);
  	$fields = array();
  	$values = array();
  	$updates = array();
  	
  	foreach ($vars as $key => $value) {
  		$fields[] = $key;
  		$values[] = "'" . $value . "'";
  		if( $key != "id" ){
  			if( strtolower($value) == 'null' ){
  				$updates[] = $key." = NULL";
  			}else{
  				$updates[] = $key." = '".$value."'";
  			}
  		}
  	}
  	
  	/* ACT AS TIMESTAMPABLE IF REQUIRED FIELDS EXIST */
    if( $this->hasColumn($this->table, 'created_at') ){
  		$fields[] = 'created_at';
  		$values[] = "'" . date('Y-m-d H:i:s') . "'";
  	}  	
  	
  	if( $this->hasColumn($this->table, 'updated_at') ){
			$fields[] = 'updated_at';
  		$values[] = "'" . date('Y-m-d H:i:s') . "'";  		
  		$updates[] = "updated_at = '" . date('Y-m-d H:i:s') . "'";
  	}
  	
  	$updateStr = implode(", ", $updates);
  	
  	/*TODO: NOT ID, BUT PRI KEY*/
  	if(array_key_exists("id", $vars)){
  		$sql = "UPDATE ".$this->table." SET " . $updateStr . " WHERE id = " . $vars['id'];
  		$effected_id = $vars['id'];
			$this->run($sql);  
			return $effected_id;  		
  	}else{
  		$sql = "INSERT INTO ".$this->table." (". implode(", ", $fields) . ") VALUES (". implode(", ", $values).")";
			$this->run($sql);  
			return $this->getConnection()->lastInsertId();  		
  	}
  	
  }
  
  public function delete($condition)
  {
  	if( !isset($condition) ){
  		return false;
  	}
  	$vars = array("id" => $condition);
  	
  	if ($condition instanceof Condition) {
  		$vars = get_object_vars($condition);	
  	}
  	
  	$conditions = array();
  	
  	foreach ($vars as $key => $value) {
 			$conditions[] = $key." = '".$value."'";
  	}
  	
  	$conditionStr = implode(" AND ", $conditions);
  	
 		$sql = "DELETE FROM " . $this->table . " WHERE " . $conditionStr;
		
 		$this->run($sql);

  }  
  
  private function run($sql)
  {
  
  		$statement = $this->getConnection()->prepare($sql);
		$statement->execute();
		$this->setSqlDebug($sql);
		return $statement;
  }
  
}


/**
 * RECORD
 **/
class Record implements ArrayAccess
{
  
  public function offsetExists($offset){}
  public function offsetGet($offset){}
  public function offsetSet($offset, $value){}
  public function offsetUnset($offset){}
  
	public function add($field, $value)
	{
		$this->$field = $value;
	}
	
	public function save()
	{
		$db = new Database();
		$db->setTable($this->table);
		unset($this->table);
		$c = new Condition();
		$vars = get_object_vars($this);
		
	  foreach ($vars as $key => $value) {
	  	if( !empty($value) ){
 			  $c->add($key, $value);
	  	}
  	}
  	$db->save($c);
		$this->table = $db->retreiveTable();
	}		
	
	public function delete()
	{
		$db = new Database();
		$db->setTable($this->table);
		$db->delete($this->id);
	}	
	
 	
	
}


/**
 * CONNECTION MANAGER
 **/
class ConnectionManager
{
	private static $instance = null;
	
	private $connections = array();
	private $config = array();
	
	/**
	 * @return ConnectionManager
	 */
	public static function getInstance(){
		if(self::$instance === null){
			self::$instance = new ConnectionManager;
		}
		
		return self::$instance;
	}
	
	public function __construct()
	{
		if(self::$instance !== null){
			throw new Exception(__CLASS__." is a singleton");
		}
	}
	
	private function getConfigFor($name = 'default')
	{
		return array(
		    "dsn" => "mysql:host=" . DB_HOST . ";dbname=" . DB_SCHEMA,
		    "username" => "" . DB_USERNAME . "",
		    "password" => "" . DB_PASSWORD."",
		    "driver_options" => array(
		      PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
		  ));
	}
	
	/**
	 * @return PDO
	 */
	public function getConnection($name = "default")
	{
		if(!isset($this->connections[$name]))
		{
			return $this->connect($name);
		}
		
		return $this->connections[$name];
	}
	
	private function connect($name)
	{
		$conf = $this->getConfigFor($name);
		
		$conn = new PDO($conf["dsn"], $conf["username"], $conf["password"], isset($conf["driver_options"]) ? $conf["driver_options"] : array());
		
		$this->connections[$name] = $conn;
		
		return  $conn;
	}
}