<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Swoole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:swoole';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->start();
    }

    public function start()
    {
        //创建websocket服务器对象，监听0.0.0.0:9502端口
        $this->ws = new \swoole_websocket_server("0.0.0.0", 9502);
        $this->ws->set(array(
            'ssl_cert_file' => '/home/wwwroot/ssl/p14fc.cn/p14fc.cn.pem',
            'ssl_key_file' => '/home/wwwroot/ssl/p14fc.cn/p14fc.cn.key;',
        ));
        //监听WebSocket连接打开事件
        $this->ws->on('open', function ($ws, $request) {
            var_dump($request->fd . "连接成功");
            // $ws->push($request->fd, "hello, welcome\n");
        });

        //监听WebSocket消息事件
        $this->ws->on('message', function ($ws, $frame) {
            // echo "Message: {$frame->data}\n";
            // $ws->push($frame->fd, "server: {$frame->data}");
            // var_dump($ws->connection_info($frame->fd));
            //fd绑定客户端传过来的标识uid
            $ws->bind($frame->fd, $frame->data);
        });

        $this->ws->on('request', function ($request, $response) {
            // 接收http请求从post获取参数
            // 获取所有连接的客户端，验证uid给指定用户推送消息
            // token验证推送来源，避免恶意访问
            if ($request->post['token'] == '333') {
                $clients = $this->ws->getClientList();
                $clientId = [];
                foreach ($clients as $value) {
                    $clientInfo = $this->ws->connection_info($value);
                    if (array_key_exists('uid', $clientInfo) && $clientInfo['uid'] == $request->post['s_id']) {
                        $clientId[] = $value;
                    }
                }
                if (!empty($clientId)) {
                    foreach ($clientId as $v) {
                        $this->ws->push($v, $request->post['info']);
                    }
                }
            }
        });

        //监听WebSocket连接关闭事件
        $this->ws->on('close', function ($ws, $fd) {
            echo "client:{$fd} is closed\n";
        });

        $this->ws->start();

    }
}
