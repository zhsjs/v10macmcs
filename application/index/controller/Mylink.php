<?php

namespace app\index\controller;

use think\Db;



class Mylink extends Base
{

    /**
     * 落地解析域名
     */
    public function myluodi(){
        //判断是否开启落地域名

        $qstr = $_SERVER["QUERY_STRING"];
        $qstr2= mb_substr($qstr,6,strlen($qstr));
        $id= mb_substr($qstr2,0,-4);

        $luodi_url=$this->getDomains(2);
      //  $newurl = 'http://'.$luodi_url.'/index.php/mylink/myluodi?'.$this->create_random_string(6).'55'.$this->create_random_string(4);
     //   echo  $newurl;
        // 默认是动态   动态 vod/detail/id/13.html  静态默认的 vodhtml/{id}/index
        // view 'vod_detail' => '0', 是动态  2是静态

        if(config('maccms')['view']['vod_detail'] ==0){
            //静态
            $alink = 'vod/detail/id/'.$id.'.html';
        }else{
            //动态
            //获取动态 url规则  默认的 vodhtml/{id}/index
            $urlrule = config('maccms')['path']['vod_detail'];
            $pos  =  strpos ( $urlrule ,  '{id}' );
            if ( $pos  ===  false ) {
                echo '静态规则只支持带{id}的格式，不支持{md5},{en}等其他格式';
                exit;
            }else{
                $alink=str_replace("{id}",$id,$urlrule);
            }

        }
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        $luodiurl = $http_type.$luodi_url.'/'.$alink;

        header("location:".$luodiurl);

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
     * 检查域名
     * @return mixed
     */
    public function checkdomain()
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


        if($domains = get_addon_config('adminlinkshort')['domaincheck']=='start'){
           // echo  '开启';

            $die =[ 'www.baidu1.com','www.baidu3.com'];

            $arr3=array_diff($arr_domains,$die);

                $mode1_value = implode($arr3,'|');


            //去掉这个域名
          //  $output = get_addon_config('adminlinkshort')['value']." = " . var_export($die, TRUE);

        //  $bbb=  get_addon_config('adminlinkshort')['mode1'];

            $config['mode1'] = $mode1_value;
         //   set_addon_fullconfig('adminlinkshort',$config['mode1']);
/*            $fullconfig = get_addon_fullconfig('adminlinkshort');
            foreach ($fullconfig as $k => &$v) {
                if (isset($config[$v['name']])) {  echo $v['name']; exit;
                    $value = $v['type'] !== 'array' && is_array($config[$v['name']]) ? implode(',', $config[$v['name']]) : $config[$v['name']];
                    $v['value'] = $value;
                }
            }
*/

           set_addon_config('adminlinkshort', $config, $writefile = true);

            echo '检查完成，并修改掉已经被微信封的域名';


        }else{
            echo '域名检查未开启';
        }
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
            $arr_doamins = explode('|', $domains);
            $arr_doamins=array_filter($arr_doamins);
            sort($arr_doamins);
            // print_r($arr_doamins);
        }

        $getdomain_url = $arr_doamins[rand(0,count($arr_doamins)-1)];

        return $getdomain_url;

    }
}

