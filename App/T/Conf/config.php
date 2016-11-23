<?php
return array( 
    'HTML_CACHE_ON' => false, // ������̬����
    'HTML_CACHE_TIME' =>518400,   // ȫ�־�̬������Ч�ڣ��룩
    'HTML_FILE_SUFFIX' => '.html', // ���þ�̬�����ļ���׺
    'HTML_CACHE_RULES' => array(
        // ���徲̬�������
        // �����ʽ1 ���鷽ʽ
        '*' =>array('{$_SERVER.REQUEST_URI|md5}')
    ),


    'interimCache'=>array(
        'label'=>'label-list',  //��ȡ���������ǩ����������ȣ��ı�ʶ
        'labelTime'=>86400,     //����ǩ�����ŵ�ʱ��
        'areaList'=>'area_list',//��ַ��ǩ
        'areaTime'=>86400,
    ),
    'qq'=>array(
        'APPID'=>'101248760',
        'APPKEY'=>'acb7f4685e4272c1f470255dab6e8623'
    )
);
