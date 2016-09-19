<?php
define('PROTOCAL_CMD_SP',' ');//参数分隔符
define('PROTOCAL_CMD_EOF',"\r\n");//协议结尾

function command_switch($cmd){
    $cmd=explode(PROTOCAL_CMD_SP,$cmd);
    $cmdsig=array_shift($cmd);
    command_handle::exec($cmdsig);
}

function is_command($cmd){
    return defined($cmd);    
}

class command_handle{
    static private $functions=[];
    static public function register($cmd,$func){
        self::$functions[$cmd]=$func;
    }
    static public function exec($cmd){
        if(isset(self::$functions[$cmd])){
            $handle=self::$functions[$cmd];
            $handle();
        }
    }
}

//注册执行方法
command_handle::register(protocal_command::LS,function(){
    echo 'protocal_command::LS';
});

command_handle::register(protocal_command::ADD,function(){
    echo 'protocal_command::ADD';
});

command_handle::register(protocal_command::DEL,function(){
    echo 'protocal_command::DEL';
});

command_handle::register(protocal_command::UPD,function(){
    echo 'protocal_command::UPD';
});

