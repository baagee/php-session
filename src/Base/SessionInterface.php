<?php
/**
 * Desc: SessionInterface
 * User: baagee
 * Date: 2019/4/13
 * Time: 下午8:41
 */

namespace BaAGee\Session\Base;
/**
 * Interface SessionInterface
 * @package BaAGee\Session\Base
 */
interface SessionInterface
{
    /**
     * 开启Session
     * @param string $prefix
     * @return mixed
     */
    public static function start(string $prefix='');

    /**
     * 设置值
     * @param string $key
     * @param        $value
     * @param string $prefix
     * @return mixed
     */
    public static function set(string $key, $value, string $prefix = '');

    /**
     * 获取值
     * @param string $key
     * @param string $prefix
     * @return mixed
     */
    public static function get(string $key, string $prefix = '');

    /**
     * 删除值
     * @param string $key
     * @param string $prefix
     * @return mixed
     */
    public static function delete(string $key, string $prefix = '');

    /**
     * 清空session
     * @param string $prefix
     * @return mixed
     */
    public static function clear(string $prefix = '');

    /**
     * 判断值是否存在
     * @param string $key
     * @param string $prefix
     * @return mixed
     */
    public static function has(string $key, $prefix = '');

    /**
     * 销毁session
     * @return mixed
     */
    public static function destroy();
}
