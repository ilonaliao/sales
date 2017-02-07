<?php
/**
 * 儲存工作元件
 */
class SaveJobs{
	private $_idck;
	private $_timeOut = 4;
	private $_headers = array();
	const CURL_TOKEN_EXPIRED = 202;
	const CURL_SUCCESS_EXECUTE = 200;
	const CURL_SUCCESS_UPDATE = 201;
	const HTTP_POST = 'POST';
	const HTTP_GET = 'GET';
	const HTTP_DELETE = 'DELETE';
	private $curl;

	public function __construct($idck = ''){
		if(!empty($idck)){
		  $this->_idck = $idck;
		  $this->curl = new Curl(SAVEJOBS_API_PRIVATE,true);
		  $this->curl->setPathUrlFormat(true);
		  $this->_setHeaders();
		  
		}else{
			throw new Exception('idenfity error');
		}
	}

	/**
	 * 重取token塞入header
	 * @param boolean $reset 是否重取
	 */
	private function _setHeaders($reset = false){
		if($reset)
			Util::deleteToken();
		$key = Util::getToken();
		$this->curl->setHeader(array('Authorization: Bearer '.$key));
	}

	/**
	 * 取得列表
	 * @return String 儲存工作列表
	 */
	public function getList(){
		$list = json_decode($this->curl->get($this->_idck));
		// var_dump($list);exit;
		if($list->status==self::CURL_TOKEN_EXPIRED){
			$this->_setHeaders(true);
			$list = json_decode($this->curl->get($this->_idck));
		}
		// var_dump($this->curl->get($this->_idck));exit;
		if($list->status==self::CURL_SUCCESS_EXECUTE){
			$jobListArr = (Array)$list->data->list;
			$newList = []; //encode jobno
			if(count($jobListArr) > 0){
				foreach ($list->data->list as $key => $value) {
					$encodeKey = base_convert($key, 10, 36);
					$newList[$encodeKey] = $encodeKey;
				}
				unset($list->data->list);
			}
			$list->data->list = $newList;
			return json_encode($list);
		}else{
			throw new Exception('Failed to get token');
		}
	}

	/**
	 * 儲存工作
	 * @return string
	 */
	public function add($custno,$jobno){
		$custno = base_convert($custno, 36, 10);
		$jobno = base_convert($jobno, 36, 10);

		$data = array(
			'id'=>$this->_idck,
			'custno'=>$custno,
			'jobno'=>$jobno
		);
		$r = json_decode($this->curl->post($data));
		if($r->status==self::CURL_TOKEN_EXPIRED){
			$this->_setHeaders(true);
			$r = json_decode($this->curl->post($data));
		}
		return json_encode($r);
	}

	/**
	 * 刪除儲存工作
	 * @return string
	 */
	public function delete($custno,$jobno){
		$custno = base_convert($custno, 36, 10);
		$jobno = base_convert($jobno, 36, 10);
		$data = $this->_idck."/custno/".$custno."/jobno/".$jobno;
		$r = json_decode($this->curl->delete($data));
		if($r->status==self::CURL_TOKEN_EXPIRED){
			$this->_setHeaders(true);
			$r = json_decode($this->curl->delete($data));
		}
		return json_encode($r);
	}
}
