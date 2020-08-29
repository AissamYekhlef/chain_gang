<?php

function db_connect(){
    $connection = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    confirm_db_connect($connection);
    return $connection;

}

function confirm_db_connect($connection){
    if($connection->connect_errno){
        $msg = "Database connection failed: ";
        $msg .= $connection->connect_error;
        $msg .= " (" . $connection->connect_errno . ")";
        return $msg;
    }    

}

function db_disconnect($connection){
    if(isset($connection)){
        $connection->close();
    }
}

// not useable
function properties_and_methods(){
    // $db -mysqli object-
    $db = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    // mysqli_query($db, $sql);
    $result = $db->query($sql);

    // mysqli_real_escape_string($db, $string) // helps us to prevent SQL injection attacks.
    $db->escape_string($string);

    // mysqli_affected_rows($db)
    $db->affected_rows;

    // mysqli_insert_id($db) //return the last record id 
    $db->inset_id;


    // mysqli_fetch_assoc($result)
    $row = $result->fetch_assoc();

    // other methods for fetch
    $result->fetch_assoc(); // associative array
    $result->fetch_row(); // basic array
    $result->fetch_array(); // assoc, row, or both
    $result->fetch_object(); // crude object

    // mysqli_free_result($result)
    $result->free();

    // mysqli_num_rows($resutl)
    $result->num_rows;


    // // close connection
    // mysqli_close($db)
    $db->close();

}

?>