<?php

$ws = new swoole_websocket_server("192.168.153.128", 9502);//SWOOLE_SSL  需要ssl才加
//$ws->set(array(
//    'ssl_cert_file' => CERT_PATH.'/XXX.crt',
//    'ssl_key_file' => CERT_PATH.'/XXX.key',
//));//如果需要 ssl的话 需要添加证书 否则去掉这段代码
//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {
    $fd[] = $request->fd;
    $GLOBALS['fd'][] = $fd;
    $ws->push($request->fd, "hello, welcome\n");
});
//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    $msg = 'from' . $frame->fd . ":{$frame->data}\n";
    foreach ($GLOBALS['fd'] as $aa) {
        foreach ($aa as $i) {
            $ws->push($i, $msg);
        }
    }
});
//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});
$ws->start();