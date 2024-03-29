<?php if (!defined('PAGSEGURO_LIBRARY')) { die('No direct script access allowed'); }
/*
************************************************************************
Copyright [2011] [PagSeguro Internet Ltda.]

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
************************************************************************
*/

/**
 * HTTP Connection Class - used in API calls (CURL library required)
*/
class HttpConnection{
	
	private $status;
	private $response;
	
	public function __construct() {
		if (!function_exists('curl_init')) {
			throw new Exception('CURL library is required.');
		}
	}
	
	public function getStatus(){
		return $this->status;
	}
	public function setStatus($status){
		$this->status = $status;
	}	
	
	public function getResponse(){
		return $this->response;
	}		
	public function setResponse($response){
		$this->response = $response;
	}	
	
	public function post($url, Array $data, $timeout = 20, $charset = 'ISO-8859-1') {
		return $this->curlConnection('POST', $url, $data, $timeout, $charset);
	}
	
	public function get($url, $timeout = 20, $charset, $charset = 'ISO-8859-1') {
		return $this->curlConnection('GET', $url, null, $timeout, $charset);
	}
	
	private function curlConnection($method = 'GET', $url, Array $data = null, $timeout, $charset) {
		
		if (strtoupper($method) === 'POST') {
			$postFields    = ($data ? http_build_query($data, '', '&') : "");
			$contentLength = "Content-length: ".strlen($postFields);
			$methodOptions = Array(
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $postFields,
			);			
		} else {
			$contentLength = null;
			$methodOptions = Array(
				CURLOPT_HTTPGET => true
			);				
		}
		
		$options = Array(
			CURLOPT_HTTPHEADER => Array(
				"Content-Type: application/x-www-form-urlencoded; charset=".$charset,
				$contentLength
			),	
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_CONNECTTIMEOUT => $timeout,
			//CURLOPT_TIMEOUT => $timeout
		); 
		$options = ($options + $methodOptions);
		$curl = curl_init();
		curl_setopt_array($curl, $options);			
		$resp  = curl_exec($curl);
		$info  = curl_getinfo($curl);
		$error = curl_errno($curl);
		$errorMessage = curl_error($curl);
		curl_close($curl);
		$this->setStatus((int)$info['http_code']);
		$this->setResponse((string)$resp);
		if ($error) {
			throw new Exception("CURL can't connect: $errorMessage");
			return false;
		} else {
			return true;
		}
	}
	
	
	
}
	
?>