<?php
require "command.php";

class protocal_command{
    const LS=0x10;//列表
    const ADD=0X11;//添加
    const DEL=0X12;//删除
    const UPD=0x13;//修改
}

class protocal_status{
    const SUCCESS=0X1;
    const ERROR=0X2;
    const UNKNOWCMD=0X3;//未知命令
    const UNKNOW=0X4;   //未知
}

function unmypack($str){
    // $len=unpack('n',$str)[1];
    // return substr($str,-$len);
    return trim($str,PROTOCAL_CMD_EOF);
}
function mypack($cmd,$value=[]){
    // return pack('n',strlen($str)).$str;
    return $cmd.PROTOCAL_CMD_SP.implode(PROTOCAL_CMD_SP,$value).PROTOCAL_CMD_EOF;
}

// echo mypack(protocal_command::LS).PHP_EOL;
// echo mypack(protocal_command::ADD,["taskname","path(or netword address)"]).PHP_EOL;
// echo mypack(protocal_command::DEL,["taskname"]).PHP_EOL;
// echo mypack(protocal_command::UPD,["taskname","value"]).PHP_EOL;

// var_dump(is_command('protocal_command::LSS'));

// command_switch(mypack(protocal_command::LS));
// command_switch(mypack(protocal_command::ADD));
// command_switch(mypack(protocal_command::UPD));
// command_switch(mypack(protocal_command::DEL));