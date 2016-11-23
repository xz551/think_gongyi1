<?php
namespace T\Common\Api;
class CityApi
{
	/**
	 * 将省市信息保存为静态数据
	 * @version 2013-7-9
	 * @author wwpeng
	 * @var array
	 */
	private static $cityData = array (
					  0 => 
					  array (
					    1 => '北京市',
					    2 => '上海市',
					    3 => '天津市',
					    4 => '重庆市',
					    5 => '河北省',
					    6 => '山西省',
					    7 => '内蒙古自治区',
					    8 => '辽宁省',
					    9 => '吉林省',
					    10 => '黑龙江省',
					    11 => '江苏省',
					    12 => '浙江省',
					    13 => '安徽省',
					    14 => '福建省',
					    15 => '江西省',
					    16 => '山东省',
					    17 => '河南省',
					    18 => '湖北省',
					    19 => '湖南省',
					    20 => '广东省',
					    21 => '广西壮族自治区',
					    22 => '海南省',
					    23 => '四川省',
					    24 => '贵州省',
					    25 => '云南省',
					    26 => '西藏自治区',
					    27 => '陕西省',
					    28 => '甘肃省',
					    29 => '青海省',
					    30 => '宁夏回族自治区',
					    31 => '新疆维吾尔自治区',
					    32 => '台湾省',
					    33 => '香港特别行政区',
					    34 => '澳门特别行政区',
					  ),
						1 =>
						array (
								415 => '东城',
								416 => '西城',
								417 => '崇文',
								418 => '宣武',
								419 => '朝阳',
								420 => '丰台',
								421 => '石景山',
								422 => '海淀',
								423 => '门头沟',
								424 => '房山',
								425 => '通州',
								426 => '顺义',
								427 => '昌平',
								428 => '大兴',
								429 => '怀柔',
								430 => '平谷',
								431 => '密云',
								432 => '延庆',
						),
						3 =>
						array (
								433 => '和平',
								434 => '河东',
								435 => '河西',
								436 => '南开',
								437 => '河北',
								438 => '红桥',
								439 => '塘沽',
								440 => '汉沽',
								441 => '大港',
								442 => '东丽',
								443 => '西青',
								444 => '津南',
								445 => '北辰',
								446 => '武清',
								447 => '宝坻',
								448 => '宁河',
								449 => '静海',
								450 => '蓟县',
						),
						2 =>
						array (
								451 => '黄浦',
								452 => '卢湾',
								453 => '徐汇',
								454 => '长宁',
								455 => '静安',
								456 => '普陀',
								457 => '闸北',
								458 => '虹口',
								459 => '杨浦',
								460 => '闵行',
								461 => '宝山',
								462 => '嘉定',
								463 => '浦东新区',
								464 => '金山',
								465 => '松江',
								466 => '青浦',
								467 => '南汇',
								468 => '奉贤',
								469 => '崇明',
						),
						4 =>
						array (
								470 => '万州',
								471 => '涪陵',
								472 => '渝中',
								473 => '大渡口',
								474 => '江北',
								475 => '沙坪坝',
								476 => '九龙坡',
								477 => '南岸',
								478 => '北碚',
								479 => '万盛',
								480 => '双桥',
								481 => '渝北',
								482 => '巴南',
								483 => '黔江',
								484 => '长寿',
								485 => '江津',
								486 => '合川',
								487 => '永川',
								488 => '南川',
								489 => '綦江',
								490 => '潼南',
								491 => '铜梁',
								492 => '大足',
								493 => '荣昌',
								494 => '璧山',
								495 => '梁平',
								496 => '城口',
								497 => '丰都',
								498 => '垫江',
								499 => '武隆',
								500 => '忠县',
								501 => '开县',
								502 => '云阳',
								503 => '奉节',
								504 => '巫山',
								505 => '巫溪',
								506 => '石柱',
								507 => '秀山',
								508 => '酉阳',
								509 => '彭水',
						),
					  5 => 
					  array (
					    35 => '石家庄市',
					    36 => '唐山市',
					    37 => '秦皇岛市',
					    38 => '邯郸市',
					    39 => '邢台市',
					    40 => '保定市',
					    41 => '张家口市',
					    42 => '承德市',
					    43 => '沧州市',
					    44 => '廊坊市',
					    45 => '衡水市',
					  ),
					  6 => 
					  array (
					    46 => '太原市',
					    47 => '大同市',
					    48 => '阳泉市',
					    49 => '长治市',
					    50 => '晋城市',
					    51 => '朔州市',
					    52 => '晋中市',
					    53 => '运城市',
					    54 => '忻州市',
					    55 => '临汾市',
					    56 => '吕梁市',
					  ),
					  7 => 
					  array (
					    57 => '呼和浩特市',
					    58 => '包头市',
					    59 => '乌海市',
					    60 => '赤峰市',
					    61 => '通辽市',
					    62 => '鄂尔多斯市',
					    63 => '呼伦贝尔市',
					    64 => '巴彦淖尔市',
					    65 => '乌兰察布市',
					    66 => '兴安盟',
					    67 => '锡林郭勒盟',
					    68 => '阿拉善盟',
					  ),
					  8 => 
					  array (
					    69 => '沈阳市',
					    70 => '大连市',
					    71 => '鞍山市',
					    72 => '抚顺市',
					    73 => '本溪市',
					    74 => '丹东市',
					    75 => '锦州市',
					    76 => '营口市',
					    77 => '阜新市',
					    78 => '辽阳市',
					    79 => '盘锦市',
					    80 => '铁岭市',
					    81 => '朝阳市',
					    82 => '葫芦岛市',
					  ),
					  9 => 
					  array (
					    83 => '长春市',
					    84 => '吉林市',
					    85 => '四平市',
					    86 => '辽源市',
					    87 => '通化市',
					    88 => '白山市',
					    89 => '松原市',
					    90 => '白城市',
					    91 => '延边朝鲜族自治州',
					  ),
					  10 => 
					  array (
					    92 => '哈尔滨市',
					    93 => '齐齐哈尔市',
					    94 => '鸡西市',
					    95 => '鹤岗市',
					    96 => '双鸭山市',
					    97 => '大庆市',
					    98 => '伊春市',
					    99 => '佳木斯市',
					    100 => '七台河市',
					    101 => '牡丹江市',
					    102 => '黑河市',
					    103 => '绥化市',
					    104 => '大兴安岭地区',
					  ),
					  11 => 
					  array (
					    105 => '南京市',
					    106 => '无锡市',
					    107 => '徐州市',
					    108 => '常州市',
					    109 => '苏州市',
					    110 => '南通市',
					    111 => '连云港市',
					    112 => '淮安市',
					    113 => '盐城市',
					    114 => '扬州市',
					    115 => '镇江市',
					    116 => '泰州市',
					    117 => '宿迁市',
					  ),
					  12 => 
					  array (
					    118 => '杭州市',
					    119 => '宁波市',
					    120 => '温州市',
					    121 => '嘉兴市',
					    122 => '湖州市',
					    123 => '绍兴市',
					    124 => '金华市',
					    125 => '衢州市',
					    126 => '舟山市',
					    127 => '台州市',
					    128 => '丽水市',
					  ),
					  13 => 
					  array (
					    129 => '合肥市',
					    130 => '芜湖市',
					    131 => '蚌埠市',
					    132 => '淮南市',
					    133 => '马鞍山市',
					    134 => '淮北市',
					    135 => '铜陵市',
					    136 => '安庆市',
					    137 => '黄山市',
					    138 => '滁州市',
					    139 => '阜阳市',
					    140 => '宿州市',
					    141 => '巢湖市',
					    142 => '六安市',
					    143 => '亳州市',
					    144 => '池州市',
					    145 => '宣城市',
					  ),
					  14 => 
					  array (
					    146 => '福州市',
					    147 => '厦门市',
					    148 => '莆田市',
					    149 => '三明市',
					    150 => '泉州市',
					    151 => '漳州市',
					    152 => '南平市',
					    153 => '龙岩市',
					    154 => '宁德市',
					  ),
					  15 => 
					  array (
					    155 => '南昌市',
					    156 => '景德镇市',
					    157 => '萍乡市',
					    158 => '九江市',
					    159 => '新余市',
					    160 => '鹰潭市',
					    161 => '赣州市',
					    162 => '吉安市',
					    163 => '宜春市',
					    164 => '抚州市',
					    165 => '上饶市',
					  ),
					  16 => 
					  array (
					    166 => '济南市',
					    167 => '青岛市',
					    168 => '淄博市',
					    169 => '枣庄市',
					    170 => '东营市',
					    171 => '烟台市',
					    172 => '潍坊市',
					    173 => '济宁市',
					    174 => '泰安市',
					    175 => '威海市',
					    176 => '日照市',
					    177 => '莱芜市',
					    178 => '临沂市',
					    179 => '德州市',
					    180 => '聊城市',
					    181 => '滨州市',
					    182 => '荷泽市',
					  ),
					  17 => 
					  array (
					    183 => '郑州市',
					    184 => '开封市',
					    185 => '洛阳市',
					    186 => '平顶山市',
					    187 => '安阳市',
					    188 => '鹤壁市',
					    189 => '新乡市',
					    190 => '焦作市',
					    191 => '濮阳市',
					    192 => '许昌市',
					    193 => '漯河市',
					    194 => '三门峡市',
					    195 => '南阳市',
					    196 => '商丘市',
					    197 => '信阳市',
					    198 => '周口市',
					    199 => '驻马店市',
					    200 => '济源市',
					  ),
					  18 => 
					  array (
					    201 => '武汉市',
					    202 => '黄石市',
					    203 => '十堰市',
					    204 => '宜昌市',
					    205 => '襄阳市',
					    206 => '鄂州市',
					    207 => '荆门市',
					    208 => '孝感市',
					    209 => '荆州市',
					    210 => '黄冈市',
					    211 => '咸宁市',
					    212 => '随州市',
					    213 => '恩施土家族苗族自治州',
					    214 => '仙桃市',
					    215 => '潜江市',
					    216 => '天门市',
					    217 => '神农架林区',
					  ),
					  19 => 
					  array (
					    218 => '长沙市',
					    219 => '株洲市',
					    220 => '湘潭市',
					    221 => '衡阳市',
					    222 => '邵阳市',
					    223 => '岳阳市',
					    224 => '常德市',
					    225 => '张家界市',
					    226 => '益阳市',
					    227 => '郴州市',
					    228 => '永州市',
					    229 => '怀化市',
					    230 => '娄底市',
					    231 => '湘西土家族苗族自治州',
					    510 => '吉首市',
					  ),
					  20 => 
					  array (
					    232 => '广州市',
					    233 => '深圳市',
					    234 => '珠海市',
					    235 => '汕头市',
					    236 => '韶关市',
					    237 => '佛山市',
					    238 => '江门市',
					    239 => '湛江市',
					    240 => '茂名市',
					    241 => '肇庆市',
					    242 => '惠州市',
					    243 => '梅州市',
					    244 => '汕尾市',
					    245 => '河源市',
					    246 => '阳江市',
					    247 => '清远市',
					    248 => '东莞市',
					    249 => '中山市',
					    250 => '潮州市',
					    251 => '揭阳市',
					    252 => '云浮市',
					  ),
					  21 => 
					  array (
					    253 => '南宁市',
					    254 => '柳州市',
					    255 => '桂林市',
					    256 => '梧州市',
					    257 => '北海市',
					    258 => '防城港市',
					    259 => '钦州市',
					    260 => '贵港市',
					    261 => '玉林市',
					    262 => '百色市',
					    263 => '贺州市',
					    264 => '河池市',
					    265 => '来宾市',
					    266 => '崇左市',
					  ),
					  22 => 
					  array (
					    267 => '海口市',
					    268 => '三亚市',
					    269 => '五指山市',
					    270 => '琼海市',
					    271 => '儋州市',
					    272 => '文昌市',
					    273 => '万宁市',
					    274 => '东方市',
					    275 => '澄迈县',
					    276 => '定安县',
					    277 => '屯昌县',
					    278 => '临高县',
					    279 => '白沙黎族自治县',
					    280 => '昌江黎族自治县',
					    281 => '乐东黎族自治县',
					    282 => '陵水黎族自治县',
					    283 => '保亭黎族苗族自治县',
					    284 => '琼中黎族苗族自治县',
					  ),
					  23 => 
					  array (
					    285 => '成都市',
					    286 => '自贡市',
					    287 => '攀枝花市',
					    288 => '泸州市',
					    289 => '德阳市',
					    290 => '绵阳市',
					    291 => '广元市',
					    292 => '遂宁市',
					    293 => '内江市',
					    294 => '乐山市',
					    295 => '南充市',
					    296 => '眉山市',
					    297 => '宜宾市',
					    298 => '广安市',
					    299 => '达州市',
					    300 => '雅安市',
					    301 => '巴中市',
					    302 => '资阳市',
					    303 => '阿坝藏族羌族自治州',
					    304 => '甘孜藏族自治州',
					    305 => '凉山彝族自治州',
					  ),
					  24 => 
					  array (
					    306 => '贵阳市',
					    307 => '六盘水市',
					    308 => '遵义市',
					    309 => '安顺市',
					    310 => '铜仁地区',
					    311 => '毕节地区',
					    312 => '黔西南布依族苗族自治州',
					    313 => '黔东南苗族侗族自治州',
					    314 => '黔南布依族苗族自治州',
					  ),
					  25 => 
					  array (
					    315 => '昆明市',
					    316 => '曲靖市',
					    317 => '玉溪市',
					    318 => '保山市',
					    319 => '昭通市',
					    320 => '丽江市',
					    321 => '普洱市',
					    322 => '临沧市',
					    323 => '楚雄彝族自治州',
					    324 => '红河哈尼族彝族自治州',
					    325 => '文山壮族苗族自治州',
					    326 => '西双版纳傣族自治州',
					    327 => '大理白族自治州',
					    328 => '德宏傣族景颇族自治州',
					    329 => '怒江傈僳族自治州',
					    330 => '迪庆藏族自治州',
					  ),
					  26 => 
					  array (
					    331 => '拉萨市',
					    332 => '昌都地区',
					    333 => '林芝地区',
					    334 => '山南地区',
					    335 => '日喀则地区',
					    336 => '那曲地区',
					    337 => '阿里地区',
					  ),
					  27 => 
					  array (
					    338 => '西安市',
					    339 => '铜川市',
					    340 => '宝鸡市',
					    341 => '咸阳市',
					    342 => '渭南市',
					    343 => '延安市',
					    344 => '汉中市',
					    345 => '榆林市',
					    346 => '安康市',
					    347 => '商洛市',
					  ),
					  28 => 
					  array (
					    348 => '兰州市',
					    349 => '嘉峪关市',
					    350 => '金昌市',
					    351 => '白银市',
					    352 => '天水市',
					    353 => '武威市',
					    354 => '张掖市',
					    355 => '平凉市',
					    356 => '酒泉市',
					    357 => '庆阳市',
					    358 => '定西市',
					    359 => '陇南市',
					    360 => '临夏回族自治州',
					    361 => '甘南藏族自治州',
					  ),
					  29 => 
					  array (
					    362 => '西宁市',
					    363 => '海东地区',
					    364 => '海北藏族自治州',
					    365 => '黄南藏族自治州',
					    366 => '海南藏族自治州',
					    367 => '果洛藏族自治州',
					    368 => '玉树藏族自治州',
					    369 => '海西蒙古族藏族自治州',
					  ),
					  30 => 
					  array (
					    370 => '银川市',
					    371 => '石嘴山市',
					    372 => '吴忠市',
					    373 => '固原市',
					    374 => '中卫市',
					  ),
					  31 => 
					  array (
					    375 => '乌鲁木齐市',
					    376 => '克拉玛依市',
					    377 => '吐鲁番地区',
					    378 => '哈密地区',
					    379 => '昌吉回族自治州',
					    380 => '博尔塔拉蒙古自治州',
					    381 => '巴音郭楞蒙古自治州',
					    382 => '阿克苏地区',
					    383 => '克孜勒苏柯尔克孜自治州',
					    384 => '喀什地区',
					    385 => '和田地区',
					    386 => '伊犁哈萨克自治州',
					    387 => '塔城地区',
					    388 => '阿勒泰地区',
					    389 => '石河子市',
					    390 => '阿拉尔市',
					    391 => '图木舒克市',
					    392 => '五家渠市',
					  ),
					  32 => 
					  array (
					    393 => '台北市',
					    394 => '高雄市',
					    395 => '基隆市',
					    396 => '台中市',
					    397 => '台南市',
					    398 => '新竹市',
					    399 => '嘉义市',
					    400 => '台北县',
					    401 => '宜兰县',
					    402 => '桃园县',
					    403 => '新竹县',
					    404 => '苗栗县',
					    405 => '台中县',
					    406 => '彰化县',
					    407 => '南投县',
					    408 => '云林县',
					    409 => '嘉义县',
					    410 => '台南县',
					    411 => '高雄县',
					    412 => '屏东县',
					    413 => '台东县',
					    414 => '花莲县',
					  ),
					  33 =>
					  array(
					  	511 => '香港岛',
					  	512 => '九龙',
					  	513 => '新界',
					  ),
					  34 =>
					  array(
					  	514 => '花地玛堂区',
					  	515 => '圣安多尼堂区',
					  	516 => '大堂区',
					  	517 => '望德堂区',
					  	518 => '风顺堂区',
					  	519 => '嘉模堂区',
					  	520 => '圣方济各堂区',
					  )
					);
	/**
	 * 根据Pid 获得 该类别下的所有地区的List
	 * @version 2013-5-26
	 * @author wwpeng
	 * @param int $pid
	 */
	public static function getDataList($pid = 0)
	{
		if ($pid === null || $pid === '' || !isset(self::$cityData[$pid]))
		{
			return array();
		}
		return self::$cityData[$pid];
	}
	/**
	 * 根据名称 查询城市ID
	 * @version 2013-7-1
	 * @author wwpeng
	 * @param Int $id
	 */
	public static function getID($name)
	{
		if (empty($name))
		{
			return '';
		}
		foreach (self::$cityData as $key=>$value)
		{
			foreach ($value as $k=>$v)
			{
				if ($name == mb_substr($v, 0, mb_strlen($name)))
				{
					return $k;
				}
			}
		}
		return '';
	}
	/**
	 * 根据城市名称/城市ID 获得 省ID
	 * @param int|string $city
	 */
	public static function getProvinceIdByCity($city)
	{
		if (!is_int($city))
		{
			$city = self::getID($city);
		}
		foreach (self::$cityData as $key=>$value)
		{
			if (array_key_exists($city, $value))
			{
				return $key;
			}
		} 
		return '';
	}
	/**
	 * 根据ID 查询城市名称
	 * @param Int $id
	 */
	public static function getName($id)
	{
		if (empty($id))
		{
			return "";
		}
		foreach (self::$cityData as $data)
		{
			if (isset($data[$id]))
			{
				return $data[$id];
			}
		}
	}
	/**
	 * 判断一个ID是否是一个省
	 * @param unknown_type $id
	 * @return boolean
	 */
	public static function isProvince($id)
	{
		$province = self::getDataList();
		if (isset($province[(int)$id]))
		{
			return true;
		}
		return false;
	}
}