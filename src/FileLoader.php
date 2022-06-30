<?php

namespace Zorro;

class FileLoader
{

    /**
     * @param string ...$dirs
     * @return void
     */
    public static function loadDirFiles(string ...$dirs): void
    {
        $files = self::loadDirs(...$dirs);
        foreach ($files as $file) {
            require_once $file;
        }
    }

    /**
     * @param string ...$dirs
     * @return string[]
     */
    public static function loadDirs(string ...$dirs): array
    {
        $files = [];
        foreach ($dirs as $dir) {
            $files = array_merge(self::loadDir($dir), $files);
        }
        return $files;
    }

    /**
     * @param string $dir
     * @return string[]
     */
    public static function loadDir($dir): array
    {
        $files = glob($dir . "/*");
        $arr = [];
        foreach ($files as $file) {
            if (is_dir($file)) {
                $arr = array_merge(self::loadDir($file), $arr);
            } elseif (pathinfo($file)['extension'] == 'php') {
                $arr[] = $file;
            }
        }
        return $arr;
    }

}
