<?php

$servers = Array();

$server = Array();
$server['name'] = "Matias";
$server['ip'] = "192.168.0.12";
$server['port'] = "8069";
$server['username'] = "admin";
$server['password'] = "1234";
$server['db'] = "gcmovil";
$servers[] = $server;

$server['name'] = "Netbook";
$server['ip'] = "10.0.0.1";
$server['port'] = "8069";
$server['username'] = "admin";
$server['password'] = "1234";
$server['db'] = "gcmovil";
$servers[] = $server;


print_r(json_encode($servers));

?>
