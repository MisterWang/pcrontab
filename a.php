<?php
require "lib/protocal.php";
//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 10086); 

//shm
$table=new swoole_table(1024*1024*2);
$table->column('taskname',$table::TYPE_STRING,16);
$table->column('value',$table::TYPE_STRING,64);
$table->create();

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {  
    echo "Client: Connect.\n";
});

//监听数据发送事件
$serv->on('receive', function ($serv, $fd, $from_id, $data){
//    $serv->send($fd, "Server: ".$data);
    $msg=unmypack($data);
    $data=command_switch($msg);
    // $serv->task($data);
    $serv->send($fd,mypack(protocal_status::SUCCESS,[$data]));
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//workstart
$serv->on('workerStart',function($serv,$worker_id){
    //只启动一个timer
    if($worker_id==0)
        $serv->tick(1000,function()use($serv){
            global $table;
            $task=$table->get('test');
            //TODO $process->write($task);
        }); 
});

//task
$serv->on('task',function($serv,$task_id,$from_id,$data){
    var_dump($data);
});

//task finished
$serv->on('finish',function($serv,$task_id,$data){

});

$serv->set(array(
    'task_worker_num'=>2,
    'woker_num'=>3,
    'package_max_length' => 8192,
    'open_eof_check'=> true,
    'package_eof' => "\r\n"
));

//注册执行方法
command_handle::register(protocal_command::LS,function(){
    global $table;
    return json_encode($table->get('test'));
    // echo 'protocal_command::LS'.PHP_EOL;
});

command_handle::register(protocal_command::ADD,function(){
    global $table;
    $table->set('test',['taskname'=>'test','value'=>'test']);
    // echo 'protocal_command::ADD'.PHP_EOL;
});

command_handle::register(protocal_command::DEL,function(){
    echo 'protocal_command::DEL'.PHP_EOL;
});

command_handle::register(protocal_command::UPD,function(){
    echo 'protocal_command::UPD'.PHP_EOL;
});

//启动服务器
$serv->start(); 
