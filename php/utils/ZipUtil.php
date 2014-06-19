<?php

/**
 * wei.chungwei@gmail.com
 * created: 2014-06-19
 */
class ZipUtil {
    /**
     * [decompress description]
     * 2014-06-19
     * @param  [type] $filename [description]
     * @param  string $dest_dir [description]
     * @return [type]           [description]
     */
    public static function decompress($filename, $dest_dir = './') {
        if (empty($filename) OR !preg_match("/.*\.zip$/i", $filename)) {
            return FALSE;
        }
        
        try {
            $obj_zip = new ZipArchive;
            if ($obj_zip->open($filename)) {
                $obj_zip->extractTo($dest_dir);
                $obj_zip->close();
                return TRUE;
            }
            return FALSE;
        } catch (Exception $e) {
            throw new Exception('exception at '.__FILE__.':'.$e->getLine().':'.$e->getMessage());
        }
    }
}

// var_dump(ZipUtil::decompress('ww.zip'));
