<?php

namespace Maengkom\Box\Test;

use Maengkom\Box\BoxAppuser;
use PHPUnit_Framework_TestCase;

require('vendor/autoload.php');

class BoxAUContentTest extends PHPUnit_Framework_TestCase
{
	protected $client;

    protected function setUp()
    {
    	$config = array(
	        'client_id' 		=> '',
	        'client_secret'		=> '',
	        'redirect_uri'		=> '',
	        'enterprise_id'		=> '',
	        'app_user_name'		=> '',
	        'app_user_id'		=> '',
	        'public_key_id'		=> '',
	        'passphrase'		=> '',
	        'expiration'		=> 60,
	        'private_key_file'	=> 'private_key.pem',
    	);
    	$this->client = new BoxAppUser($config);
    }

    public function testGetRootFolder()
    {
    	$response = $this->client->getFolderInfo("0");
    	$this->assertArrayHasKey('type', $response);
    }

    public function testCreateFolder()
    {
    	$newFolder = "testFolder";
    	$response = $this->client->createFolder($newFolder, "0");
    	$this->assertArrayHasKey('name', $response);
    }
}
