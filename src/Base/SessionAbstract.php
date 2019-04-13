<?php
/**
 * Desc: Session基本
 * User: baagee
 * Date: 2019/4/13
 * Time: 下午8:51
 */

namespace BaAGee\Session\Base;

/**
 * Class SessionAbstract
 * @package BaAGee\Session\Base
 */
abstract class SessionAbstract
{
    use ProhibitNewClone;

    /**
     * @var bool 是否初始化
     */
    protected static $init = false;

    /**
     * @var string Session key前缀
     */
    protected static $prefix = '';

    /**
     * 设置或者获取session作用域（前缀）
     * @param string $prefix
     * @return bool|string
     */
    final public static function prefix(string $prefix = '')
    {
        if (empty($prefix) && null !== $prefix) {
            return static::$prefix;
        } else {
            static::$prefix = $prefix;
            return true;
        }
    }

    /**
     * session初始化
     * @param array $config
     */
    final public static function init(array $config = [])
    {
        if (static::$init === false) {
            if (isset($config['use_trans_sid'])) {
                ini_set('session.use_trans_sid', $config['use_trans_sid'] ? 1 : 0);
            }
            $isDoStart = false;
            // 启动session
            if (!empty($config['auto_start']) && PHP_SESSION_ACTIVE === session_status()) {
                // 配置了自动开启session，并且session开启
                // ini_set('session.auto_start', 0);
                $isDoStart = true;
                if (!empty($_SESSION)) {
                    $_SESSION = [];
                }
                session_unset();
                session_destroy();
            }

            if (isset($config['prefix']) && ('' === self::$prefix || null === self::$prefix)) {
                self::$prefix = $config['prefix'];
            }
            if (isset($config['var_session_id']) && isset($_REQUEST[$config['var_session_id']])) {
                session_id($_REQUEST[$config['var_session_id']]);
            } elseif (isset($config['id']) && !empty($config['id'])) {
                session_id($config['id']);
            }
            if (isset($config['name'])) {
                session_name($config['name']);
            }
            if (isset($config['path'])) {
                session_save_path($config['path']);
            }
            if (isset($config['domain'])) {
                ini_set('session.cookie_domain', $config['domain']);
            }
            if (isset($config['expire'])) {
                ini_set('session.gc_maxlifetime', $config['expire']);
                ini_set('session.cookie_lifetime', $config['expire']);
            }
            if (isset($config['secure'])) {
                ini_set('session.cookie_secure', $config['secure']);
            }
            if (isset($config['httponly'])) {
                ini_set('session.cookie_httponly', $config['httponly']);
            }
            if (isset($config['use_cookies'])) {
                ini_set('session.use_cookies', $config['use_cookies'] ? 1 : 0);
            }
            if (isset($config['cache_limiter'])) {
                session_cache_limiter($config['cache_limiter']);
            }
            if (isset($config['cache_expire'])) {
                session_cache_expire($config['cache_expire']);
            }
            if (!empty($config['handler'])) {
                // 读取session驱动
                $handlerClass = $config['handler'];
                if (is_subclass_of($handlerClass, \SessionHandler::class)) {
                    // 检查驱动类
                    if (!class_exists($handlerClass) || !session_set_save_handler(new $handlerClass($config))) {
                        throw new \Exception(sprintf('Session驱动[%s]不合法', $handlerClass));
                    }
                }
            }
            if ($isDoStart) {
                session_start();
            }
        }
        self::$init = true;
    }
}
