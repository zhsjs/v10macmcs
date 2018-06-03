<?php

return array (
    0 =>
        array (
            'name' => 'mode0',
            'title' => '说明',
            'type' => 'text',
            'content' =>
                array (
                    0 => '',
                ),
            'value' => '域名分为分享域名和落地域名，下面的内容里面，域名用‘|’分割，视频生成的短连接只对分享域名里的域名有效果. 短链接支持静态和动态规则，下面未样式，请用户修改为自己的域名组',
            'rule' => 'required',
            'msg' => '',
            'tip' => '',
            'ok' => '',
            'extend' => '',
        ),
    1 =>
        array (
            'name' => 'mode1',
            'title' => '分享域名',
            'type' => 'text',
            'content' =>
                array (
                    0 => '字段填写帮助',
                ),
            'value' => 'www.163dd.com|www.baidudd.com|www.qq2dd.com|www.qq3dd.com|www.qqdd.com|localhost',
            'rule' => 'required',
            'msg' => '',
            'tip' => '',
            'ok' => '',
            'extend' => '',
        ),
    2 =>
        array (
            'name' => 'mode2',
            'title' => '落地域名',
            'type' => 'text',
            'content' =>
                array (
                ),
            'value' => 'www.baidudd.com|www.qqdd.com|www.163dd.com|www.qq2dd.com|www.qq3dd.com',
            'rule' => 'required',
            'msg' => '',
            'tip' => '',
            'ok' => '',
            'extend' => '',
        ),
    3 =>
        array (
            'name' => 'sharedomaincheck',
            'title' => '分享域名',
            'type' => 'radio',
            'content' =>
                array (
                    'start' => '开启',
                    'stop' => '关闭',
                ),
            'value' => 'start',
            'rule' => 'required',
            'msg' => '',
            'tip' => '',
            'ok' => '',
            'extend' => '',
        ),
    4 =>
        array (
            'name' => 'luodidomaincheck',
            'title' => '落地域名',
            'type' => 'radio',
            'content' =>
                array (
                    'start' => '开启',
                    'stop' => '关闭',
                ),
            'value' => 'stop',
            'rule' => 'required',
            'msg' => '',
            'tip' => '',
            'ok' => '',
            'extend' => '',
        ),
);
