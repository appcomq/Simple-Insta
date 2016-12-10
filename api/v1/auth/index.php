<?php
require '../vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'abc';

if (preg_match('/http(s)?:\/\/(.*\.)?(tuneout.in)/', $http_origin) || preg_match('/http(s)?:\/\/(.*\.)?(tuneout)([A-Za-z]+)$/', $http_origin))
{
  header('Access-Control-Allow-Origin: '.$http_origin);
} else {
  header('Access-Control-Allow-Origin: https://tuneout.in');
  die();
}
header('Expires: -1');
header('Cache-Control: must-revalidate, private');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: x-session-pass');

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
  die();
}

require_once '../Rest.inc.php';
$data = RestUtils::processRequest();

require_once 'auth.php';
$rest = new AuthTuneout($data);

$format = isset($data->getRequestVars()['format']) ? $data->getRequestVars()['format'] : 'json';
$exec = intval((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000) + 1;
$rest->addtoPHP('time_taken', $exec);
$rest->addtoPHP('request_time', $_SERVER['REQUEST_TIME_FLOAT']);
$accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : 0;
if ($format == 'xml' || $accept == 'application/xhtml+xml' || $accept == 'application/xml') {
    header('Content-Type: text/xml');
    $rest->printXML();
} else {
    header('Content-Type: application/json');
    $rest->printJSON();
}
?>