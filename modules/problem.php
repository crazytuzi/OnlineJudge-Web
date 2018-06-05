<script src="../lib/codemirror-5.35.0/lib/codemirror.js"></script>
<link rel="stylesheet" href="../lib/codemirror-5.35.0/lib/codemirror.css">
<script src="../lib/codemirror-5.35.0/mode/clike/clike.js"></script>
<script src="../lib/codemirror-5.35.0/addon/edit/matchbrackets.js"></script>
<?php
    //从点击页面获取问题id
    session_start();
    if (!empty($_SESSION['root'])) {
        $root = $_SESSION['root'];
        //管理员登陆的时候设置为不可提交题目
        echo "管理员登陆";
    }
    //先获取到题目id
    $problemid=$_GET['problemid'];
    //echo $problemid;
    //先执行数据库操作获取题目相关数据
    require_once('../model/redisvote.class.php');
    require_once('../config/redisconfig.php');
    $redis_obj=new RedisVote();
    $redis_conn=$redis_obj->connect(1);
    $re=$redis_obj->hval($problemid);
    //var_dump($re);
    $name=$re['name'];
    $TimeLimit=$re['TimeLimit'];
    $MemoryLimit=$re['MemoryLimit'];
    $level=$re['level'];
    $description=$re['description'];
    $TimeLimit=$re['TimeLimit'];
    $input=$re['input'];
    $output=$re['output'];
    $redis_conn=$redis_obj->connect(2);
    $record=$redis_obj->hval($problemid);
    $AC=$record['AC'];
    $WA=$record['WA'];
    $RE=$record['RE'];
    $TLE=$record['TLE'];
    $total=0;
    foreach ($record as $value){
        $total+=$value;
    }
?>
题目:<input value="<?php echo $name;?>" name="name">
</br>
时间限制:<input value="<?php echo $TimeLimit;?>" name="TimeLimit">
</br>
内存限制:<input value="<?php echo $MemoryLimit;?>" name="MemoryLimit">
</br>
等级:<input value="<?php echo $level;?>" name="level">
</br>
题目描述:<input value="<?php echo $description;?>" name="description">
</br>
案例输入:<input value="<?php echo $input;?>" name="input">
</br>
案例输出:<input value="<?php echo $output;?>" name="output">
</br>
AC:<input value="<?php echo $AC;?>">
</br>
WA:<input value="<?php echo $WA;?>">
</br>
RE:<input value="<?php echo $RE;?>">
</br>
TLE:<input value="<?php echo $TLE;?>">
</br>
Other:<input value="<?php echo $total-$AC-$WA-$RE-$TLE;?>">
<form method="post" action="">
	<?php
	$file_path = "example.txt";
	if(file_exists($file_path)){
		$str = file_get_contents($file_path);//将整个文件内容读入到一个字符串中
		//$str = str_replace("\r\n","<br />",$str);
		//echo $str;
	}
	?>
	<textarea id="c-code" name="code"><?php echo $str;?></textarea>
	<input type="submit" name="submit">
</form>


<script type="text/javascript">
//根据DOM元素的id构造出一个编辑器
    var cEditor = CodeMirror.fromTextArea(document.getElementById("c-code"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-csrc"
      });
    /*
    var cEditor = CodeMirror.fromTextArea(document.getElementById("c-code"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-csrc"
      });
      var cppEditor = CodeMirror.fromTextArea(document.getElementById("cpp-code"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-c++src"
      });
      var javaEditor = CodeMirror.fromTextArea(document.getElementById("java-code"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "text/x-java"
      });
    */
</script>

<?php
	if (isset($_POST['submit'])) {
        //将时区设置为北京时间
        date_default_timezone_set('PRC');
        $time=date('Y-m-d H:i:s',time());
	    //一定要先开启!!!session_start();
	    $username=$_SESSION['username'];
	    //没有登陆就提交的情况
	    if (empty($username)){
	        echo "请先登陆";
        }else{
	        $code=$_POST['code'];
	        //这里默认就行,不需要转换?
            #$code=nl2br($_POST['code']);
            //echo $code;
            //这里没有具体明确nl2br和str_replace的区别,用法应该等同
            //$test=str_replace(" "," ",str_replace("\n","<br/>",$_POST['code']));
            require_once('../model/redisvote.class.php');
            require_once('../config/redisconfig.php');
            $redis_obj=new RedisVote();
            $redis_conn=$redis_obj->connect(3);
            $runid=$redis_obj->dbsize()+1;
            //codelength language暂时不加入
            $array=[
                "problemid" => $problemid,
                "username" => $username,
                "code" => $code,
                "time" => $time,
                "result" => -2,
            ];
            $re=$redis_obj->hmset($runid,$array);
            $redis_conn=$redis_obj->connect(0);
            /*
             *
             * enum {
                    WRONG_ANSWER = -1,
                    ACCEPTED = 0,
                    CPU_TIME_LIMIT_EXCEEDED = 1,
                    REAL_TIME_LIMIT_EXCEEDED = 2,
                    MEMORY_LIMIT_EXCEEDED = 3,
                    RUNTIME_ERROR = 4,
                    SYSTEM_ERROR = 5,
                    PRESENTATION_ERROR = 6,
                    OUTPUT_LIMIT_EXCEEDED =7
                };
            设定-2为正在编译
             */
            /*
             * 这里可以直接把编译限制时间和限制内存传递过去,减少读取redis
             * 目前只支持C语言编译,这里先设计成这些
             */
            //status=0代表没有被运行过
            $array=[
                "problemid" => $problemid,
                "username" => $username,
                "code" => $code,
                "status" => 0,
                "TimeLimit" => $TimeLimit,
                "MemoryLimit" => $MemoryLimit,
            ];
            $re=$redis_obj->hmset($runid,$array);
        }

		/*
		//增加 
		$array=[
				"username" => "222",
				"password" => "33333",
			];
		$re=$redis_obj->hmset("2",$array);
		*/


		/*
		//查询
		$re=$redis_obj->hval(3);
		var_dump($re);
        */

		/*
		//删除
		$re=$redis_obj->del("2");
		*/


		/*
		$problemid=1;
		//Runid 题目id 代码 状态 时间 空间 语言
		$runid=$redis_obj->dbsize()+1;
		$array=[
				"problemid" => $problemid,
				"username" => $username,
				"code" => $code,
				"state" => 0,
				"Timelimit" => 1000,
				"Memory limit" => 65535,
		];
		$re=$redis_obj->hmset($runid,$array);
		*/
	}
?>