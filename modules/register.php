<form method="post" action="">
	用户名:<input type="input" name="username" value="root">
	<br>
	密码:<input type="password" name="password" value="123456">
	<br>
	<input type="submit" name="submit">
</form>
<?php
    /*
    require_once('../model/ssh.class.php');
    require_once('../config/sshconfig.php');
    $ssh=new SSH();
    $cmd='ifconfig';
    $res=$ssh->run($cmd);
    var_dump($res);
    */
?>  
<?php
	if (isset($_POST['submit'])) {
		$username=$_POST['username'];
		$password=$_POST['password'];
		echo $username.$password."</br>";
		require_once('../model/mysql.class.php');
		require_once('../config/mysqlconfig.php');
		$db=new mysql();
		$link=$db->connect2();
		//先判断该账号是否在admin表
        $sql="SELECT * FROM admin where username='$username'";
        $mysqli_num_rows=$db->getTotalRows($sql);
        echo $mysqli_num_rows;
        //注册账号为管理员账号,不允许
        if ($mysqli_num_rows > 0){
            echo "该帐号为管理员账号,请重新选择账号注册";
        }else{
            $sql="SELECT * FROM users where username='$username'";
            $mysqli_num_rows=$db->getTotalRows($sql);
            echo $mysqli_num_rows;
            if ($mysqli_num_rows == 0) {//没有注册过
                //这里需要不断拓展
                $array=[
                    "username" => $username,
                    "password" => md5($password),
                ];
                $mysqli_insert_id=$db->insert($array,'users');
                //释放结果集,考略是在类中还是这里
                //断开MYSQL
                $db->close($link);
                //头像
                $file='../images/avatar/default.jpg';
                $avatar='../images/avatar/'.$username.'.jpg';
                //这里有个权限问题
                $result=copy($file,$avatar);
                //redis中
                require_once('../model/redisvote.class.php');
                require_once('../config/redisconfig.php');
                $redis_obj=new RedisVote();
                $redis_conn=$redis_obj->connect(4);
                //默认0,题号从1开始
                $re=$redis_obj->sAdd($username,0);
                $redis_conn=$redis_obj->connect(5);
                $re=$redis_obj->sAdd($username,0);
                //弹出窗口,跳转到登陆页面
                echo <<<EOT
            <script type=text/javascript>
			 		alert('注册成功');
			 		window.location.href='login.php'; 
			</script>
EOT;
            }else{//已经被注册了
                //设计为弹出框或者红字提醒
                //若为弹出框,链接到注册页面
                //若为红字提醒,则提醒在下面
                echo "已经被注册了";
            }
        }
		/*
		//插入
		$array=[
			"username" => $username,
			"password" => $password,
		];
		$mysqli_insert_id=$db->insert($array,'users');
		*/

		/*
		//完全更新
		$array=[
			"username" => $username,
			"password" => '123',
		];
		$mysql_affected_rows=$db->update($array,'users');
		*/

		/*
		//部分更新
		$array=[
			"password" => '456',
		];
		//这里记得,数据库中字符串需要加上   ''   
		$where='username='.'\''.$username.'\'';
		$mysql_affected_rows=$db->update($array,'users',$where);
		*/

		/*
		//删除整张表的记录
		$mysql_affected_rows=$db->delete('users');
		*/

		/*
		//删除部分记录
		//这里记得,数据库中字符串需要加上   ''   
		$where='username='.'\''.$username.'\'';
		$mysql_affected_rows=$db->delete('users',$where);
		*/

		/*
		//单个查询
		//这里记得加上where语句,不然会返回表第一项记录
		$sql='SELECT * FROM users where username=\'root\'';
		$mysqli_fetch_array=$db->fetchOne($sql);
		print_r($mysqli_fetch_array);
		*/


		/*
		//查询所有记录
		$sql='SELECT * FROM users';
		$mysqli_fetch_array=$db->fetchAll($sql);
		print_r($mysqli_fetch_array);
		*/

		/*
		//记录的条数
		$sql='SELECT * FROM users';
		$mysqli_num_rows=$db->getTotalRows($sql);
		echo $mysqli_num_rows;
		*/
	}
?>