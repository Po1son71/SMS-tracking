<?php
$serverName = "nexusworld.database.windows.net";
$connectionOptions = array(
    "database" => "nexuspos",
    "uid" => "skynexus",
    "pwd" => "Snx193#*"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo("Connection failed");

}

?>