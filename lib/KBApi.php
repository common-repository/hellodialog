<?php
class KBApi {

	protected static $_token = null;

	protected $_url        = null;
	protected $_conditions = array();
	protected $_data       = array();

	public static function setToken($token){
		self::$_token = $token;
	}

	public function __construct($url){
		$this->_url = $url;
		return $this;
	}

	public function clear(){
		$this->_conditions = array();
		$this->_data       = array();
		return $this;
	}

	public function data($data){
		$this->_data = $data;
		return $this;
	}

	protected $_validConditions = array(
		'equals',
		'equals-any',
		'not-equals',
		'greater-than',
		'less-than',
		'contains',
		'not-contains',
		'starts-with',
		'ends-with',
		'before',
		'after',
		'contains-any',
		'contains-all',
		'contains-exactly',
		'not-contains-any',
		'not-contains-all'
	);

	public function condition($key, $value, $condition = 'equals'){
		if(!in_array($condition, $this->_validConditions)){
			throw new Exception("'".$condition."' is not a valid condition");
		}
		$this->_conditions[$key] = array(
			'value'     => $value,
			'condition' => $condition
		);
		return $this;
	}

	public function put($id = null){
		return $this->_request('PUT', $id);
	}

	public function delete($id){
		return $this->_request('DELETE', $id);
	}

	public function get($id = null){
		return $this->_request('GET', $id);
	}

	public function post(){
		return $this->_request('POST');
	}
	
	protected function _request($method, $id = null){
		if(is_null($this->_url)){
			throw new Exception("No url specified");
		}

		if(is_null(self::$_token)){
			throw new Exception("API token is required");
		}

		$curl = curl_init();
	
		$requestUrl = 'https://app.hellodialog.com/api/'.$this->_url;

		if(!is_null($id)){
			$requestUrl .= '/'.$id;
		}

		$requestUrl .= '?token='.self::$_token;

		foreach($this->_conditions as $key => $data){
			$requestUrl .= '&condition['.$key.']='.$data['condition'].'&values['.$key.']='.urlencode($data['value']);
		}

		if($this->_data){
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($this->_data));
		}

		curl_setopt($curl, CURLOPT_URL,            $requestUrl);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST,  $method);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);
		curl_close($curl);
		
		return json_decode($response);
	}

}