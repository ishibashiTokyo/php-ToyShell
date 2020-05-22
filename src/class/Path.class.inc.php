<?php
namespace ishibashitokyo;

class Path
{
    static public function is_phar()
    {
        if (strlen(\Phar::running()) > 0) {
            return true;
        }
        return false;
    }

    static public function file($file_path)
    {
        if (self::is_phar()) {
            return \Phar::running() . '/src/' . $file_path;
        } else {
            return __DIR__ . '/../' . $file_path;
        }
    }
}