<?php
session_start();
require_once 'anyDB.php';
require_once 'addon/DBHelper.php';
require_once 'addon/QueryHelper.php';

$database = 'brookefield_hosp';
$host = 'localhost';
$user = 'brookefield_hosp';
$password = '@May22-2017';
$dbType = 'mysql';

// create a new db layer
$db = anyDB::getLayer('MYSQL','', $dbType);

$db->connect($host, $database, $user, $password) or die(mysql_error());
$updationDate=date("Y-m-d H:i:s",time());
$rootpath="http://www.brookefieldhospital.com/";

// ---------- Cookie Info ---------- //
$cookie_name = 'brookefield';
$cookie_time = (3600 * 24 * 30); // 30 days
$enKey='$^%$';
$hostmode='PROD';
//$hostmode='Local';
?>
