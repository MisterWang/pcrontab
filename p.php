<?php
//process
define('PROCESS_MAX_NUM',3);

for($i=0;$i<PROCESS_MAX_NUM;$i++){
    $process = new swoole_process('process');
    $process->name("php p.php:process $i");
    $pid = $process->start();
    $workers[$pid] = $process;
}

foreach($workers as $process){
    //子进程也会包含此事件
    swoole_event_add($process->pipe, null,function ($pipe) use($process){
        $data = $process->read();
        echo "pid:  $process->pid,RECV: $data".PHP_EOL;
    });
}

echo getmypid()."\tmaster".PHP_EOL;
function process(swoole_process $process) {// 第一个处理
    // $process->write($process->pid);
    echo $process->pid,"\t",$process->callback .PHP_EOL;
    while(0){
        $process->write($process->pid);
        usleep(1000000);
    }
    exit();
    // swoole_process::wait();
}

swoole_process::wait();