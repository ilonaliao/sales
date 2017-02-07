<?php
class Curl{
	const TOKEN_EXPIRED = 202;
	const SUCCESS_EXECUTE = 200;
	const SUCCESS_UPDATE = 201;
	const HTTP_POST = 'POST';
	const HTTP_GET = 'GET';
	const HTTP_DELETE = 'DELETE';
	private $_timeOut = 5;
	private $_url = 4;
	private $_isSSL = 4;
	private $_header = null;
	private $_isPathUrlFormat = false;

	public function __construct($url, $isSSL=false){
		$this->_url = $url;
		$this->_isSSL = $isSSL;
	}

	public function setHeader($header){
		if(is_array($header)){
			$this->_header = $header;
		}
	}

	public function setPathUrlFormat($bool){
		if(is_bool($bool)){
			$this->_isPathUrlFormat = $bool;
		}
	}

	public function post($data){
		return $this->_curl(self::HTTP_POST, "", $data);
	}

	public function get($query){
		return $this->_curl(self::HTTP_GET, $query, array());
	}

	public function delete($query){
		return $this->_curl(self::HTTP_DELETE, $query, array());
	}

	private function _curl($method,$query="",$data=array()){
		if(!in_array($method, array(self::HTTP_GET, self::HTTP_POST, self::HTTP_DELETE))){
			throw new Exception('Required parameter "method" is missing');
		}
		if((empty($query) && empty($data))){
			throw new Exception('Required parameter "query" or "data" is missing');
		}
		if((!empty($query) && !empty($data))){
			throw new Exception('Parameter error');
		}

		$ch = curl_init();
		if($method == self::HTTP_GET){
			if($this->_isPathUrlFormat){
				$url = $this->_url . '/' . $query;
			}else{
				$url = $this->_url . '?' . $query;
			}
			
		}
		if($method == self::HTTP_POST){
			$url = $this->_url;
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		}

		if($method == self::HTTP_DELETE){
			$url = $this->_url.'/'.$query;
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, self::HTTP_DELETE);
		}
		curl_setopt($ch, CURLOPT_URL,$url);
		if($this->_isSSL){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,1);
		}else{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		}

		if(!is_null($this->_header)){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_header);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,$this->_timeOut-1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->_timeOut);
		$chReturn = curl_exec($ch);
		if($chReturn === false){
			throw new Exception("[API:{$url}][curl_errno:".curl_errno($ch)."][".curl_error($ch)."]");
		}else{
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
			if($httpcode == self::SUCCESS_EXECUTE ||
			    $httpcode ==  self::SUCCESS_UPDATE){
				$return = $chReturn;
			}else if($httpcode == self::TOKEN_EXPIRED){
				$return = '{"status":'.self::TOKEN_EXPIRED.'}';
			}else{
				throw new Exception("[failed][API:{$url}][httpCode:{$httpcode}][".$chReturn."]");
			}
		}
		curl_close($ch);
		return $return;
	}
}