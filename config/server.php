<?php

return [
    /**
     * 内置 Server 配置
     *
     * 支持参数：
     * listen: 监听配置，指定 IP与端口，如 127.0.0.1:8080
     * worker_num: 工作进程数量，默认为 1
     * type: 服务器类型，支持 http, websocket
     * reuse_port: 端口复用开关，默认为 false,
     * context_options: 传递到 stream_context 中的选项
     * ssl: 是否启用 ssl，默认否
     * router: 指定使用的路由配，http 默认为 routes，websocket 默认为 websocket，Websocket 下路由为可选项
     * on_start: 服务启动回调，仅 WebSocket 可用
     * on_stop: 服务结束回调，仅 WebSocket 可用
     * on_ping: Websocket 服务接收到 ping 时的回调，当 Controller 设置了面向连接的 onPing 时，便不会触发此回调
     * on_pong: Websocket 服务接收到 pong 时的回调，当 Controller 设置了面向连接的 onPong 时，便不会触发此回调
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
            'listen' => '0.0.0.0:2347',
            'on_start' => '\App\Controller\WebSocketController::onWorkerStart',
            'on_stop' => '\App\Controller\WebSocketController::onWorkerStop'
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
    'json_options' => JSON_THROW_ON_ERROR | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE
];
