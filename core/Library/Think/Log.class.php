<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;

/**
 * 日志处理类
 */
class Log
{

    // 日志级别 从上到下，由低到高
    const EMERG = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR = 'ERR';  // 一般错误: 一般性错误
    const WARN = 'WARN';  // 警告性错误: 需要发出警告的错误
    const NOTICE = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO = 'INFO';  // 信息: 程序输出信息
    const DEBUG = 'DEBUG';  // 调试: 调试信息
    const SQL = 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效

    // 日志信息
    static protected $log = array();

    // 日志初始化
    static public function init($config = array())
    {
    }

    /**
     * 记录日志 并且会过滤未经设置的级别
     *
     * @static
     * @access public
     *
     * @param string  $message 日志信息
     * @param string  $level   日志级别
     * @param boolean $record  是否强制记录
     *
     * @return void
     */
    static function record($message, $level = self::ERR, $record = false)
    {
        self::$log[] = "{$level}: {$message}\r\n";
    }

    /**
     * 日志保存
     *
     * @static
     * @access public
     *
     * @param integer $type        日志记录方式
     * @param string  $destination 写入目标
     *
     * @return void
     */
    static function save($type = '', $destination = '')
    {
        if (empty(self::$log)) {
            return;
        }

        foreach (self::$log as $message) {
            self::write($message, substr($message, 0, strpos($message, ':')), $type, $destination);
        }

        // 保存后清空日志缓存
        self::$log = array();
    }

    /**
     * 日志直接写入
     *
     * @static
     * @access public
     *
     * @param string  $message     日志信息
     * @param string  $level       日志级别
     * @param integer $type        日志记录方式
     * @param string  $destination 写入目标
     *
     * @return void
     */
    static function write($message, $level = self::ERR, $type = '', $destination = '')
    {
        $module_name      = !defined("MODULE_NAME") ? '' : MODULE_NAME;
        $controller_name  = !defined("CONTROLLER_NAME") ? '' : CONTROLLER_NAME;
        $action_name      = !defined("ACTION_NAME") ? '' : ACTION_NAME;
        $action_user_name = !isset($_SESSION['user']['admin_name']) ? '-' : $_SESSION['user']['admin_name'];
        $message          = str_replace(array("\r\n", "\r", "\n"), "", $message . $destination);

        \SeasLog::log(self::getLogLevel($level),
            get_client_ip() . ' | admin_system | 1 | ' . $controller_name . ' | ' . $action_name . ' | ' . DISTINCT_REQUEST_ID . ' | ' . $action_user_name . ' | - | - | - | ' . $message);
    }

    /*
     *  SEASLOG_DEBUG "debug"
        SEASLOG_INFO  "info"
        SEASLOG_NOTICE "notice"
        SEASLOG_WARNING "warning"
        SEASLOG_ERROR "error"
        SEASLOG_CRITICAL "critical"
        SEASLOG_ALERT "alert"
        SEASLOG_EMERGENCY "emergency"
     */
    static function getLogLevel($level)
    {
        $error = array('EMERG', 'ALERT', 'CRIT', 'ERR');
        $debug = array('DEBUG', 'SQL');

        if (in_array($level, $error)) {
            return 'error';
        } else {
            if (in_array($level, $debug)) {
                return 'debug';
            } else {
                if ($level == self::WARN) {
                    return 'warning';
                } else {
                    return strtolower($level);
                }
            }
        }
    }
}