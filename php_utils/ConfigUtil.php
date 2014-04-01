<?php

/**
 * Util class for loading config file.
 *
 * Before using, you should config:
 * 1. set CONFPATH;
 * 2. set ENVIROMENT, it is an enum value;
 * 3. config file name, default config.*.ini; * is an enum value, see $_env_map.
 *
 * For example:
 * require 'ConfigUtil.php';
 * ConfigUtil::get_instance()->load('mysql');
 *
 * Output:
 * Array
 * (
 *    [mysql] => Array
 *        (
 *            [hhh] => 222
 *            [pp] => 09hjk
 *            [sdhf] => e8rtghjkdf
 *        )
 * )
 * 
 * ConfigUtil::get_instance()->load();
 *
 * Output:
 * Array
 * (
 *     [mysql] => Array
 *         (
 *             [hhh] => 222
 *             [pp] => 09hjk
 *             [sdhf] => e8rtghjkdf
 *         )
 * 
 *     [uuu] => Array
 *         (
 *             [sjdfh] => 3587
 *             [hf] => 3458u
 *         )
 * )
 * 
 * Author: wei.chungwei@gmail.com
 * Create: 2014-03-11
 * Update: 2014-03-31
 */

define('CONFPATH', 'config'.DIRECTORY_SEPARATOR);
define('ENVIROMENT', 0);

class ConfigUtil {
    private static $_instance = NULL;
    private static $_config = array();
    private static $_env_map = array(
        0 => 'development',
        1 => 'testing',
        2 => 'staging',
        3 => 'production');

    private function __construct() {
        try {
            if (!isset(self::$_config) OR !self::$_config) {
                $config = CONFPATH.'config.'.self::$_env_map[ENVIROMENT].'.ini'; 
                if (file_exists($config)) {
                    self::$_config = parse_ini_file($config, TRUE);
                } else {
                    $this->free();
                    throw new Exception("{$config} doesnot exists or unreadable.");
                }
            }
        } catch (Exception $e) {
            $this->free();
            throw new Exception('init ConfigUtil failed:'.$e->getMessage());
        }
    }

    public static function get_instance() {
        if (!isset(self::$_instance) OR !self::$_instance) {
            $c = __CLASS__;
            self::$_instance = new $c;
        }

        return self::$_instance;
    }

    public function __clone() {
        throw new Exception('singleton ConfigUtil clone is not allowed.');
    }

    public function free() {
        self::$_instance = NULL;
        self::$_config = array();
    }

    public function load($nodes = NULL) {
        if (self::$_config) {
            if (!$nodes) {
                return self::$_config;
            }
            if (is_string($nodes)) {
                $nodes = array($nodes);
            }

            if (is_array($nodes) AND $nodes) {
                $arr = array();
                foreach ($nodes as $node) {
                    $node = trim($node);
                    if (isset(self::$_config[$node])) {
                        $arr[$node] = self::$_config[$node];
                    }
                }
                return $arr ? $arr : self::$_config;
            }
        }
        throw new Exception("Config is empty. Pls check it.");
    }

    public static function get_node($node = NULL) {
        if (!$node OR !is_string($node)) {
            throw new Exception("$node format error.");
        }
        $ret = ConfigUtil::get_instance()->load($node);
        return isset($ret[$node]) ? $ret[$node] : FALSE;
    }
}