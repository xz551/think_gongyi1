<?php
namespace Lib;
/**
 * 学历数据源
 * @version 2013-12-17
 * @author wwpeng
 *
 */
class EducationData
{
	public static function getData()
	{
		return array (
				'初中'=>'初中',
				'中技'=>'中技',
				'高中'=>'高中',
				'大专'=>'大专',
				'本科'=>'本科',
				'硕士'=>'硕士',
				'博士'=>'博士',
				'其他'=>'其他'
			);
	}
	/**
	 * 检查输入的最高学历是否正确
	 * @version 2013-12-17
	 * @author wwpeng
	 * @param unknown_type $data
	 * @return unknown|string
	 */
	public static function getCheckEducation($data)
	{
		$educationData = self::getData();
		
		if (isset($educationData[$data]))
		{
			return $educationData[$data];
			
		}else{
			
			return '';
		}
	} 
}