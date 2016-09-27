<?php
if(isset($_POST['taskname']) && isset($_POST['cmd'])){
    require "../client.php";
    $client=new client();
    $client->send(protocal_command::ADD,[$_POST['taskname'],$_POST['cmd'],$_POST['time']?:'* * * * *']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加</title>
</head>
<body>
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