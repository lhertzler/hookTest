<?php
/**
 * Created by PhpStorm.
 * @author Tareq Mahmood <tareqtms@yahoo.com>
 * Created at: 9/9/16 12:28 PM UTC+06:00
 */

namespace PHPShopify;


class Resource extends ShopifySDK
{
    /**
     * @var ShopifySDK $shopify;
     */
    public static $shopify;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass()
    {
        $config = array(
            'ShopUrl' => getenv('SHOPIFY_SHOP_URL'),
            'ApiKey' => getenv('SHOPIFY_API_KEY'),
            'SharedSecret' => getenv('SHOPIFY_API_SECRET'),
        );

        self::$shopify = ShopifySDK::config($config);
        ShopifySDK::checkApiCallLimit();
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass()
    {
        self::$shopify = null;
    }
}