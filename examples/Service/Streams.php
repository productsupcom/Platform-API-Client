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

$StreamService = new Productsup\Service\Streams($Client);
$Reference = new Productsup\Platform\Site\Reference();

/**
 * You have to specify the site the streams belong to.
 * This is done by references to the site.
 *
 * In case you have a productsup site id, you can pass it like this:
 **/
$Reference->setKey($Reference::REFERENCE_SITE);
$Reference->setValue(1); // Site ID
$StreamService->setReference($Reference);

$Streams = $StreamService->get();
echo 'Get one site-stream by its id: '.PHP_EOL;
print_r($Streams);
