<?php
    session_start();
    if (!empty($_SESSION['root'])) {
        $root = $_SESSION['root'];
    }
    if (!empty($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }
    require_once('../model/redisvote.class.php');
    require_once('../config/redisconfig.php');
    $redis_obj=new RedisVote();
    $redis_conn=$redis_obj->connect(3);
    $status_count=$redis_obj->dbsize();
    # config先写在这里
    #页大小
    $page_size = 10;
    #当前页码
    $page_num=(!empty($_GET['page']))?$_GET['page']:1;
    #页总数
    # ceil() 函数向上舍入为最接近的整数
    $page_count=ceil($status_count/$page_size);
    for($i=$status_count-$page_size*($page_num-1);$i>$status_count-$page_size*$page_num;$i--){
        if($i==0){
            break;
        }
        $runobj=$redis_obj->hval($i);
        $runobj['runid']=$i;
        $data[]=$runobj;
    }
?>
<table border="1">
    <tr>
        <th>runid</th>
        <th>problemid</th>
        <th>username</th>
        <th>run-time</th>
        <th>run-memory</th>
        <th>code</th>
        <th>result</th>
        <th>time</th>
    </tr>
    <?php foreach ($data as $v){?>
    <tr>
        <td><?php echo $v['runid']?></td>
        <td><?php echo $v['problemid']?></td>
        <td><?php echo $v['username']?></td>
        <td><?php echo $v['run-time']?></td>
        <td><?php echo $v['run-memory']?></td>
        <td>
            <?php
            # a标签的title应该为gcc,g++,java等,目前只完成gcc
            if (!empty($root) || $username == $v['username']) {
                echo <<<EOT
                <a href="code.php?runid={$v['runid']}">gcc</a>
EOT;
            }else{
                echo <<<EOT
                <a href="javascript:return false;" onclick="return false;" style="cursor: default;"><i class="edit" style="opacity: 0.2">gcc</i></a>
EOT;
            }
            ?>
        </td>
        <td><?php echo $v['result']?></td>
        <td><?php echo $v['time']?></td>
        <?php }?>
    </tr>
    <tr>
        <td colspan="4">
            <?php if(($page_num-1)>=1){ ?>
            <a href="?page=<?php echo ($page_num-1);?>" rel="external nofollow" >上一页</a><?php } ?>
            <?php if(($page_num+1)<=$page_count){ ?>
            <a href="?page=<?php echo ($page_num+1);?>" rel="external nofollow" >下一页</a><?php } ?>
            <a href="?page=1" rel="external nofollow" >首页</a>
            <a href="?page=<?php echo ($page_count);?>" rel="external nofollow" >尾页</a>
            当前<?php echo $page_num;?>页
            总共<?php echo $page_count;?>页
            总共<?php echo $status_count;?>条记录
        </td>
    </tr>
</table>
