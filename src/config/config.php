<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Box Developer IDs
    |--------------------------------------------------------------------------
    |
    | Set these value based on this documentation
    | https://box-content.readme.io/docs/box-platform
    |
    */

    'au_client_id'     	=> '',
    'au_client_secret' 	=> '',
    'su_client_id'         => '',
    'su_client_secret'     => '',
    'redirect_uri'  	=> '',

    /*
    |--------------------------------------------------------------------------
    | Get Enterprise IDs
    |--------------------------------------------------------------------------
    |
    | Login into box.com and go to admin console menu on top left.
    | Click gear icon on top right, click Enterprise or Business Setting.
    | See the enterprise id on the screen
    |
    */

    'enterprise_id' 	=> '',

    /*
    |--------------------------------------------------------------------------
    | Set Username or User Id
    |--------------------------------------------------------------------------
    |
    | Set user name to use as App User in Enterprise. Usually need single user.
    | Set user id if you know exactly the user id of Enterprise user
    | to use this API.
    |
    */

    'app_user_name'     => '',
    'app_user_id'       => '',

    /*
    |--------------------------------------------------------------------------
    | Expiration Time for Access Token
    |--------------------------------------------------------------------------
    |
    | Set expiration time in second after request token. Max 60 seconds.
    |
    */

    'expiration'    	=> 60,

    /*
    |--------------------------------------------------------------------------
    | Expiration Time for Access Token
    |--------------------------------------------------------------------------
    |
    | use this in terminal openssl genrsa -aes256 -out private_key.pem 2048
    | follow documentation here https://box-content.readme.io/docs/app-auth
    | copy this file in root folder of Laravel 5 project
    |
    */

    'public_key_id'     => '',
    'private_key_file'  => 'private_key.pem',
    'passphrase'        => '1234',

];
