<?php
/**
 * 取職務列表
 */
class JoblistModule{

	private $cond; //GET condition
	private $jCond; //SearchEngine condition
	private $_timeOut = 5;

	public function __construct($cond){
		$this->jCond = new JoblistCondition();
		if(isset($cond['area'])){
			$this->jCond->area = $cond['area'];
		}
		if(isset($cond['jobcat'])){
			$this->jCond->jobcat = $cond['jobcat'];
		}
		if(isset($cond['indcat'])){
			$this->jCond->indcat = $cond['indcat'];
		}
		if(isset($cond['keyword'])){
			$this->jCond->keyword = $cond['keyword'];
		}
		if(isset($cond['asc'])){
			$this->jCond->asc = $cond['asc'];
		}
		if(isset($cond['order'])){
			$this->jCond->order = $cond['order'];
		}
		if(isset($cond['page'])){
			$this->jCond->page = $cond['page'];
		}
	}

	public function getNccJobsApi(){
		$query = $this->jCond->getAllCondition();
		$curl =  new Curl(NCC_JOB_SEARCH_API);
		$data = $curl->get("accessKey=104jbcp&".http_build_query($query));
		$return = $this->_resetTxt($data);
		return $return;

	}

	/**
	 * 針對搜尋引擎回來的資料再做處理
	 * 1. 加密custno為enCC
	 * 2. jobName提醒字符處理
	 * @param  String $json data
	 * @return String $json 處理後的資料
	 */
	private function _resetTxt($json){
		$obj = json_decode($json);
		if(!empty($obj->list)){
			foreach ($obj->list as $key => $value) {
				$obj->list[$key]->enCC = base_convert($obj->list[$key]->c, 10, 36);

				$tempJobName = str_replace("[[[","<em>",$obj->list[$key]->jobName);
				$obj->list[$key]->jobName = str_replace("]]]","</em>",$tempJobName);
			}
		}
		return json_encode($obj);
	}
}

