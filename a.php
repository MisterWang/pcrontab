<?php
require "lib/protocal.php";
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 10086); 

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {  
    echo "Client: Connect.\n";
});

//监听数据发送事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
//    $serv->send($fd, "Server: ".$data);
    $msg=unmypack($data);
    command_switch($msg);
    // echo mypack(protocal_status::SUCCESS,[date("Y-m-d")]);
    $serv->send($fd,mypack(protocal_status::SUCCESS,[date("Y-m-d")]));
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$serv->set(array(
    'package_max_length' => 8192,
    'open_eof_check'=> true,
    'package_eof' => "\r\n"
));
//启动服务器
$serv->start(); 