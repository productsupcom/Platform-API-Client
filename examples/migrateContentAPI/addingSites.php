<?php
include __DIR__ . '/../../vendor/autoload.php';
/**
 * If you did use by now the content API for shopping,
 * migrating should be quite easy, most of the services follow a similar pattern
 *
 * As a first step you need to authorize for the ProductsUp API.
 * Your client id and secret are provided at http://platform.productsup.com/
 *
 * With them you can create a new client object:
 *
 */
$client = new Productsup\Client();
$client->id = 1234;
$client->secret = 'simsalabim';

/**
 * This client needs to be passed to all services you create:
 */
$siteService = new \Productsup\Service\Sites($client);

/**
 * To import products, you need a project (group of sites) and a site for the products.
 *
 * Once you have a ProductsUp account you also got already one project, you can find its id in the platform.
 * If you want to create a new project, please @see /examples/Service/Projects.php
 *
 * To create a new site it id mandatory to reference the project you want to create the site for:
 */
$project = new \Productsup\Platform\Project();
$project->id = 9697;

$siteService->setProject($project);

/**
 * Creating the new site works like this:
 * for more information about the possible actions of sites @see /examples/Service/Sites.php
 */
$siteObject = new \Productsup\Platform\Site();
$siteObject->title = 'new example site';
$reference = new \Productsup\Platform\Site\Reference();
$reference->setKey('MyTestReference');
$reference->setValue('TestId');
$siteObject->addReference($reference);
$siteObject = $siteService->insert($siteObject);

/**
 * To enable an export to a Google Content API, you can use the "exports service":
 */
$exportService = new \Productsup\Service\Exports($client);
/**
 * adding a reference to the site you want to export:
 * for more information on how references in services work, @see /examples/Service/ProductData.php
 */
$Reference->setKey('MyTestReference');
$Reference->setValue('TestId');
$exportService->setReference($Reference);

/**
 * enableContentApi enables the export to one of your merchant centers
 *
 * note: it is mandatory to authorize the "parent id" via OAuth at http://platform.productsup.com/ before you enable exports
 */
$exportService->enableContentApi(
    '1234', // google merchant center id
    '4321', // parent of current merchant center id
    'de',   // language of the target, in ISO 3166-2 (2 letter country code)
    'de'    // country of the target, in ISO 3166-2 (2 letter country code)
);


/**
 * From now on, you can upload products to this site:
 *
 * For more detailed explanation on the product service
 * @see /examples/Service/ProductData.php and @see /examples/migrateContentAPI/addingProducts.php
 */
$productsService = new \Productsup\Service\ProductData($client);
$productsService->insert(array(
        'id' => 1, // it is mandatory to pass at least an id for the product
        'title' => 'my first product',
        'description' => 'this is the first product',
        'price' => 99.90
    )
);
$productsService->delete(array(
        'id' => 1,
    )
);
$productsService->commit();
