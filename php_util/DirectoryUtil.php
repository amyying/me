<?php

class DirectoryUtil {

    public static function create_dir($dir) {
        if ($dir) {
            try {
                if (!is_dir($dir)) {
                    if (FALSE == mkdir($dir, 0646, TRUE)) {
                        LogUtil::instance()->warn("create $dir failed. please try it again or create manul.");
                    }
                }
            } catch (Exception $e) {
                LogUtil::instance()->warn("create $dir failed ".basename(__FILE__).':'.__LINE__.' : '.$e->getMessage());
            }
        }
    }
}