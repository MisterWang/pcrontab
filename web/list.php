<?php
require "../client.php";
$client=new client();
if(isset($_POST['taskname']) && isset($_POST['cmd'])){
    $client->send(protocal_command::ADD,[$_POST['taskname'],$_POST['cmd'],$_POST['time']?:'* * * * *']);
}

$data=$client->send(protocal_command::LS,'ls');
//时间间隔和命令分隔符有些冲突...
$data=explode(PROTOCAL_CMD_SP,$data);
array_shift($data);
$data=implode(' ',$data);
$data=json_decode($data,1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>任务列表</title>
</head>
<body>
    <div><table>
        <tr>
        <th>任务名称</th>
        <th>执行命令</th>
        <th>执行时间</th>
        </tr>
    <?php foreach($data as $val): ?>
        <tr>
            <td><?=$val['taskname']?></td>
            <td><?=$val['cmd']?></td>
            <td><?=$val['time']?></td>
        </tr>
    <?php endforeach; ?>
    </table>
    </div>

<br>
<br>
<br>
<form action="" method="post">
    <div>
        <div><div>任务名称</div><div><input type="text" name="taskname" /></div></div>
        <div>
            <div>执行命令</div>
            <div><textarea name="cmd" id="" cols="30" rows="10" placeholder="echo+'123'"></textarea></div>
        </div>
        <div><div>指定时间</div><div><input type="text" name="time" placeholder="* * * * *"/></div></div>

        <div><input type="submit" value="添加"></div>
    </div>
</form>
</body>
<script>
    
</script>
</html>