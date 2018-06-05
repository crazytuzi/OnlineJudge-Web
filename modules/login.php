<form method="post" action="">
	用户名:<input type="input" name="username" value="<?php echo $_COOKIE['username'];?>">
	密码:<input type="password" name="password" value="<?php echo $_COOKIE['password'];?>">
	<input type="submit" name="submit">
</form>
<?php
	if (isset($_POST['submit'])) {
		$username=$_POST['username'];
		$password=$_POST['password'];
		#$password_md5=md5($password);
        $password_md5=$password;
		echo $username.$password."</br>";
		require_once('../model/mysql.class.php');
		require_once('../config/mysqlconfig.php');
		$db=new mysql();
		$link=$db->connect2();
		//先判断是否为管理员登陆
        $sql="SELECT * FROM admin where username='$username' and password='$password_md5'";
        $mysqli_num_rows=$db->getTotalRows($sql);
        if ($mysqli_num_rows>0){
            session_start();
            $_SESSION['root']=$username;
            //保存Session
            //弹出窗口,跳转到首页
            echo <<<EOT
            <script type=text/javascript>
			 		window.location.href='../index.php'; 
			</script>
EOT;
        }else{
            $sql="SELECT * FROM users where username='$username' and password='$password_md5'";
            $mysqli_num_rows=$db->getTotalRows($sql);
            echo $mysqli_num_rows;
            //同时验证用户名和密码,更为安全,代码也更为简洁
            if ($mysqli_num_rows == 0) {//登陆失败
                echo "用户名或者密码错误";
                //释放结果集,考略是在类中还是这里
                //断开MYSQL
                $db->close($link);

            }else{//登陆成功
                //释放结果集,考略是在类中还是这里
                //断开MYSQL
                $db->close($link);
                session_start();
                $_SESSION["username"]=$username;
                setcookie("username",$username,time()+3600*24*365);
                setcookie("password",$password,time()+3600*24*365);
                //保存Session
                //弹出窗口,跳转到首页
                echo <<<EOT
            <script type=text/javascript>
			 		window.location.href='../index.php'; 
			</script>
EOT;
            }
        }
	}
?>