<?php

use App\Helper\uuid;

function generateUuid()
{
    return uuid::uuid4();
}

/**
 * @return bool
 *
 * @author Viral Solani
 */
function check_database_connection()
{
    try {
        DB::connection()->reconnect();

        return true;
    } catch (Exception $ex) {
        return false;
    }
}

if (!function_exists('checkDatabaseConnection')) {

    /**
     * @return bool
     */
    function checkDatabaseConnection()
    {
        try {
            DB::connection()->reconnect();

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
}

if (!function_exists('dbTrans')) {

    /**
     * @param string $lang
     * @param string $tableName
     *
     * @return string
     */
    function dbTrans(string $lang, string $tableName)
    {
        return $lang.'_'.config('table-variables.field_post_fix.'.$tableName);
    }
}

if (!function_exists('pluckDBTrans')) {

    /**
     * @param $query
     * @param string $fieldName
     *
     * @return mixed
     */
    function pluckDBTrans($query, string $fieldName)
    {
        return $query->where($fieldName, '!=', null)
            ->pluck($fieldName, 'id')
            ->toArray();
    }
}

if (!function_exists('labelManipulate')) {

    /**
     * @param string $configFileName
     * @param string $key
     *
     * @return array|bool|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function labelManipulate(string $configFileName, string $key)
    {
        try {
            return trans($configFileName.'.'.$key);
        } catch (\Exception $ex) {
            return false;
        }
    }
}

