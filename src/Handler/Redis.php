<?php
/**
 * Desc: Redis储存Session
 * User: baagee
 * Date: 2019/4/13
 * Time: 下午9:34
 */

namespace BaAGee\Session\Handler;

use SessionHandler;

/**
 * Class Redis
 * @package BaAGee\Session\Handler
 */
class Redis extends SessionHandler
{
    /**
     * @var \Redis
     */
    protected $handler = null;
    /**
     * @var array
     */
    protected $config = [
        'host'         => '127.0.0.1', // redis主机
        'port'         => 6379, // redis端口
        'password'     => '', // 密码
        'select'       => 0, // 操作库
        'expire'       => 3600, // 有效期(秒)
        'timeout'      => 0, // 超时时间(秒)
        'persistent'   => true, // 是否长连接
        'session_name' => '', // sessionkey前缀
    ];

    /**
     * Redis constructor.
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
        if (!extension_loaded('redis')) {
            throw new \Exception('not support:redis');
        }
        $this->handler = new \Redis();

        // 建立连接
        $func = $this->config['persistent'] ? 'pconnect' : 'connect';
        $this->handler->$func($this->config['host'], $this->config['port'], $this->config['timeout']);

        if ('' != $this->config['password']) {
            $this->handler->auth($this->config['password']);
        }

        if (0 != $this->config['select']) {
            $this->handler->select($this->config['select']);
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
     * @access public
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
        if ($this->config['expire'] > 0) {
            return $this->handler->setex($this->config['session_name'] . $sessionId, $this->config['expire'], $sessionData);
        } else {
            return $this->handler->set($this->config['session_name'] . $sessionId, $sessionData);
        }
    }

    /**
     * 删除Session
     * @access public
     * @param string $sessionId
     * @return bool
     */
    public function destroy($sessionId)
    {
        return $this->handler->delete($this->config['session_name'] . $sessionId) > 0;
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessionMaxLifeTime
     * @return bool
     */
    public function gc($sessionMaxLifeTime)
    {
        return true;
    }
}
