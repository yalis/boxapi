<?php

namespace Maengkom\Box;

class BoxStandardUser {

	use BoxContent;

    /**
     * Config
     *
     * @var array
     */
    public $config = array(
        'su_client_id' 		=> '',
        'su_client_secret'	=> '',
        'redirect_uri'		=> '',
    );

	private $refresh_token	= '';
	private $access_token	= '';
	private $auth_header	= '';

	// These urls below used for Box Content API
	private $token_url	 	= 'https://api.box.com/oauth2/token';
	private $api_url 		= 'https://api.box.com/2.0';
	private $upload_url 	= 'https://upload.box.com/api/2.0';
	private $authorize_url 	= 'https://app.box.com/api/oauth2/authorize';

	// This url below used for get App User access_token in JWT
	private $audience_url 	= 'https://api.box.com/oauth2/token';

	public function __construct(array $config = array())
	{
		$this->configure($config);

		if( ! $this->loadToken() ) {
			if(isset($_GET['code'])){
				$token = $this->getToken($_GET['code'], true);
				if($this->writeToken($token, 'file')){
					$this->loadToken();
				}
			} else {
				$this->getCode();
			}
		}

		$this->auth_header 	= "-H \"Authorization: Bearer $this->access_token\"";

	}
	
	/**
    * Overrides configuration settings
    *
    * @param array $config
    */
	private function configure(array $config = array())
    {
        $this->config = array_replace($this->config, $config);
        return $this;
    }

	private function getCode() {

		$url = $this->authorize_url.'?'.http_build_query(array(
			'response_type'	=> 'code',
			'client_id'		=> $this->config['su_client_id'],
			'redirect_uri'	=> $this->config['redirect_uri']
		));

		header('location: ' . $url);
		exit();

	}

	/* Second step for authentication [Gets the access_token and the refresh_token] */
	public function getToken($code = '', $json = false) {
		$url = $this->token_url;
		if(!empty($this->refresh_token)){
			$querystring = http_build_query(array(
				'grant_type' 	=> 'refresh_token', 
				'refresh_token' => $this->refresh_token, 
				'client_id' 	=> $this->config['su_client_id'], 
				'client_secret' => $this->config['su_client_secret']));
		} else {
			$querystring = http_build_query(array(
				'grant_type' 	=> 'authorization_code', 
				'code' 			=> $code, 
				'client_id' 	=> $this->config['su_client_id'], 
				'client_secret' => $this->config['su_client_secret']));
		}

		if($json){
			$response = shell_exec("curl $url -d '$querystring' -X POST");
		} else {
			$response = json_decode(shell_exec("curl $url -d '$querystring' -X POST"), true);
		}

		return $response;
	}

	/* Reads the token */
	public function readToken($type = 'file', $json = false) {
		if($type == 'file' && file_exists(__DIR__.'/token.box')){
			$fp = fopen(__DIR__.'/token.box', 'r');
			$content = fread($fp, filesize(__DIR__.'token.box'));
			fclose($fp);
		} else {
			return false;
		}
		if($json){
			return $content;
		} else {
			return json_decode($content, true);
		}
	}

	/* Saves the token */
	public function writeToken($token, $type = 'file') {
		$array = json_decode($token, true);
		if(isset($array['error'])){
			$this->error = $array['error_description'];
			return false;
		} else {
			$array['timestamp'] = time();
			if($type == 'file'){
				$fp = fopen(__DIR__.'/token.box', 'w');
				fwrite($fp, json_encode($array));
				fclose($fp);
			}
			return true;
		}
	}
	
	/* Loads the token */
	public function loadToken() {
		$array = $this->readToken('file');
		if(!$array){
			return false;
		} else {
			if(isset($array['error'])){
				$this->error = $array['error_description'];
				return false;
			} elseif($this->expired($array['expires_in'], $array['timestamp'])){
				$this->refresh_token = $array['refresh_token'];
				$token = $this->getToken(NULL, true);
				if($this->writeToken($token, 'file')){
					$array = json_decode($token, true);
					$this->refresh_token = $array['refresh_token'];
					$this->access_token = $array['access_token'];
					return true;
				}
			} else {
				$this->refresh_token = $array['refresh_token'];
				$this->access_token = $array['access_token'];
				return true;
			}
		}
	}

	private function expired($expires_in, $timestamp) {
		$ctimestamp = time();
		if(($ctimestamp - $timestamp) >= $expires_in){
			return true;
		} else {
			return false;
		}
	}

}
