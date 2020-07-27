<?php
return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9501,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 8,
            'reload_async' => true,
            'max_wait_time'=>3
        ],
        'TASK'=>[
            'workerNum'=>4,
            'maxRunningNum'=>128,
            'timeout'=>15
        ]
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null,


    // redis配置
    'REDIS' => [
        'host'      => '127.0.0.1',
        'port'      => '6379',
        'auth'      => '',
        'db'        => null,
        'serialize' => \EasySwoole\Redis\Config\RedisConfig::SERIALIZE_NONE
    ],


    // 写数据库配置
    "MYSQL_WRITE"=>[
        'host'          => '127.0.0.1',
        'port'          => 3306,
        'user'          => 'root',
        'password'      => 'IqwyNNbDzQgRRDl1VTu9',
        'database'      => 'open.pipirui.cn',
        'timeout'       => 45,
        'charset'       => 'utf8',

        'intervalCheckTime' => 30*1000,
        'maxIdleTime'       => 15,
        'maxObjectNum'      => 20,
        'minObjectNum'      => 5,
        'getObjectTimeout'   => 3.0,
    ],
];
