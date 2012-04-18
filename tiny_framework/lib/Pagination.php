<?php 
class Pagination
{
	private $data;
	private $totalRows ;
	private $pageNum;
	public $pageInfo ;
	private $query;
	public function __construct($dataObject)
	{
		$this->query = $dataObject;
		$this->data = Database::executeSQL($this->query);
		$this->totalRows = count($this->data->fetchAll());
		$this->getPageNum();
	}
	public function calculate_pages($rows_per_page)
	{
		$data = new Pagination($this->query);
		$last_page = ceil($data->totalRows / $rows_per_page);
		$data->pageNum = (int) $data->pageNum;
		if ($data->pageNum < 1)
		{
		   $data->pageNum = 1;
		} 
		elseif ($data->pageNum > $last_page)
		{
		  $data->pageNum = $last_page;
		}
		$upto = ($data->pageNum - 1) * $rows_per_page;
		$data->pageInfo['limit'] = 'LIMIT '.$upto.',' .$rows_per_page;
		$data->pageInfo['current'] = $data->pageNum;
		if ($data->pageNum == 1)
			$data->pageInfo['previous'] = $data->pageNum;
		else
			$data->pageInfo['previous'] = $data->pageNum - 1;
		if ($data->pageNum == $last_page)
			$data->pageInfo['next'] = $last_page;
		else
			$data->pageInfo['next'] = $data->pageNum + 1;
		$data->pageInfo['last'] = $last_page;
		$data->pageInfo['info'] = 'Page ('.$data->pageNum.' of '.$last_page.')';
		$data->pageInfo['pages'] = $data->get_surrounding_pages($data->pageNum, $last_page, $data->pageInfo['next']);
		return $data;
	}
	public function getActivePage(){
		 $data = Database::executeSQL($this->query .' '. $this->pageInfo['limit'])->fetchAll(PDO::FETCH_ASSOC);
		 Database::getSqlDebug();
		 return $data;
	}
	
	function get_surrounding_pages($last_page, $next)
	{
		$pagearr = array();
		$show = $this->pageNum;
		// at first
		if ($this->pageNum == 1)
		{
			// case of 1 page only
			if ($next == $this->pageNum) return array(1);
			for ($i = 0; $i<=$show; $i++)
			{
				
				array_push($this->pageInfo, $i+1);
			}
			return $this->pageInfo;
		}
		// at last
		if ($this->pageNum == $last_page)
		{
			$start = $last_page - $show;
			if ($start < 1) $start = 0;
			for ($i = $start; $i < $last_page; $i++)
			{
				array_push($this->pageInfo, $i + 1);
			}
			return $this->pageInfo;
		}
		// at middle
	
	}
	
	private function getPageNum(){
		$request = Context::getInstance()->getRequest();
		if (empty($request->page)){
			$this->pageNum = 1;
		}
		else {
			$this->pageNum =$request->page; 
		}
		
	}
}
	