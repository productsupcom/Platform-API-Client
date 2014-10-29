<?php
    
include __DIR__.'/../../vendor/autoload.php';

/**
 * Authentication
 *
 * You'll get the client id and secret at the plaform (API Access) 
 **/
$Client = new Productsup\Client();
$Client->id = 1234;
$Client->secret = 'simsalabim';

$ProductData = new Productsup\Service\ProductData($Client);

/**
 * Optional, define chunk site to submit products in parts using multiple 
 * post requests. Default is 5000
 */
$ProductData->setPostLimit(1000);

$Reference = new Productsup\Platform\Site\Reference();

/**
 * In case you have a productsup site id 
 **/
$Reference->setKey($Reference::REFERENCE_SITE);
$Reference->setValue(1234); // Site ID

/**
 * In case you want to use your own reference
 **/
$Reference->setKey('merchant_id'); // A site tag 
$Reference->setValue(1234); // Value of the tag

$ProductData->setReference($Reference);

/** 
 * Add Products to insert 
 */
$ProductData->insert(array('id' => '123'));
$ProductData->insert(array('id' => '124'));

/** 
 * Commit all inserts
 */
$result = $ProductData->commit();

var_dump($result);
