<?php

$http = new swoole_http_server("127.0.0.1", 8000);
//$http = new swoole_http_server("192.168.126.168", 8000);
$http->on('request', function (swoole_http_request $request, swoole_http_response $response) {
    $response->status(200);
    $response->end('hello world.');
});
$http->start();
