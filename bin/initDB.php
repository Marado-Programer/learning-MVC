#!/usr/bin/env php

<?php

define('INIT_FILE', dirname(__FILE__) . '/../resources/projectDB.sql');

function getDBConn()
{
    /**
     * this user needs to have some prvi
     */
    return mysqli_connect('localhost', 'mvcuser', 'mvc1user!passwd');
}

function fetchFile($file)
{
    $file = file($file);
    $file = implode('', $file);
    $file = str_replace("\r", "", $file);
    $file = str_replace("\n", "", $file);
    $file = str_replace("\t", "", $file);
    $file = str_replace("\v", "", $file);
    $file = explode(';', $file);
    array_pop($file);
    return $file;
}

function describe($query)
{
    $query = explode(' ', $query);
    $msg = "";
    switch ($query[0]) {
        case 'DROP':
            $msg .= "droping ";
            switch ($query[1]) {
                case 'DATABASE':
                    $msg .= "database ";
                    break;
            }
            break;
    }
    $msg .= "\n";

    return $msg;
}

$arg = $argv[1] ?? '-h';

switch ($arg) {
    case '--init':
    case '-i':
        $conn = getDBConn();
        foreach (fetchFile(INIT_FILE) as $i => $query) {
            echo "$i => $query\n", describe($query), "\n";
            mysqli_query($conn, $query . ';');
        }
        break;
    case '--help':
    case '-h':
    default:
        echo "Usage:\n";
        break;
}
