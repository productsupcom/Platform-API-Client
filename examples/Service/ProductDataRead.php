<?php
    
include __DIR__.'/../../vendor/autoload.php';

use Productsup\Service\ProductData;
use Productsup\Platform\Site\Reference;
use Productsup\Query;

/**
 * Authentication
 *
 * You'll get the client id and secret at the plaform (API Access) 
 **/
$Client = new Productsup\Client();
$Client->id = 1234;
$Client->secret = 'simsalabim';

$ProductService = new ProductData($Client);

/**
 * Optional, define chunk site to submit products in parts using multiple 
 * post requests. Default is 5000
 */
$ProductService->setPostLimit(1000);

$Reference = new Reference();

/**
 * You have to specify the site the products belong to.
 * This is done by references to the site.
 *
 * In case you have a productsup site id, you can pass it like this:
 **/
$Reference->setKey(Reference::REFERENCE_SITE);
$Reference->setValue(123); // Site ID

/**
 * In case you want to use your own reference:
 **/
//$Reference->setKey('merchant_id'); // A site tag
//$Reference->setValue(1234); // Value of the tag

$ProductService->setReference($Reference);

$query = new Query();
$query->filter = "id = 'SKU123'";
$query->limit = 1;
$query->offset = 0;

$products = $ProductService->get(ProductData::STAGE_INTERMEDIATE_PREVIEW, 0, $query);

var_dump($products);