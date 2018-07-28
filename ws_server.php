<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/28
 * Time: 9:54
 */
//创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new swoole_websocket_server("0.0.0.0", 9504);

$fd = [];

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) use ($fd) {
    $fd[] = $request->fd;
    var_dump($request->fd, $request->get, $request->server);
    $ws->push($request->fd, "hello, welcome\n");
});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    echo "Message: {$frame->data}\n";
    swoole_timer_tick(2000, function ($timer_id) use ($ws, $frame) {
        $ws->push($frame->fd, "timer: " . $frame->data);
    });
    $ws->push($frame->fd, "server: {$frame->data}");
});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();