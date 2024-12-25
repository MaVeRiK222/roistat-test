<?php



header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

date_default_timezone_set('Europe/Moscow');

ini_set('log_errors', 1);
ini_set('error_reporting', E_ALL);
ini_set('error_log', __DIR__ . '/logs/phpErrors__' . basename(__FILE__, '.php') . '.log');

require_once __DIR__ . '/init.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use AmoCRM\Client\AmoCRMApiClient;

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    http_response_code(400);
    die;
}

$log = new Logger('Form Handler');
$log->pushHandler(new StreamHandler('logs/' . basename(__FILE__, '.php') . '.log'));

$data = $_POST;

if ((!is_array($data) || count($data) < 4)) {
    http_response_code(400);
    die;
}

$apiClient = new AmoCRMApiClient();

$formHandler = new FormHandler($data, $apiClient);
$formHandler->process($log);
