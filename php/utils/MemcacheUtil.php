<?php
/**
 * 
 * example:
 * MemcacheUtil::get_instance()->set('test', 'hello world.');
 * MemcacheUtil::get_instance()->get('test'); // hello world.
 *
 * wei.chungwei@gmail.com
 * 2014-03-04
 */
class MemcacheUtil {
    private static $_instance = NULL;
    private static $_memcahce = NULL;
    private static $_config = array();

    private function __construct() {
        try {
            if (!isset(self::$_config) OR !self::$_config) {
                self::$_config['host'] = 'localhost';
                self::$_config['port'] = '11211';
                self::$_config['compression'] = FALSE;
            }
            if (self::$_config) {
                if (!isset(self::$_memcahce) OR !self::$_memcahce) {
                    self::$_memcahce = new Memcache;
                }
            }
        } catch (Exception $e) {
            throw new Exception('init memcache failed:'.$e->getMessage());
        }
    }

    public static function get_instance() {
        try {
            if (!isset(self::$_instance) OR !self::$_instance) {
                $c = __CLASS__;
                self::$_instance = new $c;
            }
            return self::$_instance;
        } catch (Exception $e) {
            throw new Exception('init MemcacheUtil failed:'.$e->getMessage());
        }
    }

    public function __clone() {
        throw new Exception('singleton clone is not allowed.');
    }

    public function free() {
        self::$_instance = NULL;
        self::$_memcahce = NULL;
        self::$_config = array();
    }

    public function set($key, $value, $expire = 0) {
        $compression = isset(self::$_config['compression']) ? self::$_config['compression'] : FALSE;
        $md5_key = md5($key);
        try {
            $conn = self::$_memcahce->connect(self::$_config['host'], self::$_config['port']);
            if (!$conn) {
                throw new Exception("connect memcache failed");
            }
            $result = self::$_memcahce->set($md5_key, json_encode($value), $compression, $expire);
            if ($result) {
                throw new Exception("memcache set {$md5_key} done");
            } else {
                throw new Exception("memcache set {$md5_key} failed");
            }
            self::$_memcahce->close();
        } catch (Exception $e) {
            throw new Exception("memcache set {$md5_key} failed:".$e->getMessage());
        }
    }

    public function get($key) {
        try {
            $conn = self::$_memcahce->connect(self::$_config['host'], self::$_config['port']);
            if (!$conn) {
                throw new Exception("connect memcache failed");
            }
            $result = self::$_memcahce->get(md5($key));
            self::$_memcahce->close();
            return json_decode($result, TRUE);
        } catch (Exception $e) {
            throw new Exception("memcache get ".md5($key)." failed:".$e->getMessage());
        }
    }
}