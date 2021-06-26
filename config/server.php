<?php

return [
    /**
     * 内置 Server 配置
     *
     * 支持参数：
     * listen: 监听配置，指定 IP与端口，如 127.0.0.1:8080
     * worker_num: 工作进程数量，默认为 1
     * type: 服务器类型，目前仅支持 http
     * reuse_port: 端口复用开关，默认为 false
     */
    'servers' => [
        /**
         * Http 服务器
         */
        [
            'type' => 'http',
            'listen' => '0.0.0.0:2346',
            'worker_num' => 2,
            'reuse_port' => false
        ],
        /**
         * Websocket 服务器
         */
        [
            'type' => 'websocket',
            'listen' => '0.0.0.0:2347'
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
        'addr' => 'unix:///tmp/wind-'.substr(uniqid(), -8).'.sock',
        //'port' => 2206
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
    ],
    /**
     * JSON 选项
     *
     * 服务器会对输出的数组等进行默认 JSON 转换输出，此选项设定默认 JSON 转换时的选项。
     * 参考 json_encode() 函数
     */
    'json_options' => JSON_UNESCAPED_UNICODE
];