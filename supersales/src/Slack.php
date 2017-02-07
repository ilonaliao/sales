<?php
/**
 * slack訊息通知
 */
class Slack{
	private $_slack;
	public function __construct($webhookUrl, $channel = null, $username = 'monolog')
    {
    	if(empty($webhookUrl)){
    		throw new Exception('Required parameter "webhookUrl" is missing');
    	}
        $settings = array(
    		'username' => $username,
    		'channel' => $channel,
    		'link_names' => true
		);
       	$this->_slack = new Maknz\Slack\Client($webhookUrl, $settings);
    }
    /**
     * error message
     * @param  string|object $e
     */
	public function log($e){
		$sendFields = [
			'envFlag'=>SRV_ENV_FLAG,
			'http_referer'=>'',
			'cookie'=>'',
			'file'=>'',
			'line'=>'',
			'trace'=>'',
		];

		if(!empty($_SERVER['HTTP_REFERER']))
			$sendFields['http_referer'] = $_SERVER['HTTP_REFERER'];
		if(!empty($_COOKIE))
			$sendFields['cookie'] = $_COOKIE;

		if(!is_string($e) && get_class($e) == 'Exception'){
			$sendFields['file'] = $e->getFile();
			$sendFields['line'] = $e->getLine();
			$sendFields['trace'] = $e->getTraceAsString();
			$message = $e->getMessage();
		}else{
			$message = $e; 
 		}

		$slackFields = [];
		foreach ($sendFields as $key => $value) {
			if(empty($value)){
				continue;
			}
			array_push($slackFields,array(
				'title' => $key,
				'value' => (is_string($value)) ? $value : json_encode($value),
				'short' => false)
			);
		}
		$this->_slack->attach([
		    'color' => 'danger',
		    'fields' => $slackFields
		])->send($message);
	}
}