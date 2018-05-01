<?php

namespace App\Repositories\Configuration;

use App\Models\Configuration\Configuration;
use App\Repositories\BaseRepository;

class ConfigurationRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = Configuration::class;

    /**
     * @return mixed
     */
    public function getConfigurationData()
    {
        $config = Configuration::all()->pluck('value', 'name')->all();

        return $config;
    }

    /**
     * @param null $input
     */
    public function storeConfigurationData($input = null)
    {
        foreach ($input as $key => $value) {
            $value = (is_array($value)) ? implode(',', $value) : $value;
            $config = Configuration::firstOrNew(['name' => $key]);
            $config->value = $value;
            $config->save();
        }

        $config = Configuration::all()->pluck('value', 'name')->all();

        return $config;
    }
}