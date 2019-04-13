<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/4/13
 * Time: 下午9:10
 */
include_once __DIR__ . '/../vendor/autoload.php';

$config = [
    'handler' => \BaAGee\Session\Handler\Redis::class,
];
\BaAGee\Session\Session::init($config);

\BaAGee\Session\Session::set('name',"很爱很爱");
var_dump(\BaAGee\Session\Session::get('name'));
\BaAGee\Session\Session::destroy();
\BaAGee\Session\Session::start();
\BaAGee\Session\Session::delete('name');
var_dump(\BaAGee\Session\Session::get('name'));


echo 'over';