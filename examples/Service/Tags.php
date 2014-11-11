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
 * first we need a project and a site which should be tagged,
 * for further explanation see the examples in Projects.php and Sites.php
 */

$project = new \Productsup\Platform\Project();
$project->name = 'test '.date('Y-m-d H:i:s');
$projectService = new \Productsup\Service\Projects($Client);
$project = $projectService->insert($project);

$site = new \Productsup\Platform\Site();
$site->domain = 'test_'.date('Ymdhis').'.tld';
$site->project_id = $project->id;
$sitesService = new \Productsup\Service\Sites($Client);
$site = $sitesService->insert($site);


/**
 * if you want to tag your site to later reference it by this tag instead of remembering the provided site_id
 * you can insert it like this
 *
 * note: a tag needs a key and a value, the key is the tags name (e.g. "myidentifier") and value it's value (e.g. "321")
 */
$tag = new \Productsup\Platform\Tag();
$tag->key = 'myidentifier';
$tag->value = '321';

$tagService = new \Productsup\Service\Tags($Client);
/**
 * note: you need to reference the site you want to tag
 */
$tagService->setSite($site);

/**
 * alternatively you could reference the site by its id in the tag object you insert
 */
// $tag->site_id = $site->id;

$tag = $tagService->insert($tag);
echo 'your new inserted tag:'.PHP_EOL;
print_r($tag);

/**
 * once you added a tag, sites may get also referenced by it,
 * you can now use a tag object as alternative for the id:
 *
 * note: keys and values are only allowed to contain a-z and 0-9 as characters
 */
$tag = new \Productsup\Platform\Tag();
$tag->key = 'myidentifier';
$tag->value = '321';

$sitesService = new \Productsup\Service\Sites($Client);
$sites = $sitesService->get($tag);

echo 'result for getting a site via its tag:'.PHP_EOL;
print_r($sites);

/**
 * if you do have a site_id and want to know which tags are assigned to it, you can query for them like this:
 */

$tagService = new \Productsup\Service\Tags($Client);
$tagService->setSite($sites[0]);
$tags = $tagService->get();

echo 'tags assigned to the test-site:'.PHP_EOL;
print_r($tags);

/**
 * if you want to change an existing tag, you can simply update it:
 */
$tagService = new \Productsup\Service\Tags($Client);
$tags[0]->value = '123';
$updatedTag = $tagService->update($tags[0]);

echo 'the updated tag looks like this:'.PHP_EOL;
print_r($updatedTag);

/**
 * notice that tags may be readonly, you may set them to readonly, but
 * once they are readonly updating and deleting is no longer allowed
 */

$updatedTag->readonly=1;
$tagService->update($updatedTag);

/**
 * if you try to update it now this will result in an exception:
 */
try {
    echo 'if you try to update a readonly tag:'.PHP_EOL;
    $updatedTag->value = 'updated321';
    $result = $tagService->update($updatedTag);
    print_r($result);
} catch(\Productsup\Exceptions\ClientException $e) {
    echo $e->getCode().': '.$e->getMessage().PHP_EOL;
}


/**
 * you can also delete existing properties, as long as they aren't read only:
 */
$newTag = new \Productsup\Platform\Tag();
$newTag->key = 'newTag';
$newTag->value = 'test';

$tagService = new \Productsup\Service\Tags($Client);
$tagService->setSite($site);
$insertedTag = $tagService->insert($newTag);

$result = $tagService->delete($insertedTag);

echo 'result from deleting a tag:'.PHP_EOL;
var_dump($result);

echo 'trying to get a site from a deleted tag:'.PHP_EOL;
$siteService = new \Productsup\Service\Sites($Client);
try {
    $result = $sitesService->get($insertedTag->id);
    print_r($result);
} catch(\Productsup\Exceptions\ClientException $e) {
    echo $e->getCode().': '.$e->getMessage();
}