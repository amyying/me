<?php
/**
 * 日志通用类
 * 日志分为四个级别：fatal,warn,debug,info
 *
 * 使用前
 * 需要在构造函数中配置日志路径，默认使用的是站点根目录下的log目录，要给写入权限
 * 至于日志文件的大小限制和数量視情况而定，可以使用默认值
 *
 * 使用示例：
 * require 'LogUtil.php';
 * LogUtil::get_instance()->debug('debug msg');
 * output:
 * [2013-11-01 18:31:03][DEBUG][127.0.0.1][LogUtil.php:151][/LogUtil.php][debug msg]
 *
 * wei.chungwei@gmail.com
 * 2013-11-11
 */

date_default_timezone_set('Asia/ShangHai');

class LogUtil {

    public static $_obj_instance;
    private $_log_dir; // 日志路径，该目录必须要有写入权限，默认是当前目录下的log目录
    private $_log_max_size; // 单个日志文件的最大值，默认是1G
    private $_log_max_num; // 每天最多产生的日志文件数量，默认是1（这也是开发环境的推荐值）

    private function __construct() {
        $this->_log_dir = dirname(__FILE__) . '/log/';
        $this->_log_max_size = 1 << 30;
        $this->_log_max_num = 1;
        echo $this->_log_dir;
    }

    public static function get_instance() {
        if (!isset(self::$_obj_instance)) {
            $c = __CLASS__;
            self::$_obj_instance = new $c;
        }

        return self::$_obj_instance;
    }

    public function __clone() {
        trigger_error('singleton clone is not allowed.', E_USER_ERROR);
    }

    public function free() {
        self::$_obj_instance = null;
    }

    public function info($msg) {
        $time = time();
        $log_name = $this->get_log_name($time);
        $this->check_file_size($time, $log_name);
        $log_msg = $this->format_log_msg($msg, $time, "INFO");
        $this->write_log($time, $log_name, $log_msg);
    }

    public function debug($msg) {
        $time = time();
        $log_name = $this->get_log_name($time);
        $this->check_file_size($time, $log_name);
        $log_msg = $this->format_log_msg($msg, $time, "DEBUG");
        $this->write_log($time, $log_name, $log_msg);
    }

    public function fatal($msg) {
        $time = time();
        $log_name = $this->get_log_name($time);
        $this->check_file_size($time, $log_name);
        $log_msg = $this->format_log_msg($msg, $time, "FATAL");
        $this->write_log($time, $log_name, $log_msg);
        $this->free();
    }

    public function warn($msg) {
        $time = time();
        $log_name = $this->get_log_name($time);
        $this->check_file_size($time, $log_name);
        $log_msg = $this->format_log_msg($msg, $time, "WARN");
        $this->write_log($time, $log_name, $log_msg);
    }

    /**
     * 获取日志文件名称
     * 文件名称格式=当前日期+随机数.log
     * 以避免单个日志文件过大
     */
    private function get_log_name($time) {
        $seq_num = rand(1, $this->_log_max_num);
        return $this->_log_dir . date("Y-m-d", $time) . "-{$seq_num}.log";
    }

    /**
     * 格式化日志信息
     */
    private function format_log_msg($msg, $time, $priority) {
        $datetime = date("Y-m-d H:i:s", $time);
        $priority = strtoupper(trim($priority));
        $ip = get_user_ip();
        $arr_trace = debug_backtrace();
        $trace = end($arr_trace);
        $file = basename($trace['file']);
        $line = $trace['line'];
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        return "[{$datetime}] [{$priority}] [{$ip}] [{$file}:{$line}] [{$uri}] [{$msg}]" . PHP_EOL;
    }

    /**
     * 检测单个日志文件大小，大于1G则重命名该日志文件
     * 这样可以避免对大文件的读写过慢
     */
    private function check_file_size($time, $log_name) {
        try {
            if (file_exists($log_name) && filesize($log_name) >= $this->_log_max_size) {
                // if log size >= 1GB(default),
                // then rename this file and make it readonly
                $rename_log_file = $this->_log_dir . date("Y-m-d H:i:s", $time) . rand(1, 100) . '.log';
                rename($log_name, $rename_log_file);
                chmod($rename_log_file, 0444); // 只有可读权限
            }
        } catch (Exception $e) {
            die('error accoured at ' . basename(__FILE__) . ':' . __LINE__ . " with msg : " . $e->getMessage());
        }
    }

    /**
     * 将日志信息写入文件
     */
    private function write_log($time, $log_name, $log_msg = "") {
        try {
            if ($fp = fopen($log_name, 'a')) {
                // 以下代码对文件加锁，1ms内加锁失败，继续枷锁；
                // 超过1ms则让出锁给其他进程
                $start_time = microtime();
                do {
                    $lock = flock($fp, LOCK_EX);
                    if(!$lock) {
                        usleep(rand(10, 30000));
                    }
                } while ((!$lock) && ((microtime() - $start_time) < 1000));
                if ($lock) {
                    fwrite($fp, $log_msg);
                    flock($fp, LOCK_UN);
                }
                fclose($fp);
                chmod($log_name, 0666);
            } else {
                die("open {$log_name} failed at " . basename(__FILE__) . " line " . __LINE__);
            }
            clearstatcache();
        } catch (Exception $e) {
            die('error accoured at ' . basename(__FILE__) . ':' . __LINE__ . " with msg : " . $e->getMessage());
        }
    }
}

/**
 * 获取用户ip
 */
function get_user_ip() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_CLIENTIP'])) {
        $ip = $_SERVER['HTTP_CLIENTIP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_CLIENTIP')) {
        $ip = getenv('HTTP_CLIENTIP');
    } elseif (getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');
    } else {
        $ip = '127.0.0.1';
    }

    $pos = strpos($ip, ',');
    if( $pos > 0 ) {
        $ip = substr($ip, 0, $pos);
    }

    return trim($ip);
}

/**
 * 使用示例
 */
LogUtil::get_instance()->info('info msg');
//LogUtil::get_instance()->debug('debug msg');
//LogUtil::get_instance()->warn('warn msg');
//LogUtil::get_instance()->fatal('fatal msg');