<?php
/**
 * Created by PhpStorm.
 * @author Tareq Mahmood <tareqtms@yahoo.com>
 * Created at: 8/21/16 8:39 AM UTC+06:00
 *
 * @see https://help.shopify.com/api/reference/tags/ Shopify API Reference for Event
 */

namespace PHPShopify;


class Tags extends ShopifyResource
{
    /**
     * @inheritDoc
     */
    protected $resourceKey = 'tags';
}