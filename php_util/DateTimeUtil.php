<?php

/**
 * 时间日期通用类
 *
 * wei.chungwei@gmail.com
 * 2013-11-14
 */

date_default_timezone_set("Asia/ShangHai");

class DateTimeUtil {
    private static $_obj_instance;

    private function __construct() {}

    public static function get_instance() {
        if (!isset(self::$_obj_instance)) {
            $c = __CLASS__;
            self::$_obj_instance = new $c;
        }
        return self::$_obj_instance;
    }

    public function __clone() {
        trigger_error("singleton clone is not allowed.", E_USER_ERROR);
    }

    public function free() {
        self::$_obj_instance = null;
    }

    public function get_chinese_standard_datetime($timestamp) {
        $format = "Y年m月d日 H点i分s秒";
        return date($format, $timestamp);
    }

    public function get_chinese_standard_time($timestamp) {
        $format = "H点i分s秒";
        return date($format, $timestamp);
    }

    public function get_chinese_standard_date($timestamp) {
        $format = "Y年m月d日";
        return date($format, $timestamp);
    }

    public function get_chinese_friendly_datetime($timestamp) {
        $now = time();
        $step = $now - $timestamp;

        if ($step < 10) {
            return "刚刚";
        } elseif ($step < 60 && $step >= 10) {
            return "{$step}秒前";
        } elseif ($step < 3600 && $step >= 60) {
            return intval($step / 60) . "分钟前";
        } elseif ($step < 86400 && $step >= 3600) {
            return intval($step / 3600) . "小时前";
        } elseif ($step < 604800 && $step >= 86400) {
            return intval($step / 86400) . "天前";
        } elseif ($step < 2592000 && $step >= 604800) {
            return intval($step / 604800) . "星期前";
        } elseif ($step < 31536000  && $step >= 2592000) {
            return intval($step / 2592000) . "个月前";
        } else {
            return intval($step / 31536000) . "年前";
        }
    }
}

// 使用示例
echo DateTimeUtil::get_instance()->get_chinese_friendly_datetime(time()-24*3600*365*12);