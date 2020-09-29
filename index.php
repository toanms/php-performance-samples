<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use React\EventLoop\Factory;
use React\ChildProcess\Process;

ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 300);

require './vendor/autoload.php';
include 'helper.php';

$reportCount = num_cpu();
$reportnum = 1;
session_start();

if (!isset($_SESSION['userList'])){
    $_SESSION['userList'] = array();
}

if (!isset($_SESSION['tmp'])){
    $_SESSION['tmp'] = 2000;
}

if (!isset($_SESSION['pid'])){
    $_SESSION['pid'] = 0;
}

$app = new \Slim\App();
$container = $app->getContainer();

$app->get('/', function ($request, $response, $args) {
    $jsonResponse = array("response" => "PHP Rest API");
    return $response->withHeader('Content-type', 'application/json')->withJson($jsonResponse, 200);
});

function generateid ($userID) {
    $numbers = $userID / 8;
    $arr = array();
    for ($i = 0; $i < $userID; $i++) {
        array_push($arr, $i);
    }
    return $arr;
};

$app->get('/memory', function ($request, $response, $args) {
    $id = floor(rand(0,100) * 5) + 1;
    if (defined ( 'PHP_WINDOWS_VERSION_MAJOR' )) {   
        $userID = 10000 * 2048;
    } else {
        $userID = (25000 + $_SESSION['tmp']) * 2024;
        $_SESSION['tmp'] = $_SESSION['tmp'] + 1500;
    }
    $user = generateid($userID);
    array_push($_SESSION['userList'],$user);
    $jsonResponse = array("response" => "User Added Num: {$id}");
    return $response->withHeader('Content-type', 'application/json')->withJson($jsonResponse, 200);
});

$app->get('/cpu', function ($request, $response, $args) {
    global $reportCount;
    global $reportnum;
    $jsonResponse = array("response" => "Report generated in background.");

    if ($reportCount >= 3) {
        $reportloop = $reportCount-2;
        for ($report = 1; $report <= $reportloop; $report++) {
            $loop = React\EventLoop\Factory::create();
            $command="php reportchild.php";
            if (defined ( 'PHP_WINDOWS_VERSION_MAJOR' )) {    
                $process = new Process($command, null, null, array());
                $process->start($loop);
                $process->on('exit', function($exitCode, $termSignal) {
                    echo 'Process exited with code ' . $exitCode . PHP_EOL;
                });
            } else {
                $process = new Process($command, null, null, array());
                $process->start($loop);
                $process->on('exit', function($exitCode, $termSignal) {
                    echo 'Process exited with code ' . $exitCode . PHP_EOL;
                });
            }
        }
    }

    for ($index = 1; $index < 2147483640; $index++) {
        $reportnum = $reportnum * $index;
    }

    return $response->withHeader('Content-type', 'application/json')->withJson($jsonResponse, 200);
});

$app->run();
