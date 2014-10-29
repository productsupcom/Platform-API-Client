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
$Sites = new Productsup\Service\Sites($Client);

/**
 * Define Project
 * 
 * In order to manage sites which belong to a project, you have to define a
 * project.
 */
$Project = new Productsup\Platform\Project();
$Project->id = 123;
$Sites->setProject($Project);

/**
 * Create a new site
 * 
 * A new site only needs a title
 **/
$Site = new Productsup\Platform\Site();
$Site->title = "Testsite ".uniqid();

$Reference = new Productsup\Platform\Site\Reference();
$Reference->setKey('merchant_id');
$Reference->setValue(1234);

$Site->addReference($Reference);

try {
    $NewSite = $Sites->insert($Site);
} catch (Exception $e) {
    // Handle Exception
}

/**
 * Get list of sites in the defined project 
 */
$sites = array();
try {
    $sites = $Sites->get();
} catch (Exception $e) {
    // Handle Exception
}

foreach ($sites as $Site) {
    echo sprintf('%s: %s', $Site->id, $Site->title).PHP_EOL;
}

/**
 * Delete a site
 */
if (isset($NewSite)) {
    $result = $Sites->delete($NewSite);
    var_dump($result);
}
