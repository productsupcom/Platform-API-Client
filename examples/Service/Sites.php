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
    echo $siteObj->id.' '.$siteObj->domain.PHP_EOL;
}



$SiteList = $Sites->get(252666);
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

$projectObject = new \Productsup\Platform\Project();
$projectObject->name = 'example project '.date('Y-m-d H:i:s');
$Projects = new \Productsup\Service\Projects($Client);
$newProject = $Projects->insert($projectObject);

$SitesService = new \Productsup\Service\Sites($Client);
$SitesService->setProject($newProject);

$siteObject = new \Productsup\Platform\Site();
$siteObject->domain = 'new example site';

$newSite = $SitesService->insert($siteObject);
echo 'new inserted site:'.PHP_EOL;
print_r($newSite);

/**
 * to update the site entry, you can send the edited site object
 */
$newSite->domain = 'updated site name';
$updatedSite = $SitesService->update($newSite);
echo 'updated site:'.PHP_EOL;
print_r($updatedSite);

/**
 * you can also delete sites:
 */
$result = $SitesService->delete($updatedSite);
echo 'result of deleting one site:'.PHP_EOL;
var_dump($result);


/**
 * you can also use tags, to identify your site, please see Tags.php for examples on how to use tags
 */