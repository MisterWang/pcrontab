<?php
require __DIR__."/lib/protocal.php";
class client{
    private $client;
    public function __construct(){
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

        $this->client=$client;
    }
    public function __destruct(){
        $this->client->close();
    }

    public function addtask(){

    }

    public function deltask(){

    }

    public function ls(){

    }

    public function send($cmd,$msg){
        $client=$this->client;

        if(!is_array($msg))
            $msg=[$msg];
        $pack=mypack($cmd,$msg);
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
        return unmypack($data);
    }
}