<?php

namespace Productsup\Platform;

class Source extends DataModel {
    const TYPE_DEFAULT = 1;
    const TYPE_ADDITIONAL = 2;

    /** @var string The datasource is fully operational */
    const STATUS_ACTIVE = 'active';
    /** @var string The datasource is paused and the data is not imported */
    const STATUS_PAUSED = 'paused';

    public $id;
    public $site_id;
    public $description;
    /** @var string url to import source */
    public $source;
    /** @var int one of the TYPE_* constants */
    public $import_type = self::TYPE_DEFAULT;
    public $import_id;
    /** @var string one of the STATUS_* constants */
    public $status;
    /** @var array As received from API: a list like [0 => 'key1 : val1', 1 => 'key2 : val2'] */
    public $settings;

    /**
     * This is a helper method to access the settings more easily.
     *
     * The setting keys are available as array keys.
     *
     * @return array ['key1' => 'val1', 'key2' => 'val2']
     */
    public function getSettings()
    {
        $data = array();
        foreach ($this->settings as $setting) {
            $extracted = explode(' : ', $setting);
            $data[$extracted[0]] = $extracted[1];
        }

        return $data;
    }
}
