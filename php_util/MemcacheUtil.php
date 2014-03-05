<?php
/**
 * 
 * example:
 * MemcacheUtil::instance()->set('test', 'hello world.');
 * MemcacheUtil::instance()->get('test'); // hello world.
 *
 * wei.chungwei@gmail.com
 * 2014-03-04
 */
class MemcacheUtil {
	private static $obj_instance = NULL;
    private static $obj_memcahce = NULL;
    private static $arr_config = array();

    private function __construct() {
        try {
            if (!isset(self::$arr_config) OR !self::$arr_config) {
                self::$arr_config['host'] = 'localhost';
                self::$arr_config['port'] = '11211';
                self::$arr_config['compression'] = false;
            }
            if (self::$arr_config) {
                if (!isset(self::$obj_memcahce) OR !self::$obj_memcahce) {
                    self::$obj_memcahce = new Memcache;
                }
            }
        } catch (Exception $e) {
            Util_Log::instance()->warn('init memcache failed:'.$e->getMessage());
        }

    }

    public static function instance() {
        try {
            if (!isset(self::$obj_instance) OR !self::$obj_instance) {
                $c = __CLASS__;
                self::$obj_instance = new $c;
            }
            return self::$obj_instance;
        } catch (Exception $e) {
            Util_Log::instance()->warn('init Util_Memcache failed:'.$e->getMessage());
        }
    }

    public function __clone() {
        trigger_error('singleton clone is not allowed.', E_USER_ERROR);
    }

    public function free() {
        self::$obj_instance = null;
        self::$obj_memcahce = null;
        self::$arr_config = array();
    }

    public function set($key, $value, $expire = 0) {
        $compression = isset(self::$arr_config['compression']) ? self::$arr_config['compression'] : false;
        $md5_key = md5($key);
        try {
            $conn = self::$obj_memcahce->connect(self::$arr_config['host'], self::$arr_config['port']);
            if (!$conn) {
                Util_Log::instance()->warn("connect memcache failed");
                return FALSE;
            }
            $result = self::$obj_memcahce->set($md5_key, json_encode($value), $compression, $expire);
            if ($result) {
                Util_Log::instance()->info("memcache set {$md5_key} done");
            } else {
                Util_Log::instance()->warn("memcache set {$md5_key} failed");
            }
            self::$obj_memcahce->close();
        } catch (Exception $e) {
            Util_Log::instance()->warn("memcache set {$md5_key} failed:".$e->getMessage());
        }
    }

    public function get($key) {
        try {
            $conn = self::$obj_memcahce->connect(self::$arr_config['host'], self::$arr_config['port']);
            if (!$conn) {
                Util_Log::instance()->warn("connect memcache failed");
                return FALSE;
            }
            $result = self::$obj_memcahce->get(md5($key));
            self::$obj_memcahce->close();
            return json_decode($result, TRUE);
        } catch (Exception $e) {
            Util_Log::instance()->warn("memcache get ".md5($key)." failed:".$e->getMessage());
        }
    }

}