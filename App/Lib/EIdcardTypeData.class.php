<?php
namespace Lib;
/**
 * 证件类型
 * @version 2014-5-16
 * @author wwpeng
 *
 */
class EIdcardTypeData
{
    /**
     * 证件类型
     * 1 二代身份证
     * 2 港澳台身份证
     * 3 护照
     * @var int
     */
    const IDCARD_TYPE_SHENFENZHENG = 1;
    const IDCARD_TYPE_GANGAOTAI = 2;
    const IDCARD_TYPE_PASSPORT = 3;
    
	public static function getData()
	{
		return array (
            self::IDCARD_TYPE_SHENFENZHENG => '二代身份证',
            self::IDCARD_TYPE_GANGAOTAI => '港澳台身份证',
            self::IDCARD_TYPE_PASSPORT => '护照'
			);
	}
    
    /**
	 * 检查输入的最高学历是否正确
	 * @version 2013-12-17
	 * @author wwpeng
	 * @param unknown_type $data
	 * @return unknown|string
	 */
	public static function getCheckEIdcardType($data)
	{
		$idcardTypeData = self::getData();
		
		if (isset($idcardTypeData[$data]))
		{
			return $idcardTypeData[$data];
			
		}else{
			
			return '';
		}
	}
	
}

