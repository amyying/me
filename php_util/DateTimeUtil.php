<?php

/**
 * 时间日期通用类
 *
 * wei.chungwei@gmail.com
 * 2013-11-14
 */

class DateTimeUtil {

    public static function get_chinese_standard_datetime($timestamp = null) {
        $format = "Y年m月d日 H点i分s秒";
        return date($format, $timestamp ? $timestamp : time());
    }

    public static function get_chinese_standard_time($timestamp = null) {
        $format = "H点i分s秒";
        return date($format, $timestamp ? $timestamp : time());
    }

    public static function get_chinese_standard_date($timestamp = null) {
        $format = "Y年m月d日";
        return date($format, $timestamp ? $timestamp : time());
    }

    public static function get_chinese_friendly_datetime($timestamp) {
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
echo DateTimeUtil::get_chinese_standard_datetime();