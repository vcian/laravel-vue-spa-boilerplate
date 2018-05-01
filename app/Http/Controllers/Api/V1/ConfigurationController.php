<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\Configuration\ConfigurationRepository;
use Illuminate\Http\Request;

/**
 * ConfigurationController.
 */
class ConfigurationController extends APIController
{
    /**
     * ConfigurationRepository $conf
     *
     * @var object
     */
    protected $configuration;

    /**
     * @param ConfigurationRepository $conf
     */
    public function __construct(ConfigurationRepository $conf)
    {
        $this->configuration = $conf;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $config = $this->configuration->getConfigurationData();

        return response()->json(compact('config'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $config = $this->configuration->storeConfigurationData($input);

        return response()->json(['message' => 'Configuration stored successfully!']);
    }
}
