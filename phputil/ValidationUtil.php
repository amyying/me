<?php
/**
 * 验证通用类
 * 包括验证用户邮箱、qq、11位手机号、15/18位身份证号、用户名、邮编等，
 * 正确返回1，否则返回<-1000
 *
 * 使用示例：
 * require 'ValidationUtil.php';
 * ValidationUtil::get_instance()->validate_email('wei.chungwei@gmail.com');
 * output:
 * 1
 *
 * wei.chungwei@gmail.com
 * 2013-11-12
 */

class ValidationUtil {
    private static $_obj_instance;

    const STATUS_SUCCESS = 1; // 验证成功
    const STATUS_ERROR_EMPTY_ARGS = -1001; // 传入的参数为空
    const STATUS_ERROR_ILLEGAL_FORMAT = -1002; // 格式错误
    const STATUS_ERROR_ILLEGAL_DOMAIN = -1003; // 域名验证失败
    const STATUS_ERROR_USERNAME_LONG = -1004; // 用户名过长
    const STATUS_ERROR_USERNAME_SHORT = -1005; // 用户名过短
    const STATUS_ERROR_ILLEGAL_DATE = -1006; // 无效的身份证日期
    const STATUS_ERROR_ILLEGAL_IP = -1007; // 不合法ip

    const USERNAME_MAX_LENGTH = 32; // 用户名最大长度
    const USERNAME_MIN_LENGTH = 6; // 用户名最小长度
    const PASSWORD_MAX_LENGTH = 32; // 密码最大长度
    const PASSWORD_MIN_LENGTH = 6; // 密码最小长度

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

