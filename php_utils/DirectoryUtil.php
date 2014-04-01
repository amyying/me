<?php

/**
 * Util class for operate dir.
 * 
 * Author: wei.chungwei@gmail.com
 * Create: 2014-03-10
 * Update: 2014-03-11
 */
class DirectoryUtil {

    public static function create_dir($dir) {
        if ($dir) {
            try {
                if (!is_dir($dir)) {
                    if (FALSE == mkdir($dir, 0646, TRUE)) {
                        throw new Exception("create $dir failed. please try it again or create manully.");
                    }
                    return TRUE;
                }
            } catch (Exception $e) {
                throw new Exception("create $dir failed ".basename(__FILE__).':'.__LINE__.' : '.$e->getMessage());
            }
        }
        return FALSE;
    }
}