<?php

class RequestUtil {

    private static $timeout = 5; // 请求超时时间
    private static $encoding = 'UTF-8'; // 请求编码

    /**
     * 发送 get 请求
     * @param  [type]  $url     [请求地址]
     * @param  boolean $header  [TRUE返回头信息，FALSE不返回]
     * @param  boolean $getInfo [TRUE返回请求具体状态信息，FALSE不返回]
     * @return [type]           [description]
     */
    public static function doGet($url = '', $header = FALSE, $getInfo = FALSE) {
        if (empty($url) OR !is_string($url) OR !is_bool($header) OR !is_bool($getInfo)) {
            return FALSE;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, boolval($header));
        curl_setopt($ch, CURLOPT_ENCODING, self::$encoding);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $content = curl_exec($ch);
        if (TRUE === $getInfo) {
            $result = curl_getinfo($ch);
        }
        $result['request_content'] = $content;
        curl_close($ch);
        return TRUE === $getInfo ? $result : $result['request_content'];
    }

    /**
     * 发送 post 请求
     * 
     * @param  [type]  $url     [请求地址]
     * @param  [type]  $params  [请求参数]
     * @param  boolean $header  [TRUE返回头信息，FALSE不返回]
     * @param  boolean $getInfo [TRUE返回请求具体状态信息，FALSE不返回]
     * @return [type]           [description]
     */
    public static function doPost($url = '', $params = NULL, $header = FALSE, $getInfo = FALSE) {
        if (empty($url) OR !is_string($url) OR empty($params) OR !is_array($params) OR !is_bool($header) OR !is_bool($getInfo)) {
            return FALSE;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, boolval($header));
        curl_setopt($ch, CURLOPT_ENCODING, self::$encoding);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $content = curl_exec($ch);
        if (TRUE === $getInfo) {
            $result = curl_getinfo($ch);
        }
        $result['request_content'] = $content;
        curl_close($ch);
        return TRUE === $getInfo ? $result : $result['request_content'];
    }
}
