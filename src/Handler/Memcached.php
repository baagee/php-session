<?php
/**
 * Desc: Memcached储存Session
 * User: baagee
 * Date: 2019/4/13
 * Time: 下午9:30
 */

namespace BaAGee\Session\Handler;

use SessionHandler;

/**
 * Class Memcached
 * @package BaAGee\Session\Handler
 */
class Memcached extends SessionHandler
{
    /**
     * @var \Memcached
     */
    protected $handler = null;
    /**
     * @var array
     */
    protected $config = [
        'host' => '127.0.0.1', // memcache主机
        'port' => 11211, // memcache端口
        'expire' => 3600, // session有效期
        'timeout' => 0, // 连接超时时间（单位：毫秒）
        'session_name' => '', // memcache key前缀
        'username' => '', //账号
        'password' => '', //密码
    ];

    /**
     * Memcached constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 打开Session
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     * @throws \Exception
     */
    public function open($savePath, $sessionName)
    {
        // 检测php环境
        if (!extension_loaded('memcached')) {
            throw new \Exception('not support:memcached');
        }
        $this->handler = new \Memcached();
        // 设置连接超时时间（单位：毫秒）
        if ($this->config['timeout'] > 0) {
            $this->handler->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $this->config['timeout']);
        }
        //重要，php memcached有个bug，当get的值不存在，有固定40ms延迟，开启这个参数，可以避免这个bug
        $this->handler->setOption(\Memcached::OPT_TCP_NODELAY, true);
        // 支持集群
        $hosts = explode(',', $this->config['host']);
        $ports = explode(',', $this->config['port']);
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        // 建立连接
        $servers = [];
        foreach ((array)$hosts as $i => $host) {
            $servers[] = [$host, (isset($ports[$i]) ? $ports[$i] : $ports[0]), 1];
        }
        $this->handler->addServers($servers);
        if ('' != $this->config['username']) {
            $this->handler->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
            $this->handler->setSaslAuthData($this->config['username'], $this->config['password']);
        }
        return true;
    }

    /**
     * 关闭Session
     * @access public
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->handler->quit();
        $this->handler = null;
        return true;
    }

    /**
     * 读取Session
     * @param string $sessionId
     * @return string
     */
    public function read($sessionId)
    {
        return (string)$this->handler->get($this->config['session_name'] . $sessionId);
    }

    /**
     * 写入Session
     * @access public
     * @param string $sessionId
     * @param String $sessionData
     * @return bool
     */
    public function write($sessionId, $sessionData)
    {
        return $this->handler->set($this->config['session_name'] . $sessionId, $sessionData, $this->config['expire']);
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessionId
     * @return bool
     */
    public function destroy($sessionId)
    {
        $this->handler->delete($this->config['session_name'] . $sessionId);
        return true;
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessionMaxLifeTime
     * @return true
     */
    public function gc($sessionMaxLifeTime)
    {
        return true;
    }
}
