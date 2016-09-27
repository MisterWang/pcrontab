<?php
define('PROTOCAL_CMD_SP',' ');//参数分隔符
define('PROTOCAL_CMD_EOF',"\r\n");//协议结尾

function command_switch($cmd){
    $cmd=explode(PROTOCAL_CMD_SP,$cmd);
    $cmdsig=array_shift($cmd);
    return command_handle::exec($cmdsig,$cmd);
}

function check_time($str){
    $time=explode(' ',$str);
    if($time[0]=='*'||date('s')==$time[0])
        if($time[1]=='*'||date('i')==$time[1])
            if($time[2]=='*'||date('h'==$time[2]))
                if($time[3]=='*'||date('m')==$time[3])
                    if($time[4]=='*'||date('w')==$time[4])
                        return true;
    return false;
}

function is_command($cmd){
    return defined($cmd);    
}

class command_handle{
    static private $functions=[];
    static public function register($cmd,$func){
        self::$functions[$cmd]=$func;
    }
    static public function exec($cmd,$data){
        if(isset(self::$functions[$cmd])){
            $handle=self::$functions[$cmd];
            return $handle($data);
        }
        return null;
    }
}


