<?php
class RedisVote{
	public $redis_obj=null;//redis实例化时静态变量
	public function connect($id=0){
		$this->redis_obj=new Redis();
		$this->redis_obj->connect(REDIS_HOST,REDIS_PORT);
		$this->redis_obj->auth(REDIS_PWD);
		$this->redis_obj->select($id);
		//echo $this->redis_obj->ping();
		return $this->redis_obj;
	}

    public function sAdd($key,$value)
    {
        if(!is_array($value))
            $arr=array($value);
        else
            $arr=$value;
        foreach($arr as $row)
            $this->redis_obj->sAdd($key,$row);
        return true;
    }

    public function sMembers($key)
    {
        return $this->redis_obj->sMembers($key);
    }

    /**
     * 在队列尾部插入一个元素
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public function rPush($key,$value)
    {
        return $this->redis_obj->rPush($key,$value);
    }

    /**
     * 在队列尾部插入一个元素 如果key不存在，什么也不做
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public function rPushx($key,$value)
    {
        return $this->redis_obj->rPushx($key,$value);
    }

    /**
     * 在队列头部插入一个元素
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public function lPush($key,$value)
    {
        return $this->redis_obj->lPush($key,$value);
    }

    /**
     * 在队列头插入一个元素 如果key不存在，什么也不做
     * @param unknown $key
     * @param unknown $value
     * 返回队列长度
     */
    public function lPushx($key,$value)
    {
        return $this->redis_obj->lPushx($key,$value);
    }

    /**
     * 返回队列长度
     * @param unknown $key
     */
    public function lLen($key)
    {
        return $this->redis_obj->lLen($key);
    }

    /**
     * 返回队列指定区间的元素
     * @param unknown $key
     * @param unknown $start
     * @param unknown $end
     */
    public function lRange($key,$start,$end)
    {
        return $this->redis_obj->lrange($key,$start,$end);
    }

    /**
     * 返回队列中指定索引的元素
     * @param unknown $key
     * @param unknown $index
     */
    public function lIndex($key,$index)
    {
        return $this->redis_obj->lIndex($key,$index);
    }

    /**
     * 设定队列中指定index的值。
     * @param unknown $key
     * @param unknown $index
     * @param unknown $value
     */
    public function lSet($key,$index,$value)
    {
        return $this->redis_obj->lSet($key,$index,$value);
    }


    /**
     * 增，以json格式插入数据到缓存,hash类型 
     * @param $redis_key |array , $redis_key['key']数据库的表名称;$redis_key['field'],下标key 
     * @param $token,该活动的token，用于区分标识 
     * @param $id,该活动的ID，用于区分标识 
     * @param $data|array ，要插入的数据, 
     * @param $timeOut ，过期时间，默认为0 
     * @return $number 插入成功返回1【,更新操作返回0】 
     */  
    public function hset_json($redis_key,$token,$id,$data,$timeOut = 0){  
        $redis_table_name = $redis_key['key'].':'.$token;           //key的名称  
        $redis_key_name = $redis_key['field'].':'.$id;              //field的名称，表示第几个活动  
        $redis_info = json_encode($data);                           //field的数据value，以json的形式存储  
        $re = $this->redis_obj -> hSet($redis_table_name,$redis_key_name,$redis_info);//存入缓存  
        if ($timeOut > 0) $this->redis_obj->expire($redis_table_name, $timeOut);//设置过期时间  
        return $re;  
    }  
  
    /** 
     * 查，json形式存储的哈希缓存，有值则返回;无值则查询数据库并存入缓存 
     * @param $redis,$redis['key'],$redis['field']分别是hash的表名称和键值 
     * @param $token,$token为公众号 
     * @param $token,$id为活动ID 
     * @return bool|array, 成功返回要查询的信息，失败或不存在返回false 
     */  
    public function hget_json($redis_key,$token,$id){  
        $re =   $this->redis_obj->hexists($redis_key['key'].':'.$token,$redis_key['field'].':'.$id);//返回缓存中该hash类型的field是否存在  
        if($re){  
            $info = $this->redis_obj->hget($redis_key['key'].':'.$token,$redis_key['field'].':'.$id);  
            $info = json_decode($info,true);  
        }else{  
            $info = false;  
        }  
        return $info;  
    }  
  
    /** 
     * 增，普通逻辑的插入hash数据类型的值 
     * @param $key ,键名 
     * @param $data |array 一维数组，要存储的数据 
     * @param $timeOut |num  过期时间 
     * @return $number 返回OK【更新和插入操作都返回ok】 
     */  
    public function hmset($key,$data,$timeOut=0){  
        $re = $this->redis_obj  -> hmset($key,$data);  
        if ($timeOut > 0) $this->redis_obj->expire($key, $timeOut);  
        return $re;  
    }  
  
    /** 
     * 查，普通的获取值 
     * @param $key,表示该hash的下标值 
     * @return array 。成功返回查询的数组信息，不存在信息返回false 
     */  
    public function hval($key){  
        $re =   $this->redis_obj->exists($key);//存在返回1，不存在返回0  
        if(!$re) return false;  
        $vals = $this->redis_obj -> hvals($key);  
        $keys = $this->redis_obj -> hkeys($key);  
        $re = array_combine($keys,$vals);  
        foreach($re as $k=>$v){  
            if(!is_null(json_decode($v))){  
                $re[$k] = json_decode($v,true);//true表示把json返回成数组  
            }  
        }  
        return $re;  
    }  
  
    /** 
     * 
     * @param $key 
     * @param $filed 
     * @return bool|string 
     */  
    public function hget($key,$filed){  
        $re = $this->redis_obj->hget($key,$filed);  
        if(!$re){  
            return false;  
        }  
        return $re;  
    }  

    /** 
     * 删 
     * @param $key,表示该hash的下标值 
     * @return boolean 。删除成功返回true，不存在信息返回false 
     */  
    public function del($key){
    	$re=$this->redis_obj->del($key);
    	return $re;
    }

    /** 
     * 统计个数 
     * @param 
     * @return int 。返回当前数据库的keys个数
     */ 
    public function dbsize(){
    	//这里用的dbsize(),资料上显示,这样统计不是很精确,但是当keys数量很多时,直接keys *会导致数据库卡死
    	$count=$this->redis_obj->dbsize();
    	return $count;
    }
}  
?>