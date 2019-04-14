# php-session
PHP Session library

# 基本使用
```php
include_once __DIR__ . '/../vendor/autoload.php';

use \BaAGee\Session\Session;

// 配置
$config = [
    'handler'      => \BaAGee\Session\Handler\Redis::class,//使用redis储存
    'host'         => '127.0.0.1', // redis主机
    'port'         => 6379, // redis端口
    'password'     => '', // 密码
    'select'       => 1, // 操作库
    'expire'       => 3600, // 有效期(秒)
    'timeout'      => 0, // 超时时间(秒)
    'persistent'   => false, // 是否长连接
    'session_name' => 'session_', // sessionkey前缀
    'auto_start'   => 1,// 是否自动开启session
    'use_cookies'   =>1
];
//初始化
Session::init($config);
// 开启 并且可以设置全局作用域
Session::start('prefix1');
// 设置session
Session::set('time1', time());
// 获取
var_dump(Session::get('time1'));
var_dump($_SESSION);
//删除
Session::delete('time1');

//改变全局作用域
Session::setPrefix('prefix2');
Session::set('time2', time());
var_dump($_SESSION);

//设置值时指定作用域，不更改全局
Session::set('age1', mt_rand(1, 99), 'user');
var_dump(Session::get('age1', 'user'));
var_dump($_SESSION);

Session::set('time3',time());
var_dump(Session::get('time3'));
var_dump($_SESSION);
/*array(3) {
  ["prefix1"]=>
  array(0) {
  }
  ["prefix2"]=>
  array(2) {
    ["time2"]=>
    int(1555222799)
    ["time3"]=>
    int(1555222799)
  }
  ["user"]=>
  array(1) {
    ["age1"]=>
    int(70)
  }
}*/
// 只会删除最后一次设置的全局作用域
Session::clear();
var_dump($_SESSION);
/*array(2) {
  ["prefix1"]=>
  array(0) {
  }
  ["user"]=>
  array(1) {
    ["age1"]=>
    int(40)
  }
}*/
//删除指定的作用域
Session::clear('user');
var_dump($_SESSION);
/*array(1) {
  ["prefix1"]=>
  array(0) {
  }
}*/
//销毁全部session
\BaAGee\Session\Session::destroy();
echo 'over';
```

# 支持的配置项
```
参数                  描述
handler	            session类型 
expire	            session过期时间
prefix	            session前缀
auto_start	        是否自动开启
use_trans_sid	    是否使用use_trans_sid
var_session_id	    请求session_id变量名
id	                session_id
name	            session_name
path	            session保存路径
domain	            session cookie_domain
use_cookies	        是否使用cookie
cache_limiter	    session_cache_limiter
cache_expire	    session_cache_expire
secure              session.cookie_secure 的配置
```

# 内置支持的handler及其扩充的配置:
```php
\BaAGee\Session\Handler\Memcache::class;
// 配置 支持集群
protected $config = [
    'host'         => '127.0.0.1', // memcache主机 集群示例：'127.0.0.1,127.0.0.2'
    'port'         => 11211, // memcache端口 集群示例：11211,11222 顺序和host对应
    'expire'       => 3600, // session有效期
    'timeout'      => 0, // 连接超时时间（单位：毫秒）
    'persistent'   => true, // 长连接
    'session_name' => '', // memcache key前缀
];
\BaAGee\Session\Handler\Memcached::class;
// 配置
protected $config = [
    'host'         => '127.0.0.1', // memcache主机 memcache主机 集群示例：'127.0.0.1,127.0.0.2'
    'port'         => 11211, // memcache端口 集群示例：11211,11222 顺序和host对应
    'expire'       => 3600, // session有效期
    'timeout'      => 0, // 连接超时时间（单位：毫秒）
    'session_name' => '', // memcache key前缀
    'username'     => '', //账号
    'password'     => '', //密码
];
\BaAGee\Session\Handler\Redis::class;
// 配置：
protected $config = [
    'host'         => '127.0.0.1', // redis主机
    'port'         => 6379, // redis端口
    'password'     => '', // 密码
    'select'       => 0, // 操作库
    'expire'       => 3600, // 有效期(秒)
    'timeout'      => 0, // 超时时间(秒)
    'persistent'   => true, // 是否长连接
    'session_name' => '', // sessionKey前缀
];
```
handler配置不填或者其他的，默认是file文件储存


# 自定义Session处理类
```php
/**
 * Class MySessionHandler 自定义session处理类 必须继承\SessionHandler，实现以下方法
 */
class MySessionHandler extends \SessionHandler
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

//用法不变
$config=[
    'handler'=>MySessionHandler::class//设置成自己的Session处理类
];

\BaAGee\Session\Session::init($config);
\BaAGee\Session\Session::start();
\BaAGee\Session\Session::set('key','val');
```
