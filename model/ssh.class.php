<?php

class SSH
{
    /*
    public static $config = [
        'host'      => '127.0.0.1',
        'port'      => '22',
        'username'  => 'root',
        'password'  => '123456'
    ];
    */

    /**
     * 执行操作
     * @param string $cmd 执行的命令
     * @param array $config 连接配置文件, 具体参考上边的 $config 公共变量
     * @return mixed
     */
    public static function run($cmd='')
    {
        $config = [
            'host'      => SSH_HOST,
            'port'      => SSH_PORT,
            'username'  => SSH_USER,
            'password'  => SSH_PWD
        ];
        if (empty($config)) {
            $config = static::$config;
        }

        // 连接服务器
        $connection=ssh2_connect($config['host'], $config['port']);

        // 身份验证
        ssh2_auth_password($connection, $config['username'], $config['password']);

        // 执行命令
        $ret=ssh2_exec($connection, $cmd);

        // 获取结果
        stream_set_blocking($ret, true);

        // 返回结果
        return stream_get_contents($ret);
    }
}