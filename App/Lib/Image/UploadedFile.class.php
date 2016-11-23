<?php

namespace Lib\Image;

/**
 * 获取上传的文件路径
 * Class UploadedFile
 * TODO: 定义找不到图片时返回的图片
 */
class UploadedFile {

    /**
     * 获取基础的文件访问路径, 不带文件名
     * @param string $serverId
     * @param string $fileType
     * @return string
     */
    public static function getBaseUrl($serverId = 'fs', $fileType = 'project') {
        $conf = C("uploadImages"); // Y::paramsConfig('uploadImages');
        $url = sprintf('%s/%s', $conf['server'][$serverId], $fileType);
        return $url;
    }

    /**
     * 获取基础的文件存储路径, 不带文件名
     * @param string $serverId
     * @param string $fileType
     * @return string
     */
    public static function getBasePath($serverId = 'fs', $fileType = 'project') {
        $conf = C("uploadImages"); // Y::paramsConfig('uploadImages');
        return $conf['path'][$fileType];
    }

    /**
     * 获取带文件名的文件存储路径
     * @param string $serverId
     * @param $fileType
     * @param $fileName
     * @return string
     */
    public static function getFullPath($serverId = 'fs', $fileType, $fileName) {
        return self::getBasePath($serverId, $fileType) . '/' . $fileName;
    }

    /**
     * 获取逽文件名的文件访问路径
     * @param string $serverId
     * @param $fileType
     * @param $fileName
     * @return string
     */
    public static function getFullUrl($serverId = 'fs', $fileType, $fileName) {
        return self::getBaseUrl($serverId, $fileType) . '/' . $fileName;
    }

    /**
     * 获取默认的点位图片
     * @param $fileType
     * @return string
     */
    public static function getPlaceHolderImage($fileType) {
        $conf = C("uploadImages"); // Y::paramsConfig('uploadImages');
        if (isset($conf['placeholder'][$fileType])) { 
            return $conf['placeholder'][$fileType];
        }
        return $conf['placeholder']['default'];
    }

    /**
     * 获得图片的物理地址
     * @version 2013-6-19
     * @author wwpeng
     * @param unknown_type $_file_hash
     * @param unknown_type $_size
     * @return string|boolean
     */
    public static function getFilePath($_file_hash, $_size = false) {
        $temp = explode('_', $_file_hash);
        if (count($temp) == 4) {
            list($fileType, $fileServer, $fileName, $fileExt) = $temp;
            if ($_size == false) {
                $fileName = $fileName . '.' . $fileExt;
            } else {

                if (!isset($_size[1]) || empty($_size[1])) {
                    $_size[1] = 0;
                }
                $fileName = sprintf('%s_%s_%s.%s', $_size[0], $_size[1], $fileName, $fileExt);
            }
            return self::getFullPath($fileServer, $fileType, $fileName);
        }
        return false;
    }

    /**
     * 通过文件HASH获取文件路径的工厂
     * @param $file_hash 格式 : fileType_fileserver_filename_fileExt
     * @param array $size
     */
    public static function getFileUrl($_file_hash, $_size = array(100, 100), $defaultType = 'default') {
        if (strstr($_file_hash, 'http://')) {
            return $_file_hash;
        }
        if (!isset($_size[1]) || empty($_size[1])) {
            $_size[1] = 0;
        }
        $temp = explode('_', $_file_hash);
        if (is_array($temp) && count($temp) == 4) {
            list($fileType, $fileServer, $fileName, $fileExt) = $temp;
        } else {

            return self::getPlaceHolderImage($defaultType);
        }
        
        $callFunctionName = sprintf('get%sUrl', ucfirst($fileType));
        
        if (!method_exists(__CLASS__, $callFunctionName)) {
            return self::getPlaceHolderImage($defaultType);
        }
        return self::$callFunctionName($fileServer, $fileType, $fileName, $fileExt, $_size);
    }

    /**
     * 对文件存储生成HASH文件名
     * @param $fileType
     * @param $fileServer
     * @param $fileName
     * @param $fileExt
     * @return string
     */
    public static function getHashFileName($fileType, $fileServer, $fileName, $fileExt) {
        return sprintf('%s_%s_%s_%s', $fileType, $fileServer, $fileName, $fileExt);
    }

    /**
     * ==============================================
     * 工厂生产区
     * ================================================
     */
    private static function getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size) {
        $_baseUrl = self::getBaseUrl($file_server, $fileType);
        if(!$size || ( count($size)==1 && $size[0]=0 ) ||   ( $size[0]==0 && $size[1]==0) ){
            return sprintf('%s/%s.%s', $_baseUrl, $file_name, $fileExt);
        }
        $_basePath = self::getBasePath($file_server, $fileType);
        $fileRoute = sprintf('%s/%d_%d_%s.%s', $_baseUrl, $size[0], $size[1], $file_name, $fileExt);
        $fileDirRoute = sprintf('%s/%d_%d_%s.%s', $_basePath, $size[0], $size[1], $file_name, $fileExt);

        $resizeFile = ImageResize::run($size, $file_name, $fileExt, $_basePath);
       
        //如果生产失败返回默认的点位图片
        if (false === $resizeFile) {
            $fileRoute = self::getPlaceHolderImage($fileType);
        }
        return $fileRoute;
    }

    private static function getSupportUrl($file_server, $fileType, $file_name, $fileExt, $size) {
        return self::getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size);
    }

    private static function getEventUrl($file_server, $fileType, $file_name, $fileExt, $size) {
        return self::getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size);
    }

    private static function getTeamUrl($file_server, $fileType, $file_name, $fileExt, $size) {
        return self::getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size);
    }

    private static function getProjectUrl($file_server, $fileType, $file_name, $fileExt, $size) {
        return self::getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size);
    }

    private static function getUserUrl($file_server, $fileType, $file_name, $fileExt, $size) {
        return self::getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size);
    }

    private static function getIdcardUrl($file_server, $fileType, $file_name, $fileExt, $size) {
        return self::getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size);
    }

    private static function getOrganizationUrl($file_server, $fileType, $file_name, $fileExt, $size) {
        return self::getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size);
    }

    private static function getBannerUrl($file_server, $fileType, $file_name, $fileExt, $size) {
        return self::getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size);
    }

    private static function getActiveUrl($file_server, $fileType, $file_name, $fileExt, $size) {
        return self::getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size);
    }
    private static function getGroupUrl($file_server, $fileType, $file_name, $fileExt, $size) {
        return self::getImageUrlPublic($file_server, $fileType, $file_name, $fileExt, $size);
    }

}
