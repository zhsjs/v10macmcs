<?php

namespace app\admin\controller;

use think\Db;



class Linkurl extends Base
{

    public function index(){
        echo '欢迎使用短链接';
    }


    /**
     * 生成短链接
     * @return mixed
     */
    public function shorturl()
    {
        $param = input();
        $ids = $param['ids']?$param['ids']:'';

        $datas=[];
        if(!empty($ids)) {
            $where = [];
            $where['vod_id'] = ['in', $ids];
            $listvod = model('Vod')::all($ids);
            foreach($listvod as $key=>$row) {

                //是否开启分享域名
                if($domains = get_addon_config('adminlinkshort')['sharedomaincheck']=='start'){
                    //随机获取
                    $share_url=$this->getDomains(1);
                }else{
                    //固定主域名
                    $share_url= $_SERVER['HTTP_HOST'];
                }


                //是否开启落地域名
                if($domains = get_addon_config('adminlinkshort')['luodidomaincheck']=='start'){
                    //自定义的url规则
                    $newurl = 'http://'.$share_url.'/index.php/mylink/myluodi?'.$this->create_random_string(6).$row['vod_id'].$this->create_random_string(4);

                }else{
                    //不开启落地多域名 url规则是默认的，默认的分为动态 和 静态2种
                    // 默认是动态   动态 vod/detail/id/13.html  静态默认的 vodhtml/{id}/index
                    // view 'vod_detail' => '0', 是动态  2是静态
                    if(config('maccms')['view']['vod_detail'] ==0){
                        //静态
                        $alink = 'vod/detail/id/'.$row['vod_id'].'.html';
                    }else{
                        //动态
                        //获取动态 url规则  默认的 vodhtml/{id}/index
                        $urlrule = config('maccms')['path']['vod_detail'];
                        $pos  =  strpos ( $urlrule ,  '{id}' );
                        if ( $pos  ===  false ) {
                            echo '静态规则只支持带{id}的格式，不支持{md5},{en}等其他格式';
                            exit;
                        }else{
                            $alink=str_replace("{id}",$row['vod_id'],$urlrule);
                        }
                    }
                     $newurl = 'http://'.$share_url.'/'.$alink;
                }

                //生成短链接
                $source= 1681459862;
                $newurl2=$this->getSinaShortUrl($source, $newurl);
                $datas[] =[
                    'name' =>$row['vod_name'],
                    'surl' =>$newurl2[0]['url_short'],
                ] ;

            }

            $this->assign('datas',$datas);
            return $this->fetch('admin@linkurl/index');;

        }else {
            return $this->error('error');
        }
    }


