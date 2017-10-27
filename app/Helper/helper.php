<?php
function generateUuid(){
  return \App\Helper\UUID::uuid4();
}

/**
 * @return boolean
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
