<?php

namespace Maengkom\Box;

use Config;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;

class BoxAppUser 
{

	use BoxContent;
	use Helper;

    /**
     * Config
     *
     * @var array
     */
    public $config = array(
        'au_client_id' 		=> '',
        'au_client_secret'	=> '',
        'redirect_uri'		=> '',
        'enterprise_id'		=> '',
        'app_user_name'		=> '',
        'app_user_id'		=> '',
        'kid_value'			=> '',
        'passphrase'		=> '',
        'expiration'		=> 60,
        'private_key_file'	=> 'private_key.pem',
    );

	private $access_token	= '';
	private $auth_header	= '';

	// These urls below used for Box Content API
	private $token_url	 	= 'https://www.box.com/api/oauth2/token';
	private $api_url 		= 'https://api.box.com/2.0';
	private $upload_url 	= 'https://upload.box.com/api/2.0';

	// This url below used for get App User access_token in JWT
	private $audience_url 	= 'https://api.box.com/oauth2/token';

	public function __construct(array $config = array())
	{
		$this->configure($config);

		// get enterprise admin token to check if app_user_name exist 
		// if exist, get the id and put it on app_user_id
		$this->getToken($this->config['enterprise_id'], "enterprise");
		$this->checkUser($this->config['app_user_name']);

		// user exist, and get token for the user to access box content
		$this->getToken();
	}

	public function getAccessToken()
	{
		return $this->access_token;
	}

	public function getInstance()
	{
		return $this;
	}

    /**
     * Overrides configuration settings
     *
     * @param array $config
     */
	public function configure(array $config = array())
    {
        $this->config = array_replace($this->config, $config);
        return $this;
    }

	private function getToken($id = '', $type = '') {

		if (empty($id)) {
			$id = $this->config['app_user_id']; 
			$type = "user";
		}

		$signer = new Sha256();

		// Set path of private_key.pem. Overwrite the default file as sample.
		$privateKeyString = new Key(
			"file://" . $this->config['private_key_file'], $this->config['passphrase']
		);
	
		$assertion = (new Builder())
			->setHeader('kid', $this->config['kid_value'])
			->setIssuer($this->config['au_client_id'])
			->setSubject($id)
			->set('box_sub_type', $type)
			->setAudience($this->audience_url)
			->setId(uniqid('ABC'))
			->setIssuedAt(time())
			->setExpiration(time() + $this->config['expiration'])
		    ->sign($signer,  $privateKeyString) 
		    ->getToken(); 

		$attributes = "-d 'grant_type=urn:ietf:params:oauth:grant-type:jwt-bearer";
		
		$cid = $this->config['au_client_id'];
		$csc = $this->config['au_client_secret'];

		$result = shell_exec("curl $this->token_url $attributes&client_id=$cid&client_secret=$csc&assertion=$assertion' -X POST");
		
		try
		{
	            $this->access_token = json_decode($result, true)["access_token"];
		}
		catch(\Exception $exception)
		{
		    throw new \Exception("Can't get the access_token for this user configuration...");
		}

		$this->auth_header 	= "-H \"Authorization: Bearer $this->access_token\"";

		$this->auth_header_php = "Authorization: Bearer $this->access_token";
	}

	public function get_token() {
		echo $this->access_token;
	}

	public function createAppUser($name, $json = false) {
		$url = $this->api_url . "/users";
		$attributes = "-d '{\"name\": \"$name\", \"is_platform_access_only\": true}'";
		$result = shell_exec("curl $url $this->auth_header $attributes -X POST");
		if ($json) {
			return $result;
		} else {
			return json_decode($result, true);
		}
	}

	public function getEnterpriseUsers($json = false) {
		$url = $this->api_url . "/users";
		$result = shell_exec("curl $url $this->auth_header");
		if ($json) {
			return $result;
		} else {
			return json_decode($result, true);
		}
	}

	private function checkUser($name) {

		// Get all enterprise users with name like in config.app_user_name. 
		$users = $this->getEnterpriseUsers()['entries'];
		$response = $this->multiArraySearch($users, ['name' => $name]);

		// If not exist, create it.
		if (count($response) <= 0) {
			$app_user_id = $this->createAppUser($name)["id"];
			$this->config['app_user_id'] = $app_user_id;
		}  else {
			// If exist, get the id
			$this->config['app_user_id'] = $users[$response[0]]["id"];
		}	

		return $this->config['app_user_id'];
	}

}
