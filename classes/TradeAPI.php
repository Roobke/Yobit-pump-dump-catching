<?php
	class YoBitNetAPI
	{
		const DIRECTION_BUY = 'buy';
		const DIRECTION_SELL = 'sell';
		protected $public_api = 'https://yobit.net/api/3/';
		protected $api_key;
		protected $api_secret;
		protected $noonce;
		protected $RETRY_FLAG = false;
		
		public function __construct($api_key, $api_secret, $base_noonce = false)
		{
			$this->api_key = $api_key;
			$this->api_secret = $api_secret;
			
			if ($base_noonce === false) {
				$this->noonce = time();
			} else {
				$this->noonce = $base_noonce;
			}
		}
		
		protected function getnoonce()
		{
			$n = intval(trim(file_get_contents(str_replace('/classes', '', realpath(dirname(__FILE__))).'/nonce.txt'))) + 1;
			
			file_put_contents(str_replace('/classes', '', realpath(dirname(__FILE__))).'/nonce.txt', $n);

			return array(0.05, $n);
		}
		
		public function apiQuery($method, $req = array())
		{
			$req['method'] = $method;
			$mt = $this->getnoonce();
			$req['nonce'] = $mt[1];
			
			$post_data = http_build_query($req, '', '&');
			$sign = hash_hmac("sha512", $post_data, $this->api_secret);
			$headers = array(
				'Sign: '.$sign,
				'Key: '.$this->api_key,
			);
			
			$ch = null;
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; SMART_API PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
			curl_setopt($ch, CURLOPT_URL, 'https://yobit.net/tapi/');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_ENCODING , 'gzip');
			
			$res = curl_exec($ch);        
			
			if ($res === false) {
				$e = curl_error($ch);
				
				curl_close($ch);
				throw new YoBitNetAPIFailureException('Could not get reply: '.$e);
			} else {
				curl_close($ch);
			}
			
			$result = json_decode($res, true);
			
			if (!$result) {
				throw new YoBitNetAPIInvalidJSONException('Invalid data received, please make sure connection is working and requested API exists');
			}
			
			if (isset($result['error']) === true) {
				if (strpos($result['error'], 'nonce') > -1 && $this->RETRY_FLAG === false) {
					$matches = array();
					$k = preg_match('/:([0-9])+,/', $result['error'], $matches);
					$this->RETRY_FLAG = true;
					
					trigger_error("Nonce we sent ({$this->noonce}) is invalid, retrying request with server returned nonce: ({$matches[1]})!");
					$this->noonce = $matches[1];
					return $this->apiQuery($method, $req);
				} else {
					throw new YoBitNetAPIErrorException('API Error Message: '.$result['error'].". Response: ".print_r($result, true));
				}
			}
			
			$this->RETRY_FLAG = false;
			
			return $result;
		}
		
		protected function retrieveJSON($URL)
		{
			$opts = array('http' =>
				array(
					'method'  => 'GET',
					'timeout' => 10 
				)
			);
			$context  = stream_context_create($opts);
			$feed = file_get_contents($URL, false, $context);
			$json = json_decode($feed, true);
			
			return $json;
		}
		
		public function makeOrder($amount, $pair, $direction, $price)
		{
			if ($direction == self::DIRECTION_BUY || $direction == self::DIRECTION_SELL) {
				$data = $this->apiQuery(
					"Trade",
					array(
						'pair' => $pair,
						'type' => $direction,
						'rate' => $price,
						'amount' => $amount
					)
				);
				
				return $data; 
			} else {
				throw new YoBitNetAPIInvalidParameterException('Expected constant from '.__CLASS__.'::DIRECTION_BUY or '.__CLASS__.'::DIRECTION_SELL. Found: '.$direction);
			}
		}
		
		public function checkPastOrder($orderID)
		{
			$data = $this->apiQuery(
				"OrderList",
				array(
					'from_id' => $orderID,
					'to_id' => $orderID,
					'active' => 0
				)
			);
			
			if ($data['success'] == "0") {
				throw new YoBitNetAPIErrorException("Error: ".$data['error']);
			} else {
				return($data);
			}
		}
		
		public function getPairFee($pair)
		{
			return $this->retrieveJSON($this->public_api.$pair."/fee");
		}
		
		public function getPairTicker($pair)
		{
			return $this->retrieveJSON($this->public_api."ticker/".$pair);
		}
		
		public function getPairTrades($pair)
		{
			return $this->retrieveJSON($this->public_api.$pair."/trades");
		}
		
		public function getPairDepth($pair)
		{
			return $this->retrieveJSON($this->public_api.$pair."/depth");
		}
		
		public function getInfo()
		{
			$data = $this->apiQuery("getInfo");
			
			return $data;
		}
		
		public function getExchangeInfo()
		{
			return $this->retrieveJSON($this->public_api."info");
		}
	}
	
	class YoBitNetAPIException extends Exception {}
	class YoBitNetAPIFailureException extends YoBitNetAPIException {}
	class YoBitNetAPIInvalidJSONException extends YoBitNetAPIException {}
	class YoBitNetAPIErrorException extends YoBitNetAPIException {}
	class YoBitNetAPIInvalidParameterException extends YoBitNetAPIException {}
?>