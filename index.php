<?php 

ini_set('display_errors',1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
use PHPShopify;
use PHPShopify\ShopifySDK;
use PHPShopify\HttpRequestJson;
use PHPShopify\Resource;
use PHPShopify\SimpleResource;
use PHPShopify\Webhook;

$config = array(
    'ShopUrl' => 'laurelbox-dev.myshopify.com',
    'ApiKey' => '27b61365c8b5213ef10b711a19ef990f',
    'Password' => 'd01e529022535ceb2e4a1e85496b67b7',
);

PHPShopify\ShopifySDK::config($config);
$shopify = new PHPShopify\ShopifySDK($config);
$postArray = array(
    "topic" => "orders/create",
    "address" => "https://984bd9fe.ngrok.io",
    "format" => "json"
);


//$products = $shopify->Product->get();

//$order = $shopify->Product->get();



//Webhook ID:
96902;




/**
 * Webook
 
    $url_webhook = "https://api.rechargeapps.com/webhooks/96902/test";
        // The data to send to the API
        $postData = array(
                "address" => $url_webhook,
                "topic" => "subscription/created",
                "format" => "json"
        );

        // Create the context for the request
        $context = stream_context_create(array(
            'http' => array(
            // http://www.php.net/manual/en/context.http.php
            'method' => 'POST',
            'url' => $url_webhook,
            'header' => "x-recharge-access-token: yeD4I03tSNeUiTEKXbcmdCuNCdEUVM\r\n".
            "Content-Type: application/json\r\n"
            //'content' => json_encode($postData)
            )
        ));

        if($data = file_get_contents("php://input")){
            echo 'data';}
        $jsonArray = json_decode($data);
        var_dump($jsonArray);
//$hook->newCharge();

if($hook){

    $hook->verify();
    $topic = $hook->topic();

    if($topic == 'subscription/created'){

        $dataArray = $hook->newSubscription();

        // Connect to database
        $hook->openConn();

        // Record new entry
        $hook->insertNewSubscription($dataArray);

        // Connect to database
        $hook->closeConn();
    } 
    if ($topic == 'charge/created'){

        // Get information about the customer and subscription
        $dataArray = $hook->getCustomerInfo('charge');

        // Connect to database
        $hook->openConn();

        // Update next charges, shipping dates, etc.
        $hook->updateNextThem($dataArray);
        $hook->updateNextUs($dataArray);

        // Connect to database
        $hook->closeConn();
    }
}

//var_dump(json_decode($test));
*/


$hook = new Webhook();
$hook->receiveData();




?>