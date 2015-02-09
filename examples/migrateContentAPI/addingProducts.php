<?php
include __DIR__ . '/../../vendor/autoload.php';

/**
 * This example shows how to insert or delete products to the ProductsUp API if you used the content API for shopping
 * before and have already a site to upload to.
 *
 * You need to have a site to upload products to. On how to create and enable sites
 * @see /examples/migrateContentAPI/addingSites.php and @see /examples/Service/Sites.php
 */

// authorization provided at http://platform.productsup.com/
$client = new \Productsup\Client();
$client->id = 1234;
$client->secret = 'simsalabim';

$productsService = new \Productsup\Service\ProductData($client);
// reference to the site you want to add or delete products for, for a more detailed description @see /examples/Service/Sites.php
$reference = new \Productsup\Platform\Site\Reference();
$reference->setKey('MyTestReference');
$reference->setValue('TestId');
$productsService->setReference($reference);

/**
 * one example with all fields supported by the content API for shopping. Not all of the fields are required,
 * only the "id" column is mandatory.
 *
 * note: the field names are similar to the content API, but may be slightly different
 */
$product = array(
    'id' => 1,
    'additionalImageLink' => 'http://example.com/img/1.jpg,http://example.com/img/2.jpg',
    'adult' => 1,
    'adwords_grouping' => '',
    'adwords_labels' => '',
    'adwords_redirect' => '',
    'age_group' => 'adult',
    'availability' => 'in stock',
    'availability_date' => '2014-12-01',
    'brand' => 'my brand',
    'color' => 'red',
    'condition' => 'new',
    'custom_label_0' => 'custom label 0',
    'custom_label_1' => 'custom label 1',
    'custom_label_2' => 'custom label 2',
    'custom_label_3' => 'custom label 3',
    'custom_label_4' => 'custom label 4',
    'description' => 'describes my product',
    'energy_efficiency_class' => '',
    'expiration_date' => '',
    'gender' => 'male',
    'google_product_category' => '',
    'gtin' => '',
    'identifier_exists' => '',
    'image_link' => 'http://example.com/img/default.jpg',
    'is_bundle' => '',
    'item_group_id' => '',
    'link' => 'http://example.com/product.html',
    'material' => '',
    'mobile_link' => 'http://m.example.com/product.html',
    'mpn' => '',
    'multipack' => '',
    'online_only' => '',
    'pattern' => '',
    'price' => '90.90 EUR',
    'product_type' => '',
    'sale_price' => '12.34 EUR',
    'sale_price_effective_date' => '',
    // CSV format: <country>::<service>:<price>( <currency>) i.e. DE::DHL:5.00 EUR prices have to be dot-decimal.
    // multiple shipping options can be added comma separated "DE::DHL:5.00 EUR,AT::Express:19.50"
    'shipping' => 'DE::DHL:5.00 EUR,AT::Express:19.50',
    'shipping_label' => '',
    'size_system' => '',
    'size_type' => '',
    'size' => '',
    'title' => 'example product'
);


try {
    $productsService->insert($product);
    $productsService->commit();
} catch (\Productsup\Exceptions\ServerException $e) {
    // A exception at the API Server happened, should not happen but may be caused by a short down time
    // You may want to retry it later, if you keep getting this kind of exceptions please notice us.
    throw new Exception('Error at the productsup API, retry later');
} catch (\Productsup\Exceptions\ClientException $e) {
    // Most likely some of the data you provided was malformed
    // The error codes follow http status codes, @see http://en.wikipedia.org/wiki/List_of_HTTP_status_codes#4xx_Client_Error
    // The message may give more information on what was wrong:
    echo $e->getCode().' '.$e->getMessage();
} catch (\Exception $e) {
    // Exceptions not within the Productsup namespace are not thrown by the client, so these exceptions were most likely
    // thrown from your application or another 3rd party application

    // however, if you don't catch Productsup exceptions explicitly, you can catch them all like this
    echo $e->getCode().' '.$e->getMessage();
    throw $e;
}



