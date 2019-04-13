<?php
/**
 * Desc: Memcache储存Session
 * User: baagee
 * Date: 2019/4/13
 * Time: 下午9:25
 */

namespace BaAGee\Session\Handler;

use \SessionHandler;

/**
 * Class Memcache
 * @package BaAGee\Session\Handler
 */
class Memcache extends SessionHandler
{
    /**
     * @var \Memcache
     */
    protected $handler = null;

    /**
     * @var array
     */
    protected $config = [
        'host'         => '127.0.0.1', // memcache主机
        'port'         => 11211, // memcache端口
        'expire'       => 3600, // session有效期
        'timeout'      => 0, // 连接超时时间（单位：毫秒）
        'persistent'   => true, // 长连接
        'session_name' => '', // memcache key前缀
    ];

    /**
     * Memcache constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 打开Session
     * @param string $savePath
     * @param string $sessName
     * @return bool
     * @throws \Exception
     */
    public function open($savePath, $sessName)
    {
        // 检测php环境
        if (!extension_loaded('memcache')) {
            throw new \Exception('not support:memcache');
        }
        $this->handler = new \Memcache();
        // 支持集群
        $hosts = explode(',', $this->config['host']);
        $ports = explode(',', $this->config['port']);
        if (empty($ports[0])) {
            $ports[0] = 11211;
        }
        // 建立连接
        foreach ((array)$hosts as $i => $host) {
            $port = isset($ports[$i]) ? $ports[$i] : $ports[0];
            $this->config['timeout'] > 0 ?
                $this->handler->addServer($host, $port, $this->config['persistent'], 1, $this->config['timeout']) :
                $this->handler->addServer($host, $port, $this->config['persistent'], 1);
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
        $this->handler->close();
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
     * @param String $sessData
     * @return bool
     */
    public function write($sessionId, $sessData)
    {
        return $this->handler->set($this->config['session_name'] . $sessionId, $sessData, 0, $this->config['expire']);
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessionId
     * @return bool
     */
    public function destroy($sessionId)
    {
        return $this->handler->delete($this->config['session_name'] . $sessionId);
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
