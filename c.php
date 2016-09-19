<?php
require "lib/protocal.php";

$client = new swoole_client(SWOOLE_SOCK_TCP);
$client->set(array(
    'package_max_length' => 8192,
    'open_eof_check'=> true,
    'package_eof' => "\r\n"
));
//连接到服务器
if (!$client->connect('127.0.0.1', 10086, 0.5))
{
    die("connect failed.");
}
//向服务器发送数据
$msg='hello world';
$pack=mypack(protocal_command::LS,[$msg]);
if (!$client->send($pack))
{
    die("send failed.");
}
//从服务器接收数据
$data = $client->recv();
if (!$data)
{
    die("recv failed.");
}
echo $data;
// echo unmypack($data);
//关闭连接
$client->close();