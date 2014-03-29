<?php

/**
 * Util class for writing log
 * it includes 4 levels:
 * fatal, warn, debug(only use in development), info.
 *
 * Befire using it, you should config some params in constructor:
 * 1. set dir log, and it must be writable by others, default ./log/;
 * 2. set max size of a single log file, default 1GB;
 * 3. set max log files num per day, default 1;
 * 4. set env value, default DEVELOPMENT.
 *
 * For example:
 * require 'LogUtil.php';
 * LogUtil::instance()->info('info msg');
 * LogUtil::instance()->debug('debug msg');
 * sleep(3);
 * LogUtil::instance()->warn('warn msg');
 * sleep(3);
 * LogUtil::instance()->fatal('fatal msg');
 * 
 * Output:
 * [2014-03-10 04:03:56][INFO][127.0.0.1][LogUtil.php:205][][info msg]
 * [2014-03-10 04:03:56][WARN][127.0.0.1][LogUtil.php:206][][warn msg]
 * [2014-03-10 04:03:59][DEBUG][127.0.0.1][LogUtil.php:208][][debug msg]
 * [2014-03-10 04:04:02][FATAL][127.0.0.1][LogUtil.php:210][][fatal msg]
 *
 * Author: wei.chungwei@gmail.com
 * Create: 2013-11-01
 * Update: 2014-03-10
 */

class LogUtil {

    private static $_obj_instance = NULL;
    private static $_arr_conf = array();

    private function __construct() {
        if (!isset(self::$_arr_conf) OR !self::$_arr_conf) {
            self::$_arr_conf['dir'] = dirname(__FILE__).DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR;
            self::$_arr_conf['env'] = 'DEVELOPMENT';
            self::$_arr_conf['max_size'] = 1<<30;
            self::$_arr_conf['max_num'] = 1;

            $this->create_log_dir(self::$_arr_conf['dir']);
        }
    }

    public static function instance() {
        if (!isset(self::$_obj_instance) OR !self::$_obj_instance) {
            $c = __CLASS__;
            self::$_obj_instance = new $c;
        }
        self::$_arr_conf['time'] = time();

        return self::$_obj_instance;
    }

    public function __clone() {
        trigger_error('singleton LogUtil clone is not allowed.', E_USER_ERROR);
    }

    public function free() {
        self::$_obj_instance = NULL;
        self::$_arr_conf = NULL;
    }

    public function info($msg) {
        $this->log_business($msg, __FUNCTION__);
    }

    public function debug($msg) {
        if (isset(self::$_arr_conf['env']) AND self::$_arr_conf['env'] == 'DEVELOPMENT') {
            $this->log_business($msg, __FUNCTION__);
        }
    }

    public function warn($msg) {
        $this->log_business($msg, __FUNCTION__);
    }

    public function fatal($msg) {
        $this->log_business($msg, __FUNCTION__);
    }

    /**
     * get log file name.
     * file name="current date + rand num".log
     */
    private function get_log_name() {
        $seq = mt_rand(1, self::$_arr_conf['max_num']);
        return self::$_arr_conf['dir'].date("Y-m-d", self::$_arr_conf['time'])."-{$seq}.log";
    }

    /**
     * format log msg like:
     * [2013-11-01 18:31:03][DEBUG][127.0.0.1][LogUtil.php:151][/LogUtil.php][debug msg]
     */
    private function format_log_msg($msg, $priority) {
        $datetime = date("Y-m-d H:i:s", self::$_arr_conf['time']);
        $priority = strtoupper(trim($priority));
        $ip = get_user_ip();
        $arr_trace = debug_backtrace();
        $trace = $arr_trace[2]; // Pls pay attention to the array index.
        $file = basename($trace['file']);
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        return "[{$datetime}][{$priority}][{$ip}][{$file}:{$trace['line']}][{$uri}][{$msg}]".PHP_EOL;
    }

    /**
     * check sizes of a single log file,
     * rename it if > 1GB to avoid low performance.
     * the new log file will be readable only.
     */
    private function check_file_size($log_name) {
        try {
            if (file_exists($log_name) AND filesize($log_name) >= self::$_arr_conf['max_size']) {
                $rename_file = self::$_arr_conf['dir'].date("Y-m-d H:i:s", self::$_arr_conf['time']).mt_rand(100, 999).'.log';
                rename($log_name, $rename_file);
                chmod($rename_file, 0444); // readable only
            }
            return TRUE;
        } catch (Exception $e) {
            echo 'error accoured at '.basename(__FILE__).':'.__LINE__." with msg : ".$e->getMessage();
            return FALSE;
        }
    }

    /**
     * write log msg into file
     */
    private function write_log($log_name, $log_msg = "") {
        try {
            if ($fp = fopen($log_name, 'a')) {
                // lock the log file for writing.
                // if locking file failed in 1ms, try it again,
                // otherwise free the lock to other proccess.
                $start_time = microtime();
                do {
                    $lock = flock($fp, LOCK_EX);
                    if(!$lock) {
                        usleep(mt_rand(10, 30000));
                    }
                } while ((!$lock) && ((microtime() - $start_time) < 1000));

                if ($lock) {
                    fwrite($fp, $log_msg);
                    flock($fp, LOCK_UN);
                }
                fclose($fp);

                if (!is_writable($log_name)) {
                    chmod($log_name, 0666);
                }
                
                clearstatcache();
                return TRUE;
            } else {
                echo "open {$log_name} failed at ".basename(__FILE__)." line ".__LINE__;
                return FALSE;
            }
        } catch (Exception $e) {
            echo 'error accoured at '.basename(__FILE__).':'.__LINE__." : ".$e->getMessage();
            return FALSE;
        }
    }

    /**
     * 2014-03-10
     * @return [type] [description]
     */
    private function log_business($msg, $log_type) {
        $log_name = $this->get_log_name();
        $this->check_file_size($log_name);
        $log_msg = $this->format_log_msg($msg, $log_type);
        $this->write_log($log_name, $log_msg);
    }

    /**
     * 2014-03-10
     */
    private function create_log_dir($dir) {
        if ($dir) {
            try {
                if (!is_dir($dir)) {
                    if (FALSE == mkdir($dir, 0777, TRUE)) {
                        echo "create $dir failed. please try it again or create manul.";
                        return FALSE;
                    }
                    return TRUE;
                }
            } catch (Exception $e) {
                echo "create $dir failed ".basename(__FILE__).':'.__LINE__.' : '.$e->getMessage();
                return FALSE;
            }
        }
        return FALSE;
    }
}

/**
 * get user client ip.
 * recommend refactor thie function
 */
function get_user_ip() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        echo __LINE__.$ip;
    } elseif (isset($_SERVER['HTTP_CLIENTIP'])) {
        $ip = $_SERVER['HTTP_CLIENTIP'];echo __LINE__.$ip;
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];echo __LINE__.$ip;
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');echo __LINE__.$ip;
    } elseif (getenv('HTTP_CLIENTIP')) {
        $ip = getenv('HTTP_CLIENTIP');echo __LINE__.$ip;
    } elseif (getenv('REMOTE_ADDR')) {
        $ip = getenv('REMOTE_ADDR');echo __LINE__.$ip;
    } else {
        $ip = '127.0.0.1';echo __LINE__.$ip;
    }

    $pos = strpos($ip, ',');
    if( $pos > 0 ) {
        $ip = substr($ip, 0, $pos);
    }

    return trim($ip);
}


LogUtil::instance()->fatal('fatal msg');