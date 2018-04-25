<?php

class WebSocketServer
{
    private $_serv;
    public $key = '^manks.top&swoole$';

    public function __construct()
    {
        $this->_serv = new swoole_websocket_server("192.168.153.128", 9501);
        $this->_serv->set([
		'worker_num' => 1,
		'heartbeat_check_interval' => 1,//心跳检测 N秒进行一次
    		'heartbeat_idle_time' => 15, //M 客户端被检测后M秒无活动视为无效
        ]);
        $this->_serv->on('open', [$this, 'onOpen']);
        $this->_serv->on('message', [$this, 'onMessage']);
        $this->_serv->on('close', [$this, 'onClose']);
    }

    /**
     * @param $serv
     * @param $request
     */
    public function onOpen($serv, $request)
    {
        //echo "server: handshake success with fd{$request->fd}.\n";
	$this->checkAccess($serv, $request);
    }

    /**
     * @param $serv
     * @param $frame
     */
    public function onMessage($serv, $frame)
    {
        //$serv->push($frame->fd, "server received data :{$frame->data}");
   	// 循环当前的所有连接，并把接收到的客户端信息全部发送
    	 foreach ($serv->connections as $fd) {
       		 $serv->push($fd, $frame->data);
   	 } 
    }
    public function onClose($serv, $fd)
    {
        echo "client {$fd} closed.\n";
    }

    /**
     * 校验客户端连接的合法性,无效的连接不允许连接
     * @param $serv
     * @param $request
     * @return mixed
     */
    public function checkAccess($serv, $request)
    {
        // get不存在或者uid和token有一项不存在，关闭当前连接
        if (!isset($request->get) || !isset($request->get['uid']) || !isset($request->get['token'])) {
            $this->_serv->close($request->fd);
            return false;
        }
        $uid = $request->get['uid'];
        $token = $request->get['token'];
        // 校验token是否正确,无效关闭连接
        if (md5(md5($uid) . $this->key) != $token) {
            $this->_serv->close($request->fd);
            return false;
        }
    }

    public function start()
    {
        $this->_serv->start();
    }
}

$server = new WebSocketServer;
$server->start();
