<script src="../lib/codemirror-5.35.0/lib/codemirror.js"></script>
<link rel="stylesheet" href="../lib/codemirror-5.35.0/lib/codemirror.css">
<script src="../lib/codemirror-5.35.0/mode/clike/clike.js"></script>
<script src="../lib/codemirror-5.35.0/addon/edit/matchbrackets.js"></script>
<?php
    session_start();
    if (!empty($_SESSION['root'])) {
        $root = $_SESSION['root'];
    }
    if (!empty($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }
    $runid=$_GET['runid'];
    require_once('../model/redisvote.class.php');
    require_once('../config/redisconfig.php');
    $redis_obj=new RedisVote();
    $redis_conn=$redis_obj->connect(3);
    $runobj=$redis_obj->hval($runid);
    if (!empty($root) || $username == $runobj['username']) {
        ?>
        <table border="1">
            <tr>
                <th>problemid</th>
                <th>username</th>
                <th>run-time</th>
                <th>run-memory</th>
                <th>result</th>
                <th>time</th>
            </tr>
            <tr>
                <td><?php echo $runobj['problemid']?></td>
                <td><?php echo $runobj['username']?></td>
                <td><?php echo $runobj['run-time']?></td>
                <td><?php echo $runobj['run-memory']?></td>
                <td><?php echo $runobj['result']?></td>
                <td><?php echo $runobj['time']?></td>
            </tr>
        </table>
        <textarea id="c-code" name="code"><?php echo $runobj['code'];?></textarea>
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
    }else {
        echo <<<EOT
            <script type=text/javascript>
			 		alert('不允许的操作');
			 		window.location.href='status.php'; 
			</script>
EOT;
    }
?>