    /**
     * validate an email
     * 2013-11-02
     * @param  [type] $email [description]
     * @return [type]        [description]
     */
    public function validate_email($email) {
        $email = trim($email);

        // 邮箱不能为空
        if (!$email) {
            return self::STATUS_ERROR_EMPTY_ARGS;
        }

        // 邮箱用户名长度是4-32，只能包含大小写英文字母、数字和.-域名最长不能超过6级
        $pattern = "/^[\w]{1}[\.\w-]{3,31}@[\w]+(\.[\w]+){1,5}$/";
        if (0 == preg_match($pattern, $email)) {
            return self::STATUS_ERROR_ILLEGAL_FORMAT;
        }

        // 检验域名是否有效
        $arr_info = explode("@", $email);
        if (function_exists("checkdnsrr")) {
            $domain = isset($arr_info[1]) ? $arr_info[1] : '';
            try {
                if (!checkdnsrr($domain, "MX")) {
                    return self::STATUS_ERROR_ILLEGAL_DOMAIN;
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        return self::STATUS_SUCCESS;
    }

    /**
     * validate a qq num
     * 2013-11-02
     * @param  [type] $qq [description]
     * @return [type]     [description]
     */
    public function validate_qq($qq) {
        $qq = trim(strval($qq));

        if (empty($qq)) {
            return self::STATUS_ERROR_EMPTY_ARGS;
        }

        // qq号码不能以0开头，长度必须大于4位数
        $pattern = "/^[1-9]{1}[0-9]{4,}$/";
        if (!preg_match($pattern, $qq)) {
            return self::STATUS_ERROR_ILLEGAL_FORMAT;
        }

        return self::STATUS_SUCCESS;
    }

    /**
     * validate a mobile number
     * 2013-11-03
     * @param  [type] $number [description]
     * @return [type]         [description]
     */
    public function validate_mobile_num($number) {
        $number = trim(strval($number));

        if (empty($number)) {
            return self::STATUS_ERROR_EMPTY_ARGS;
        }

        // 手机号必须是11位数，且以1开头,目前可以匹配13*、14*、15*、18*
        // 能匹配的格式包括13838384388和138-3838-4388
        $pattern = "/^1(3|4|5|8)+?[0-9]+?(-?[\d]{4}){2}$/";
        if (!preg_match($pattern, $number)) {
            return self::STATUS_ERROR_ILLEGAL_FORMAT;
        }

        return self::STATUS_SUCCESS;
    }

    /**
     * validate a username
     * 2013-11-03
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
    public function validate_username($username) {
        $username = trim($username);

        if (empty($username)) {
            return self::STATUS_ERROR_EMPTY_ARGS;
        }

        // 这是要注意中英文混合的名字，并把中文看做是两个英文字符的情况
        // 用户名长度6-32
        $len = (strlen($username) + mb_strlen($username,'UTF-8')) / 2;
        if ($len > self::USERNAME_MAX_LENGTH) {
            return self::STATUS_ERROR_USERNAME_LONG;
        } elseif ($len < self::USERNAME_MIN_LENGTH) {
            return self::STATUS_ERROR_USERNAME_SHORT;
        }

        $pattern = "/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/i";
        if (!preg_match($pattern, $username)) {
            return self::STATUS_ERROR_ILLEGAL_FORMAT;
        }

        return self::STATUS_SUCCESS;
    }

    /**
     * validate an ID card number
     * 2013-11-03
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function validate_idcard_num($id) {
        $id = trim(strval($id));

        if (empty($id)) {
            return self::STATUS_ERROR_EMPTY_ARGS;
        }

        $len = strlen($id);
        $birth_year = '';
        $birth_month = '';
        $birth_day = '';
        $pattern = '';

        // 针对18位和15位的身份证，二者的处理有所不同
        if (18 == $len) {
            $birth_year = substr($id, 6, 4);
            $birth_month = substr($id, 10, 2);
            $birth_day = substr($id, 12, 2);
            $pattern = "/^[1-9]{1}\d{5}(1|2)+?\d{3}(0|1)+?\d+?[0-3]+?\d+?\d{3}(\d|X)+?$/";
        } elseif (15 == $len) {
            $birth_year = substr($id, 6, 2);
            $birth_month = substr($id, 8, 2);
            $birth_day = substr($id, 10, 2);
            $pattern = "/^[1-9]{1}\d{5}[1-9]{2}(0|1)+?\d+?[0-3]+?\d+?\d{3}$/";
        } else {
            return self::STATUS_ERROR_ILLEGAL_FORMAT;
        }

        // 验证生日是否有效日期
        if (!checkdate($birth_month, $birth_day, $birth_year)) {
            return self::STATUS_ERROR_ILLEGAL_DATE;
        }

        if (!preg_match($pattern, $id)) {
            return self::STATUS_ERROR_ILLEGAL_FORMAT;
        }

        return true;
    }

    /**
     * validate a post code
     * 2013-11-04
     * @param  [type] $postcode [description]
     * @return [type]           [description]
     */
    public function validate_postcode($postcode) {
        $postcode = trim(strval($postcode));

        if (empty($postcode)) {
            return self::STATUS_ERROR_EMPTY_ARGS;
        }

        $pattern = "/^\d{6}$/i";
        if (!preg_match($pattern, $postcode)) {
            return self::STATUS_ERROR_ILLEGAL_FORMAT;
        }

        return self::STATUS_SUCCESS;
    }

    /**
     * validate an ip addr
     * 2013-11-12
     * @param $ip
     * @return int
     */
    public function validate_ip($ip) {
        $ip = trim(strval($ip));

        if (empty($ip)) {
            return self::STATUS_ERROR_EMPTY_ARGS;
        }

        $pattern = "/^((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)(\.((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]\d)|\d)){3}$/";
        if (!preg_match($pattern, $ip)) {
            return self::STATUS_ERROR_ILLEGAL_FORMAT;
        }

        $ip = bindec(decbin(ip2long($ip)));
        if (floatval($ip) <= 0) {
            return self::STATUS_ERROR_ILLEGAL_IP;
        }

        return self::STATUS_SUCCESS;
    }
}

// 使用示例
//echo ValidationUtil::get_instance()->validate_postcode('533304');
//echo '<br/>';
//echo ValidationUtil::get_instance()->validate_ip('255.255.255.255');