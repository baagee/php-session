<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/4/13
 * Time: 下午9:10
 */
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
