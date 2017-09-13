<?php

use Productsup\Platform\Process;

include __DIR__.'/../../autoload.php';

/**
 * Authentication
 *
 * You'll get the client id and secret at the plaform (API Access)
 **/
$Client = new Productsup\Client();
$Client->id = 4558;
$Client->secret = 'b011f60ae834da8a8054d149b5ed5727';

/**
 * Initialize the Sites Service where you can
 *
 * Create a new site (Sites->insert())
 * Delete a site (Sites->delete())
 * Get a list of sites (Sites->get())
 */
$processCall = new Productsup\Service\Process($Client);

/**
 * to get a certain site you may pass a reference,
 * how references are created is explained later
 */
$reference = new \Productsup\Platform\Site\Reference();
$reference->setKey($reference::REFERENCE_SITE);
$reference->setValue(434456); // Site ID

/**
 * Triggering an action
 *
 * Valid actions are:
 *  - import: triggers an import
 *  - export-all: triggers all exports and channels
 *  - export: triggers an export (old style), action_id parameter with export id is required
 *  - channel: triggers a channel (new style), action_id parameter with channel is required
 *  - all: triggers an import and all exports and channels
 */
$processModel = new Process();
$processModel->action = 'channel';
$processModel->action_id = 51395;
$processModel->addReference($reference);
// This also works, but using a reference is preferred
// $processModel->site_id = $reference->getValue();

// The results reveals whether jenkins accepted the job or, but not does not say
// anything about if the job is already started or queued, in most cases
// it will run immediately
$result = $processCall->post($processModel);

var_dump($result);
