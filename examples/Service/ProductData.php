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

$ProductService = new Productsup\Service\ProductData($Client);

/**
 * Optional, define chunk site to submit products in parts using multiple 
 * post requests. Default is 5000
 */
$ProductService->setPostLimit(1000);

$Reference = new Productsup\Platform\Site\Reference();

/**
 * You have to specify the site the products belong to.
 * This is done by references to the site.
 *
 * In case you have a productsup site id, you can pass it like this:
 **/
$Reference->setKey($Reference::REFERENCE_SITE);
$Reference->setValue(397); // Site ID

/**
 * In case you want to use your own reference:
 **/
//$Reference->setKey('merchant_id'); // A site tag
//$Reference->setValue(1234); // Value of the tag

$ProductService->setReference($Reference);


/**
 * you may specify which type of import you plan to send:
 *
 * a full import replaces all existing products (default, if not specified)
 * a delta import is used to update the latest full import
 *
 * note: one import/service has only one type
 */
//$ProductService->setImportType(\Productsup\Service\ProductData::TYPE_FULL);
$ProductService->setImportType(\Productsup\Service\ProductData::TYPE_DELTA);



/** 
 * Adding one product to insert.
 *
 * A product is represented by an array.
 * There is no fixed structure you have to follow,
 * the keys you use will become the column name for the resulting upload
 *
 * note: you have to call commit() at the end before the data actually gets handled
 */
$ProductService->insert(array(
        'id' => 123,
        'price' => 39.90,
        'description' => 'some text',
    )
);

$ProductService->insert(array(
        'id' => 124,
        'price' => 99.99,
        'description_de' => 'ein text',
    )
);


// adding 5000 random "products"
for($i=0;$i<5000;$i++) {
    $ProductService->insert(
        array(
            'id' => uniqid(),
            'test' => md5(uniqid()),
            'created' => microtime(true),
            'price' => mt_rand(0,1000000)/100
        )
    );
}

/**
 * deleting products works the same as inserting products:
 */
$ProductService->delete(array(
        'id' => 123,
    )
);

/**
 * Optional: Deactivate the automatic import and export process (thats enabled by default) so that you are responsible
 * for triggering a process after the commit (and for determining the type more precisely).
 * @see examples\service\Process.php
 * @see https://api-docs.productsup.io/#committing
 */
//$ProductService->setAutomaticImportScheduling(false);

/**
 * if you added all products, call commit to start the processing:
 *
 * note: you may not insert or delete products after the submit.
 * if you have more products to insert, please create a new service
 */
$result = $ProductService->commit();
var_dump($result);

/**
 * if you encounter any problems after you started the upload, you can tell the api to discard all uploaded data:
 */
//$result = $ProductService->discard();
