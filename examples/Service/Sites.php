<?php

include __DIR__.'/../../autoload.php';

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
 * getting all sites:
 */
$SiteList = $Sites->get();
echo 'Get all your sites: '.PHP_EOL;
foreach($SiteList as $siteObj) {
    echo $siteObj->id.' '.$siteObj->title.PHP_EOL;
}


/**
 * to get a certain site you may pass a reference,
 * how references are created is explained later
 */
$reference = new \Productsup\Platform\Site\Reference();
$reference->setKey('MyTestReference');
$reference->setValue('1234');

$SiteList = $Sites->get($reference);
echo 'Get one site by its id: '.PHP_EOL;
print_r($SiteList);

echo 'Getting a site that does not exist results in an ClientException: '.PHP_EOL;

try {
    $SiteList = $Sites->get(-1);
} catch(\Productsup\Exceptions\ClientException $e) {
    echo $e->getCode().': '.$e->getMessage().PHP_EOL;
}

/**
 * you can also get only sites for a certain project,
 * to do so you have to set the project object to the service as a reference:
 */
$Project = new \Productsup\Platform\Project();
$Project->id = 9659;

$Sites->setProject($Project);
$SiteList = $Sites->get();
echo 'Getting a site list for a certain project'.PHP_EOL;
print_r($SiteList);


/**
 * to insert one project, you need a reference to the project
 * you can also create a new one:
 */

// creating the project
$projectObject = new \Productsup\Platform\Project();
$projectObject->name = 'example project '.date('Y-m-d H:i:s');
$Projects = new \Productsup\Service\Projects($Client);
$newProject = $Projects->insert($projectObject);

// create the service and reference the project
$SitesService = new \Productsup\Service\Sites($Client);
$SitesService->setProject($newProject);

$siteObject = new \Productsup\Platform\Site();
$siteObject->title = 'new example site';

/**
 * if you want to reference the project from now on with your identifier,
 * you can create a reference while inserting:
 *
 * note: references have to be unique,
 * if you try to add a reference that already exists you will receive an conflict exception
 */
$reference = new \Productsup\Platform\Site\Reference();
$reference->setKey('MyTestReference');
$reference->setValue(uniqid());
$siteObject->addReference($reference);


// perform the actual insert
$newSite = $SitesService->insert($siteObject);
echo 'new inserted site:'.PHP_EOL;
print_r($newSite);


/**
 * to update the site entry, you can send the edited site object
 */
$newSite->title = 'updated site name';
$updatedSite = $SitesService->update($newSite);
echo 'updated site:'.PHP_EOL;
print_r($updatedSite);

/**
 * you can also delete sites:
 */
$result = $SitesService->delete($updatedSite);
echo 'result of deleting one site:'.PHP_EOL;
var_dump($result);
