<?php
namespace app\index\controller;
use think\Controller;
use app\common\controller\All;

class Base extends All
{
    var $_group;
    var $_user;

    public function __construct()
    {
        parent::__construct();
        $this->check_site_status();
        $this->label_maccms();
        $this->label_user();
    }
    protected $beforeActionList = [
        'first',                     //执行任何方法之前都会执行这个first
        //    'second' =>  ['except'=>'hello'],   //除了hello方法以外的方法执行之前都会先执行一次second
        //    'three'  =>  ['only'=>'hello,data'], //仅在hello和data方法执行之前执行一次three
    ];

    /*
     * 检查是否要跳转一次域名
     */
    protected function first()
    {

        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        //  $allurl=$http_type.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $curdomain= $_SERVER['HTTP_HOST']; //可能是默认的域名 可能是分享的域名其中一个

        if( get_addon_config('adminlinkshort')['sharedomaincheck'] =='start') {
            $sharedomains = get_addon_config('adminlinkshort')['mode1'];
            $share_arr = explode('|',$sharedomains);
        }else{
            $share_arr=[];
        }

        if( get_addon_config('adminlinkshort')['luodidomaincheck'] =='start') {
            $luodidomains = get_addon_config('adminlinkshort')['mode2'];
            $luodi_arr = explode('|',$luodidomains);
        }else{
            $luodi_arr=[];
        }

        //在分享域名的里面
        if(count($share_arr)>0){
            //分享域名不是空的
            if(count($luodi_arr)>0 && in_array($curdomain,$share_arr)){
                $newurl=$http_type.$luodi_arr[rand(0,count($luodi_arr)-1)].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
                header('location: '.$newurl);
            }else{
                //  header('location: '.$allurl);
            }

        }else{
            //分享域名是空的，那就直接用当前的url
            //  header('location: '.$allurl);
        }


    }

    public function _empty()
    {
        header("HTTP/1.0 404 Not Found");
        echo  '<script>setTimeout(function (){location.href="'.MAC_PATH.'";},'.(2000).');</script>';
        $msg = '页面不存在';
        abort(404,$msg);
        exit;
    }

    protected function check_search($param)
    {
        if($GLOBALS['config']['app']['search'] !='1'){
            echo $this->error('搜索功能关闭中');
            exit;
        }

        if ( $param['page']==1 && mac_get_time_span("last_searchtime") < $GLOBALS['config']['app']['search_timespan']){
            echo $this->error("请不要频繁操作，搜索时间间隔为".$GLOBALS['config']['app']['search_timespan']."秒");
            exit;
        }

    }

    protected function check_site_status()
    {
        //站点关闭中
        if ($GLOBALS['config']['site']['site_status'] == 0) {
            $this->assign('close_tip',$GLOBALS['config']['site']['site_close_tip']);
            echo $this->fetch('public/close');
            die;
        }
    }

    protected function check_user_popedom($type_id,$popedom,$param=[],$flag='',$points=0,$trysee=0)
    {
        $user = $GLOBALS['user'];
        $group = $GLOBALS['user']['group'];

        $res = false;
        if(strpos(','.$group['group_type'],','.$type_id.',')!==false && !empty($group['group_popedom'][$type_id][$popedom])!==false){
            $res = true;
        }
        if($popedom==3){

            if($res===false && (empty($group['group_popedom'][$type_id][5]) || $trysee==0)){
                return ['code'=>3001,'msg'=>'您没有权限访问此数据，请升级会员','trysee'=>0];
            }
            elseif($group['group_id']<3 && empty($group['group_popedom'][$type_id][3]) && !empty($group['group_popedom'][$type_id][5]) && $trysee>0){
                return ['code'=>3002,'msg'=>'进入试看模式','trysee'=>$trysee];
            }
            elseif($group['group_id']<3 && $points>0  ){
                $where=[];
                $where['ulog_mid'] = 1;
                $where['ulog_type'] = $flag=='play' ? 4 : 5;
                $where['ulog_rid'] = $param['id'];
                $where['ulog_sid'] = $param['sid'];
                $where['ulog_nid'] = $param['nid'];
                $where['user_id'] = $user['user_id'];
                $where['ulog_points'] = $points;
                $res = model('Ulog')->infoData($where);
                if($res['code'] > 1) {
                    return ['code'=>3003,'msg'=>'观看此数据，需要支付【'.$points.'】积分，确认支付吗？','points'=>$points,'confirm'=>1,'trysee'=>0];
                }
            }
        }
        else{
            if($res===false){
                return ['code'=>1001,'msg'=>'您没有权限访问此页面，请升级会员组'];
            }
            if($popedom == 4){
                if( $group['group_id'] ==1 && $points>0){
                    return ['code'=>4001,'msg'=>'此页面为收费数据，请先登录后访问！','trysee'=>0];
                }
                elseif($group['group_id'] ==2 && $points>0){
                    $where=[];
                    $where['ulog_mid'] = 1;
                    $where['ulog_type'] = $flag=='play' ? 4 : 5;
                    $where['ulog_rid'] = $param['id'];
                    $where['ulog_sid'] = $param['sid'];
                    $where['ulog_nid'] = $param['nid'];
                    $where['user_id'] = $user['user_id'];
                    $where['ulog_points'] = $points;
                    $res = model('Ulog')->infoData($where);

                    if($res['code'] > 1) {
                        return ['code'=>4003,'msg'=>'下载此数据，需要支付【'.$points.'】积分，确认支付吗？','points'=>$points,'confirm'=>1,'trysee'=>0];
                    }
                }
            }
            elseif($popedom==5){
                if(empty($group['group_popedom'][$type_id][3]) && !empty($group['group_popedom'][$type_id][5])){
                    $where=[];
                    $where['ulog_mid'] = 1;
                    $where['ulog_type'] = $flag=='play' ? 4 : 5;
                    $where['ulog_rid'] = $param['id'];
                    $where['ulog_sid'] = $param['sid'];
                    $where['ulog_nid'] = $param['nid'];
                    $where['user_id'] = $user['user_id'];
                    $where['ulog_points'] = $points;
                    $res = model('Ulog')->infoData($where);

                    if($res['code'] == 1) {

                    }
                    elseif( $group['group_id'] <=2 && $points <= intval($user['user_points']) ){
                        return ['code'=>5001,'msg'=>'试看结束,是否支付[' . $points . ']积分观看完整数据？您还剩下[' . $user['user_points'] . ']积分，请先充值！','trysee'=>$trysee];
                    }
                    elseif( $group['group_id'] <3 && $points > intval($user['user_points']) ){
                        return ['code'=>5002,'msg'=>'对不起,观看此页面数据需要[' . $points . ']积分，您还剩下[' . $user['user_points'] . ']积分，请先充值！','trysee'=>$trysee];
                    }
                }
            }
        }

        return ['code'=>1,'msg'=>'权限验证通过'];
    }
}