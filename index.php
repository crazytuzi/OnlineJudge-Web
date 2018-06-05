<a href="modules/register.php">注册</a>
<a href="modules/login.php">登陆</a>
<a href="modules/status.php">状态</a>
<a href="modules/problem.php?problemid=1">问题</a>
<a href="modules/problems.php">问题列表</a>
<a href="modules/addproblem.php">添加题目</a>
<a href="modules/status.php">状态</a>
<?php
//redis中集合添加元素
/*
require_once('model/redisvote.class.php');
require_once('config/redisconfig.php');
$redis_obj=new RedisVote();
$redis_conn=$redis_obj->connect(4);
$redis_obj->sAdd('2',1);
$re=$redis_obj->sMembers('2');
var_dump($re);
*/
?>
<?php
    session_start();//一定要加上这句话
    //管理员账号
    if (!empty($_SESSION['root'])){
        $root=$_SESSION['root'];
        echo "管理员登陆";
    }else{
        //普通用户
        if (!empty($_SESSION['username'])){
            $username=$_SESSION['username'];
            echo $username;
            echo <<<EOT
           <input type="image" src="images/avatar/{$username}.jpg">
EOT;
            //
            echo <<<EOT
            <form action="" method="get">
                <input type="submit" value="logout" name="logout">
            </form>
EOT;
        }else{
            echo "没有登陆";
        }
    }
?>
<?php
    if (isset($_GET["logout"])){
        //这里没有实时刷新,先处理为直接跳转到index.
        unset($_SESSION['root']);
        unset($_SESSION['username']);
        echo <<<EOT
            <script type=text/javascript>
			 		window.location.href='../index.php'; 
			</script>
EOT;
    }
?>