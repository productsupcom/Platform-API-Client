<?php
    
include __DIR__.'/../../vendor/autoload.php';

$Client = new Productsup\Client();
$Client->id = 1234;
$Client->secret = 'simsalabim';

$ProductData = new Productsup\Service\ProductData($Client);
$ProductData->referenceId = 123;

$ProductData->insert(array('id' => '123'));

$result = $ProductData->submit();

var_dump($result);
