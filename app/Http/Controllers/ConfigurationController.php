<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function store(Request $request)
    {
        $input = $request->all();
        foreach ($input as $key => $value) {
            $value = (is_array($value)) ? implode(',', $value) : $value;
            $config = Configuration::firstOrNew(['name' => $key]);
            $config->value = $value;
            $config->save();
        }

        $config = Configuration::all()->pluck('value', 'name')->all();

        return response()->json(['message' => 'Configuration stored successfully!']);
    }

    public function index()
    {
        $config = Configuration::all()->pluck('value', 'name')->all();

        return response()->json(compact('config'));
    }
}