    /**
     * 生成指定长度的字符串
     * by www.jbxue.com
     */
    function create_random_string($random_length) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $random_string = '';
        for ($i = 0; $i < $random_length; $i++) {
            $random_string .= $chars [mt_rand(0, strlen($chars) - 1)];
        }
        return $random_string;
    }


    /**
     * 检查分享域名 是否被封
     * @return mixed
     */
    public function checkdomainshare()
    {
        $domaintype = 'mode1';
        $domains='';
        if( get_addon_config('adminlinkshort')){
            $domains = get_addon_config('adminlinkshort')[$domaintype];
        }

        $arr_domains=[];
        if( $domains!='') {
            $arr_domains = explode('|',  $domains);
            $arr_domains=array_filter($arr_domains);
            sort($arr_domains);
            // print_r($arr_domains);
        }

            // echo  '开启';

            $token='76ecec993d41d69d42025efd30ba70d5';
            $check_url='http://wz.tkc8.com/manage/api/check?token=';
            $ret='';$feng=[];
            foreach($arr_domains as $v){

                if(!is_null($v) && strlen($v)>0) {
                    $url = $check_url . $token . '&url=' . $v;
                    $ch2 = curl_init();

                    curl_setopt($ch2, CURLOPT_URL, $url);
                    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch2, CURLOPT_HEADER, false);
                    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch2, CURLOPT_URL, $url);
                    curl_setopt($ch2, CURLOPT_REFERER, $url);
                    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
                    $ret = curl_exec($ch2);
                }

                if(!is_null($ret) && !is_null($v) && strlen($v)>0){
                    $arr_ret = json_decode($ret,true);
                    //code=9904  去掉这个域名，短信通知
                    if(!is_null($arr_ret['code']) && $arr_ret['code']==9904){
                        //域名被封了,修改去掉被封域名

                        $feng[] = $v;
                        //print_r( $p->sendSmsCode('3892820','13697314406',$v,'5') );
                    }

                    //code = 139  没有权限，短信通知
                    if(!is_null($arr_ret['code']) && $arr_ret['code']==139){
                        // $p->sendSmsCode('6272','13888888888','','5');
                    }

                }

                sleep(3);
            }

            //  $feng =[ 'www.baidu24.com','www.baidu2.com'];

            $arr3=array_diff($arr_domains,$feng);
            $mode1_value = implode($arr3,'|');
            $config['mode1'] = $mode1_value;
            set_addon_config('adminlinkshort', $config, $writefile = true);

            echo '检查完成，并修改掉已经被微信封的域名';

    }

    /**
     * 检查落地域名是否被封
     */
    public function checkdomainluodi()
    {
        $domaintype = 'mode2';
        $domains='';
        if( get_addon_config('adminlinkshort')){
            $domains = get_addon_config('adminlinkshort')[$domaintype];
        }

        $arr_domains=[];
        if( $domains!='') {
            $arr_domains = explode('|',  $domains);
            $arr_domains=array_filter($arr_domains);
            sort($arr_domains);
            // print_r($arr_domains);
        }

            $token='76ecec993d41d69d42025efd30ba70d5';
            $check_url='http://wz.tkc8.com/manage/api/check?token=';
            $ret='';$feng=[];
            foreach($arr_domains as $v){

                if(!is_null($v) && strlen($v)>0) {
                    $url = $check_url . $token . '&url=' . $v;
                    $ch2 = curl_init();

                    curl_setopt($ch2, CURLOPT_URL, $url);
                    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch2, CURLOPT_HEADER, false);
                    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch2, CURLOPT_URL, $url);
                    curl_setopt($ch2, CURLOPT_REFERER, $url);
                    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
                    $ret = curl_exec($ch2);
                }

                if(!is_null($ret) && !is_null($v) && strlen($v)>0){
                    $arr_ret = json_decode($ret,true);
                    //code=9904  去掉这个域名，短信通知
                    if(!is_null($arr_ret['code']) && $arr_ret['code']==9904){
                        //域名被封了,修改去掉被封域名

                        $feng[] = $v;
                        //print_r( $p->sendSmsCode('3892820','13697314406',$v,'5') );
                    }

                    //code = 139  没有权限，短信通知
                    if(!is_null($arr_ret['code']) && $arr_ret['code']==139){
                        // $p->sendSmsCode('6272','13888888888','','5');
                    }

                }

                sleep(3);
            }


            //  $feng =[ 'www.baidu24.com','www.baidu2.com'];

            $arr3=array_diff($arr_domains,$feng);

            $mode1_value = implode($arr3,'|');


            $config['mode1'] = $mode1_value;

            set_addon_config('adminlinkshort', $config, $writefile = true);

            echo '检查完成，并修改掉已经被微信封的域名';


    }


    function getSinaShortUrl($source, $url_long){

        // 参数检查
        if(empty($source) || !$url_long){
            return false;
        }

        // 参数处理，字符串转为数组
        if(!is_array($url_long)){
            $url_long = array($url_long);
        }

        // 拼接url_long参数请求格式
        $url_param = array_map(function($value){
            return '&url_long='.urlencode($value);
        }, $url_long);

        $url_param = implode('', $url_param);

        // 新浪生成短链接接口
        $api = 'http://api.t.sina.com.cn/short_url/shorten.json';

        // 请求url
        $request_url = sprintf($api.'?source=%s%s', $source, $url_param);

        $result = array();

        // 执行请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        $data = curl_exec($ch);
        if($error=curl_errno($ch)){
            return false;
        }
        curl_close($ch);

        $result = json_decode($data, true);

        return $result;

    }

    /**
     * @param $id  $id=1 shared 分享域名   $id=2 落地域名
     * 返回随机的1个域名
     */
    public function getDomains($id)
    {

        $domaintype='';
        if(!empty($id)){
            if($id==1)
            {
                $domaintype = 'mode1';
            }
            if($id==2)
            {
                $domaintype = 'mode2';
            }
        }

        $domains='';
        if( get_addon_config('adminlinkshort')){
            $domains = get_addon_config('adminlinkshort')[$domaintype];
        }

        $arr_doamins=[];
        if( $domains!='') {
            $arr_doamins = explode('|',  $domains);
            $arr_doamins=array_filter($arr_doamins);
            sort($arr_doamins);
            // print_r($arr_doamins);
        }

        $getdomain_url = $arr_doamins[rand(0,count($arr_doamins)-1)];

        return $getdomain_url;

    }
}

