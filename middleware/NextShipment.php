<?php

namespace PHPShopify;

use PHPShopify;
use PHPShopify\ShopifySDK;
use PHPShopify\HttpRequestJson;
$config = array(
    'ShopUrl' => 'laurelbox.myshopify.com',
    'ApiKey' => '626f92776e082ddae247218567abdc0c',
    'Password' => 'fb906bd587a5ab0153a1a0a152b27dcf',
);
PHPShopify\ShopifySDK::config($config);
$shopify = new PHPShopify\ShopifySDK($config);


/**
 * Class NextShipment
 *
 * Prepare the selected dates data
 *
 */
class NextShipment
{
    public $shopify;

    /**
	 * Constructor.
	 *
	 * @param $folder
	 */
	function __construct(){
		//
    }
    


}


?>