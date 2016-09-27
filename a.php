<?php
require "lib/protocal.php";

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 10086); 

//shm
$table=new swoole_table(1024*1024*2);
$table->column('taskname',$table::TYPE_STRING,16);
$table->column('cmd',$table::TYPE_STRING,64);
$table->column('time',$table::TYPE_STRING,16);
$table->create();

//子进程
$process=new swoole_process(function($process){
    swoole_event_add($process->pipe,function($pipe)use($process){
        $key=$process->read();
        // echo $key.PHP_EOL;
        global $table;
        $data=$table->get($key);
        if(check_time($data['time']))
            echo exec(str_replace('+',' ',$data['cmd']));
    });
});
$pid=$process->start();

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
    $data=file_get_contents(__DIR__.'/table.json');
    $list=json_decode($data,1);
    if($list){
        global $table;
        foreach($list as $key=>$val)
            $table->set($key,$val);
    }

    //只启动一个timer
    if($worker_id==0)
        $serv->tick(4444,function()use($serv){
            global $table;
            global $process;
            //TODO $process->write($task);//不支持在timer中投递到task,只好交给子进程
            foreach($table as $key=>$row)
                $process->write($key);
        }); 
});

//task
$serv->on('task',function($serv,$task_id,$from_id,$data){
    var_dump($data);
});

//task finished
$serv->on('finish',function($serv,$task_id,$data){

});

$serv->on('shutdown',function($serv){
    global $table;
    $list=[];
    foreach($table as $key=>$val)
        $list[$key]=$val;
    file_put_contents(__DIR__."/table.json",json_encode($list));
});

$serv->set(array(
    'task_worker_num'=>2,
    'woker_num'=>3,
    'package_max_length' => 8192,
    'open_eof_check'=> true,
    'package_eof' => "\r\n",
    'daemonize'=>1,
    'log_file'=>'a.log'
));

//注册执行方法
command_handle::register(protocal_command::LS,function(){
    global $table;
    $list=[];
    foreach($table as $val)
        $list[]=$val;
    return json_encode($list);
    // echo 'protocal_command::LS'.PHP_EOL;
});

command_handle::register(protocal_command::ADD,function($data){
    global $table;
    $tname=array_shift($data);
    $key=hash('crc32b',$tname);
    $cmd=array_shift($data);
    if($data)
        $time=implode(' ',$data);
    else
        $time='* * * * *';
    $table->set($key,['taskname'=>$tname,'cmd'=>$cmd,'time'=>$time]);
    // echo 'protocal_command::ADD'.PHP_EOL;
});

command_handle::register(protocal_command::DEL,function($data){
    //echo 'protocal_command::DEL'.PHP_EOL;
    global $table;
    $tname=array_shift($data);
    $key=hash('crc32b',$tname);
    $table->del($key);
});

command_handle::register(protocal_command::UPD,function(){
    //其实和添加一样...
    echo 'protocal_command::UPD'.PHP_EOL;
});

//启动服务器
$serv->start(); 

