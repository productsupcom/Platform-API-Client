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

/**
 * Initialize the Sites Service where you can
 * 
 * Create a new site (Sites->insert())
 * Delete a site (Sites->delete())
 * Get a list of sites (Sites->get())
 */
$Tags = new Productsup\Service\Tags($Client);

/**
 * Define Site
 * 
 * In order to manage tags which belong to a site, you have to define a
 * site.
 */
$Site = new Productsup\Platform\Site();
$Site->id = 123;
$Tags->setSite($Site);

/**
 * Create a new tag
 * 
 * A new site only needs a title
 **/
$Tag = new Productsup\Platform\Tag();
$Tag->key = 'merchant_id';
$Tag->value = 1234567;

try {
    $NewTag = $Tags->insert($Tag);
} catch (Exception $e) {
    // Handle Exception
}

/**
 * Get list of sites in the defined project 
 */
$tags = array();
try {
    $tags = $Tags->get();
} catch (Exception $e) {
    // Handle Exception
}

foreach ($tags as $Tag) {
    echo sprintf('%s: %s', $Tag->key, $Tag->value).PHP_EOL;
}

/**
 * Delete a site
 */
if (isset($NewTag)) {
    $result = $Tags->delete($NewTag);
    var_dump($result);
}
