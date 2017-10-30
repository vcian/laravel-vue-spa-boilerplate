<?php

use App\Helper\uuid;

function generateUuid()
{
    return uuid::uuid4();
}

/**
 * @return bool
 *
 * @author Sang Nguyen
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
