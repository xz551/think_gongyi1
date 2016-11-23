<?php

namespace Lib\Image;

class ImageResize {

    private static $_fileInfo;
    private static $_size;
    private static $_fileName;

    /**
     * @param $size
     * @param $fileName
     * @return bool
     */
    public static function run($size, $fileName, $fileExt, $_basePath) {
        if (!isset($size[1]) || empty($size[1])) {
            $size[1] = 0;
        }
        $fullFilePath = sprintf('%s/%s.%s', $_basePath, $fileName, $fileExt);
        $fullSavePath = sprintf('%s/%d_%d_%s.%s', $_basePath, $size[0], $size[1], $fileName, $fileExt);
        if (!file_exists($fullFilePath)) {
            return false;
        }

        if (file_exists($fullSavePath)) {
            return $fullSavePath;
        }


        //进行图片缩放
        $image = new Image();
        $image->setSrcFile($fullFilePath)->resize($size)->create($fullSavePath);
        if (!file_exists($fullSavePath)) {
            return false;
        }
        return $fullSavePath;
    }

}
