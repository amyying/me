<?php
/**
 * 获取区域信息工具类
 * 包括获取用户ip、用户地址、用户所在地天气
 *
 * 使用示例：
 * require 'RegionInfoUtil.php';
 * RegionInfoUtil::get_user_city_by_ip('218.129.157.159');
 *
 * wei.chungwei@gmail.com
 * 2013-11-04
 */

//date_default_timezone_set("Asia/ShangHai");

class RegionInfoUtil {

    const STATUS_SUCCESS = 1; // 验证成功
    const STATUS_ERROR_EMPTY_ARGS = -1001; // 传入的参数为空
    const STATUS_ERROR_ILLEGAL_FORMAT = -1002; // 格式错误
    const STATUS_ERROR_REQUEST = -1008; // 根据ip获取城市信息失败

    const STATUS_ERROR_UNKNOWN = -9999; // 系统未知错误

    /**
     * 获取用户ip
     * 2013-11-12
     * @return string 用户ip，如 218.147.125.168
     */
    public static function get_user_ip() {
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
     * 根据ip获取所在地信息
     * 使用的是淘宝的接口，参见淘宝文档 http://ip.taobao.com/instructions.php
     * 2013-11-12
     * @param $ip
     * @return array
     */
    public static function get_user_city_by_ip($ip) {
        $ip = trim(strval($ip));
        if (empty($ip)) {
            return array('code' => self::STATUS_ERROR_EMPTY_ARGS, 'data' => 'empty ip is not allowed.');
        }

        $url = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
        try {
            $tmp = file_get_contents($url);
            if ($tmp) {
                $tmp = json_decode($tmp, true);
                $ret = array();
                $ret['code'] = (isset($tmp['code']) && $tmp['code'] === 0) ? 1 : self::STATUS_ERROR_REQUEST;
                $ret['data'] = isset($tmp['data']) ? $tmp['data'] : array();
                return $ret;
            }
            return array('code' => self::STATUS_ERROR_REQUEST, 'data' => 'get user region info failed.');
        } catch (Exception $e) {
            return array('code' => self::STATUS_ERROR_UNKNOWN, 'data' => 'system error.');
        }

    }

    /**
     * 获取城市天气预报信息
     * 使用的是百度车联网接口，文档参见http://developer.baidu.com/map/carapi-7.htm
     *
     * 2013-11-12
     * @param $location
     * @return array|mixed
     */
    public static function get_location_weather($location) {
        $location = trim(strval($location));
        if (empty($location)) {
            return array('code' => self::STATUS_ERROR_EMPTY_ARGS, 'data' => 'empty location is not allowed.');
        }

        $location = urlencode($location);
        $allow_key = '6eac22ca4fe669f142ba7626db67f8f4'; // 这个要自己申请哦，参看文档
        $url = "http://api.map.baidu.com/telematics/v3/weather?location={$location}&output=json&ak={$allow_key}";
        //echo $url;
        try {
            $tmp = file_get_contents($url);
            if ($tmp) {
                $tmp = json_decode($tmp, true);
                $ret['code'] = (isset($tmp['status']) && $tmp['status'] === 'success') ? 1 : self::STATUS_ERROR_REQUEST;
                $ret['data'] = $tmp;
                return $ret;
            }
            return array('code' => self::STATUS_ERROR_REQUEST, 'data' => 'get user region info failed.');
        } catch (Exception $e) {
            return array('code' => self::STATUS_ERROR_UNKNOWN, 'data' => 'system error.');
        }
    }
}

// 使用示例
//$ip = RegionInfoUtil::get_user_ip();
//$ip = '218.17.157.129';
//echo '<pre>'; print_r(RegionInfoUtil::get_user_city_by_ip($ip));
//echo '<pre>'; print_r(RegionInfoUtil::get_location_weather('深圳'));