<form method="get" action="">
    题目:<input value="A+B" name="name">
    </br>
    时间限制:<input value="1000" name="TimeLimit">
    </br>
    内存限制:<input value="256" name="MemoryLimit">
    </br>
    这里level应该设计为一个可选的select框
    </br>
    等级:<input value="0" name="level">
    </br>
    题目描述:<input value="a+b" name="description">
    </br>
    案例输入:<input value="1 2" name="input">
    </br>
    案例输出:<input value="3" name="output">
    </br>
    案例文件上传,设置为上传zip,rar等压缩格式,这样不用考略上传多个文件的情况:<input type="file" name="file">
    </br>
    暂不考略文件上传
    </br>
    <input type="submit" value="提交" name="submit">
</form>
<?php
    session_start();//一定要加上这句话
    //管理员账号
    //如果允许用户自定义题目,这里该如何判断
    if (empty($_SESSION['root'])) {
        echo "请使用管理员账号登陆";
    }
    if (isset($_GET['submit'])) {
        //这里先用GET一个个获取,考略到如果提供用户自定义题目,需要进行安全检查之类的
        $name=$_GET['name'];
        $TimeLimit=$_GET['TimeLimit'];
        $MemoryLimit=$_GET['MemoryLimit'];
        $level=$_GET['level'];
        $description=$_GET['description'];
        $input=$_GET['input'];
        $output=$_GET['output'];
        require_once('../model/redisvote.class.php');
        require_once('../config/redisconfig.php');
        $redis_obj=new RedisVote();
        $redis_conn=$redis_obj->connect(1);
        $problemid=$redis_obj->dbsize()+1;
        //这里传递的数字类型的值.储存在redis中为整型,即使php中已经转换为string?
        $array=[
            "name" => $name,
            "TimeLimit" => $TimeLimit,
            "MemoryLimit" => $MemoryLimit,
            "level" => $level,
            "description" => $description,
            "input" => $input,
            "output" => $output,
        ];
        $re=$redis_obj->hmset($problemid,$array);
        $array=[
            "WA" => 0,
            "AC" => 0,
            "TLE" => 0,
            "RTLE" => 0,
            "MLE" => 0,
            "RE" => 0,
            "SE" => 0,
            "PE" => 0,
            "OLE" => 0,
            "CE" => 0,
        ];
        $redis_conn=$redis_obj->connect(2);
        //这里1 2 数据库中problemid一致
        $re=$redis_obj->hmset($problemid,$array);
    }
?>