<?php
$serverName = "server_name";
$connectionOptions = array(
    "database" => "db_name",
    "uid" => "user_name",
    "pwd" => "password"
);

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    echo("Connection failed");

}

?>
