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
 * Initialize the Projects Service where you can
 * 
 * Create a new project (Projects->insert())
 * Delete a project (and all related sites) (Projects->delete())
 * Get a list of sites (Projects->get())
 */
$Projects = new Productsup\Service\Projects($Client);

/* Override the host to test on development instance */
$Projects->host = 'local.api.productsup.com';

/**
 * Create a new project
 * 
 * A new project only needs a title
 **/
$Project = new Productsup\Platform\Project();
$Project->title = "Testproject ".uniqid();

try {
    $NewProject = $Projects->insert($Project);
} catch (Exception $e) {
    // Handle Exception
}

/**
 * Get list of projects in your account
 */
$projects = array();
try {
    $projects = $Projects->get();
} catch (Exception $e) {
    // Handle Exception
}

foreach ($projects as $Project) {
    echo sprintf('%s: %s', $Project->id, $Project->title).PHP_EOL;
}

/**
 * Delete a project
 */
if (isset($NewProject)) {
    $result = $Projects->delete($NewProject);
    var_dump($result);
}
