<?php
    require_once('../model/redisvote.class.php');
    require_once('../config/redisconfig.php');
    $redis_obj=new RedisVote();
    $redis_conn=$redis_obj->connect(1);
    $problem_count=$redis_obj->dbsize();
    # config先写在这里
    #页大小
    $page_size = 10;
    #当前页码
    $page_num=(!empty($_GET['page']))?$_GET['page']:1;
    #页总数
    # ceil() 函数向上舍入为最接近的整数
    $page_count=ceil($problem_count/$page_size);
    for($i=1+$page_size*($page_num-1);$i<=$page_size*$page_num;$i++){

        if($i==$problem_count){
            break;
        }
        #这里需要先切换回数据库1
        $redis_conn=$redis_obj->connect(1);
        $runobj=$redis_obj->hval($i);
        $redis_conn=$redis_obj->connect(2);
        $record=$redis_obj->hval($i);
        $AC_count=$record['AC'];
        $total=0;
        foreach ($record as $value){
            $total+=$value;
        }
        $runobj['AC']=$AC_count;
        $runobj['total']=$total;
        $runobj['problemid']=$i;
        $data[]=$runobj;
    }
    ?>
    <table border="1">
        <tr>
            <th>problemid</th>
            <th>name</th>
            <th>level</th>
            <th>Total</th>
            <th>AC Rate</th>
        </tr>
        <?php foreach ($data as $v){?>
        <tr>
            <td>
                <?php
                echo <<<EOT
                <a href="problem.php?problemid={$v['problemid']}">{$v['problemid']}</a>
EOT;
                ?>
            </td>
            <td>
                <?php
                echo <<<EOT
                <a href="problem.php?problemid={$v['problemid']}">{$v['name']}</a>
EOT;
                ?>
            </td>
            <td><?php echo $v['level']?></td>
            <td><?php echo $v['total'];?></td>
            <td>
                <?php
                    if ($v['total'] == 0){
                        echo 0;
                    }else{
                        echo round($v['AC']/$v['total']*100,2)."％";;
                    }
                ?>
            </td>
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
                总共<?php echo $problem_count;?>条记录
            </td>
        </tr>
    </table>
