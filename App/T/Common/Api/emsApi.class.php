<?php
namespace T\Common\Api;
class emsApi {
	/**
	 * 将物流信息保存为静态数据
	 * @var unknown
	 */
	private static $expresses=array (
			'ems' => 'EMS',
			'shentong' => '申通快递',
			'shunfeng' => '顺丰速运',
			'yunda' => '韵达速递',
			'yuantong' => '圆通速递',
			'zhongtong' => '中通快递',
			'tiantian' => '天天快递',
			'huitongkuaidi' => '百世汇通',
			'zhaijisong' => '宅急送',
			'quanfengkuaidi' => '全峰快递',
			'debangwuliu' => '德邦',
			'youzhengguonei' => '包裹信件',
			'ztky' => '中铁物流',
			'aae' => 'AAE全球专递',
			'annengwuliu' => '安能物流',
			'baifudongfang' => '百福东方',
			'datianwuliu' => '大田物流',
			'fanyukuaidi' => '凡宇快递',
			'guotongkuaidi' => '国通快递',
			'hengluwuliu' => '恒路物流',
			'jd' => '京东快递',
			'jiajiwuliu' => '佳吉快运',
			'jialidatong' => '嘉里大通',
			'jiayiwuliu' => '佳怡物流',
			'jiayunmeiwuliu' => '加运美',
			'jinguangsudikuaijian' => '京广速递',
			'kangliwuliu' => '康力物流',
			'kuaijiesudi' => '快捷速递',
			'kuayue' => '跨越速递',
			'lianhaowuliu' => '联昊通',
			'minbangsudi' => '民邦速递',
			'minghangkuaidi' => '民航快递',
			'mingliangwuliu' => '明亮物流',
			'quanchenkuaidi' => '全晨快递',
			'quanyikuaidi' => '全一快递',
			'rufengda' => '如风达',
			'shenganwuliu' => '圣安物流',
			'shenghuiwuliu' => '盛辉物流',
			'suer' => '速尔快递',
			'suijiawuliu' => '穗佳物流',
			'tiandihuayu' => '天地华宇',
			'tnt' => 'TNT',
			'vancl' => '凡客配送',
			'wanxiangwuliu' => '万象物流',
			'xinbangwuliu' => '新邦物流',
			'xinfengwuliu' => '信丰物流',
			'yinjiesudi' => '银捷速递',
			'youshuwuliu' => '优速物流',
			'ytkd' => '运通中港快递',
			'yuanchengwuliu' => '远成物流',
			'zengyisudi' => '增益速递',
	);
	public function getems($key=''){
		return $key!='' ?  self::$expresses[$key]:self::$expresses;
	}
}