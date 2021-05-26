<?php
    $dbConn = new mysqli('localhost', 'twa106', 'twa106XX', 'A_League2021_106');
    if ($dbConn->connect_error) {
        die('Connection error (' . $dbConn->connect_errno . ')' . $dbConn->connect_error);
    }
?>