<?php

/**
 * Util class for loading config file.
 *
 * Before using, you should config:
 * 1. set CONFPATH;
 * 2. set ENVIROMENT, it is an enum value;
 * 3. config file name, default config.*.ini; * is an enum value, see $_arr_env_map.
 *
 * For example:
 * require 'ConfigUtil.php';
 * ConfigUtil::instance()->load('mysql');
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
 * ConfigUtil::instance()->load();
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
 * Update: 2014-03-30
 */

define('CONFPATH', 'config'.DIRECTORY_SEPARATOR);
define('ENVIROMENT', 0);

class ConfigUtil {
    private static $_obj_instance = NULL;
    private static $_arr_config = array();
    private static $_arr_env_map = array(
        0 => 'development',
        1 => 'testing',
        2 => 'staging',
        3 => 'production');

    private function __construct() {
        try {
            if (!isset(self::$_arr_config) OR !self::$_arr_config) {
                $config = CONFPATH.'config.'.self::$_arr_env_map[ENVIROMENT].'.ini'; 
                if (file_exists($config)) {
                    self::$_arr_config = parse_ini_file($config, TRUE);
                } else {
                    $this->free();
                    echo "{$config} doesnot exists or unreadable.";
                }
            }
        } catch (Exception $e) {
            $this->free();
            echo 'init ConfigUtil failed:'.$e->getMessage();
        }
    }

    public static function instance() {
        if (!isset(self::$_obj_instance) OR !self::$_obj_instance) {
            $c = __CLASS__;
            self::$_obj_instance = new $c;
        }

        return self::$_obj_instance;
    }

    public function __clone() {
        trigger_error('singleton ConfigUtil clone is not allowed.', E_USER_ERROR);
    }

    public function free() {
        self::$_obj_instance = NULL;
        self::$_arr_config = array();
    }

    public function load($nodes = NULL) {
        if (self::$_arr_config) {
            if (!$nodes) {
                return self::$_arr_config;
            }
            if (is_string($nodes)) {
                $nodes = array($nodes);
            }

            if (is_array($nodes) AND $nodes) {
                $arr = array();
                foreach ($nodes as $node) {
                    $node = trim($node);
                    if (isset(self::$_arr_config[$node])) {
                        $arr[$node] = self::$_arr_config[$node];
                    }
                }
                return $arr ? $arr : self::$_arr_config;
            }
        }
        echo "Config is empty. Pls check it.";
        return FALSE;
    }

    public static function get_node($node = NULL) {
        if (!$node OR !is_string($node)) {
            echo "$node format error.";
            return FALSE;
        }
        $ret = ConfigUtil::instance()->load($node);
        return isset($ret[$node]) ? $ret[$node] : FALSE;
    }

    public function get_nodes($nodes = NULL) {
        return ConfigUtil::instance()->load($nodes);
    }
}