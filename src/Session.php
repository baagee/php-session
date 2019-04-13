<?php
/**
 * Desc: Session
 * User: baagee
 * Date: 2019/4/13
 * Time: 下午8:40
 */

namespace BaAGee\Session;

use BaAGee\Session\Base\SessionAbstract;
use BaAGee\Session\Base\SessionInterface;

/**
 * Class Session
 * @package BaAGee\Session
 */
class Session extends SessionAbstract implements SessionInterface
{
    private static function checkSessionInit()
    {
        if (self::$init !== true) {
            throw new \Exception('Session没有初始化');
        }
    }

    /**
     * 设置Session
     * @param string $key
     * @param        $value
     * @param string $prefix
     * @return mixed|void
     * @throws \Exception
     */
    public static function set(string $key, $value, string $prefix = '')
    {
        self::checkSessionInit();
        $prefix = !empty($prefix) ? $prefix : self::$prefix;
        if (strpos($key, '.')) {
            // 二维数组赋值
            list($name1, $name2) = explode('.', $key);
            if ($prefix) {
                $_SESSION[$prefix][$name1][$name2] = $value;
            } else {
                $_SESSION[$name1][$name2] = $value;
            }
        } elseif ($prefix) {
            $_SESSION[$prefix][$key] = $value;
        } else {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * 获取Session
     * @param string $key
     * @param string $prefix
     * @return array|mixed|null
     * @throws \Exception
     */
    public static function get(string $key, string $prefix = '')
    {
        self::checkSessionInit();
        $prefix = !is_null($prefix) ? $prefix : self::$prefix;
        if ('' == $key) {
            // 获取全部的session
            $value = $prefix ? (!empty($_SESSION[$prefix]) ? $_SESSION[$prefix] : []) : $_SESSION;
        } elseif ($prefix) {
            // 获取session
            if (strpos($key, '.')) {
                list($name1, $name2) = explode('.', $key);
                $value = isset($_SESSION[$prefix][$name1][$name2]) ? $_SESSION[$prefix][$name1][$name2] : null;
            } else {
                $value = isset($_SESSION[$prefix][$key]) ? $_SESSION[$prefix][$key] : null;
            }
        } else {
            if (strpos($key, '.')) {
                list($name1, $name2) = explode('.', $key);
                $value = isset($_SESSION[$name1][$name2]) ? $_SESSION[$name1][$name2] : null;
            } else {
                $value = isset($_SESSION[$key]) ? $_SESSION[$key] : null;
            }
        }
        return $value;
    }

    /**
     * 删除储存的值
     * @param string $key
     * @param string $prefix
     * @return mixed|void
     * @throws \Exception
     */
    public static function delete(string $key, string $prefix = '')
    {
        self::checkSessionInit();
        $prefix = !is_null($prefix) ? $prefix : self::$prefix;
        if (strpos($key, '.')) {
            list($name1, $name2) = explode('.', $key);
            if ($prefix) {
                unset($_SESSION[$prefix][$name1][$name2]);
            } else {
                unset($_SESSION[$name1][$name2]);
            }
        } else {
            if ($prefix) {
                unset($_SESSION[$prefix][$key]);
            } else {
                unset($_SESSION[$key]);
            }
        }
    }

    /**
     * 关闭，销毁Session
     * @return mixed|void
     * @throws \Exception
     */
    public static function destroy()
    {
        self::checkSessionInit();
        if (!empty($_SESSION)) {
            $_SESSION = [];
        }
        session_unset();
        session_destroy();
        self::$init = false;
    }

    /**
     * 清空Session
     * @param string $prefix
     * @return mixed|void
     * @throws \Exception
     */
    public static function clear(string $prefix = '')
    {
        self::checkSessionInit();
        $prefix = !is_null($prefix) ? $prefix : self::$prefix;
        if ($prefix) {
            unset($_SESSION[$prefix]);
        } else {
            $_SESSION = [];
        }
    }

    /**
     * 开启Session
     * @param string $prefix
     * @return mixed|void
     * @throws \Exception
     */
    public static function start(string $prefix)
    {
        self::checkSessionInit();
        session_start();
    }

    /**
     * 重新生成SessionId
     * @param bool $delete
     * @return mixed|void
     * @throws \Exception
     */
    public static function regenerate(bool $delete = false)
    {
        self::checkSessionInit();
        session_regenerate_id($delete);
    }

    /**
     * 暂停Session
     * @return mixed|void
     * @throws \Exception
     */
    public static function pause()
    {
        self::checkSessionInit();
        session_write_close();
        self::$init = false;
    }

    /**
     * 判断是否设置了session
     * @param string $key
     * @param string $prefix
     * @return bool|mixed
     * @throws \Exception
     */
    public static function has(string $key, $prefix = '')
    {
        self::checkSessionInit();
        $prefix = !is_null($prefix) ? $prefix : self::$prefix;
        if (strpos($key, '.')) {
            // 支持数组
            list($name1, $name2) = explode('.', $key);
            return $prefix ? isset($_SESSION[$prefix][$name1][$name2]) : isset($_SESSION[$name1][$name2]);
        } else {
            return $prefix ? isset($_SESSION[$prefix][$key]) : isset($_SESSION[$key]);
        }
    }
}
