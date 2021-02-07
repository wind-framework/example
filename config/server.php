<?php

return [
    /**
     * 内置 Server 配置
     */
    'servers' => [
        /**
         * Http 服务器
         */
        [
            'listen' => '0.0.0.0:2345',
            'worker_num' => 2,
            'type' => 'http'
        ]
    ],
    'static_file' => [
        'document_root' => BASE_DIR.'/static',
        'enable_negotiation_cache' => true
    ],
    /**
     * Channel 服务配置
     * addr 默认为 TCP 通讯方式，绑定地址为 127.0.0.1，可指定为 unix:// 通信方式
     * port 为当使用 TCP 通讯方式时使用的端口
     */
    'channel' => [
        'enable' => true,
        'addr' => 'unix://'.sys_get_temp_dir().'/wind-'.substr(uniqid(), -8).'.sock',
        'port' => 2206
    ],
    /**
     * Task Worker 配置
     */
    'task_worker' => [
        /**
         * Task Worker 进程数量
         * 为 0 时将不启动任何 Task Worker 进程
         */
        'worker_num' => 2
    ]
];