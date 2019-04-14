<?php

/**
 * Desc:
 * User: baagee
 * Date: 2019/4/14
 * Time: 下午2:47
 */

/**
 * Class MySessionHandler 自定义session处理类
 */
class MySessionHandler extends SessionHandler
{
    protected $config = [];

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

    }

    /**
     * 关闭Session
     * @access public
     */
    public function close()
    {
    }

    /**
     * 读取Session
     * @param string $sessionId
     * @return string
     */
    public function read($sessionId)
    {

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

    }

    /**
     * 删除Session
     * @access public
     * @param string $sessionId
     * @return bool
     */
    public function destroy($sessionId)
    {
    }

    /**
     * Session 垃圾回收
     * @access public
     * @param string $sessionMaxLifeTime
     * @return true
     */
    public function gc($sessionMaxLifeTime)
    {

    }
}

$config=[
    'handler'=>MySessionHandler::class
];

\BaAGee\Session\Session::init($config);
\BaAGee\Session\Session::start();
\BaAGee\Session\Session::set('key','val');