<?php
class UploadImage
{
    /**
     * 上传文件
     * @param $file object 上传文件的对象
     * @param $fileType strint 文件上传项目类型
     * @return array(status, message)
     */
    public static function Upload($file, $fileType, $maxfilesize)
    {
        $fileExt = $file->extensionName;
        $fileServer = 'fs';
        $fileCleanName = Helper::getUniqueChar();
        $fileName = sprintf('%s.%s', $fileCleanName, $fileExt);

        //文件后缀不合适
        $uploadConf = Y::paramsConfig('uploadImages');
        $enableExt = $uploadConf['enableExt'];
        if( !in_array( strtolower($fileExt), $enableExt ) )
        {
            return array(false, '只可以上传 '.implode(',', $enableExt).' 格式图片文件');
        }
        if (empty($maxfilesize))
        {
        	$maxfilesize  = $uploadConf['maxFileSize'];
        }
        
        if( $file->size > $maxfilesize )
        {
            return array(false, '文件大小超出最大限制');
        }

        $uploadfile = UploadedFile::getFullPath($fileServer, $fileType, $fileName);
        
        if($file->saveAs($uploadfile))
        {
            $fileHashName = UploadedFile::getHashFileName($fileType, $fileServer, $fileCleanName, $fileExt);
            return array(true, UploadedFile::getFullUrl($fileServer, $fileType,$fileName), $fileHashName);

        }else{

            return array(false, self::getUploadError($file->error));
        }
    }

    /**
     * 取得上传错误
     * @param $error 错误id
     * @return string 错误说明
     * @TODO: 这里的错误提示方案要处理下, 太机器化了
     */
    private static function getUploadError($error){
        switch ($error){
            case 0:
                $err = '文件上传成功';
                break;
            case 1:
                $err = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
                break;
            case 2:
                $err = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
                break;
            case 3:
                $err = '文件只有部分被上传';
                break;
            case 4:
                $err = '没有文件被上传';
                break;
            case 5:
                $err = '找不到临时文件夹';
                break;
            case 6:
                $err = '找不到临时文件夹';
                break;
            case 7:
                $err = '文件写入失败';
                break;
            default:
                $err = '未知错误';
                break;
        }

        return $err;
    }
}