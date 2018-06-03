<?php
namespace app\admin\controller;
use think\Db;
use \think\Image;

class Imgurl extends Base
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $param = input();
       echo '欢迎使用缩略图插件';
    }

    /**
     * 生成缩略图
     * @return mixed
     */
    public function imgurl()
    {
        $param = input();
        $canid = $param['ids'];
        if (!is_null($canid)) {
            if (strpos($canid, ',') === false) {
                // echo '不存在逗号，就是一条数据';
                $result = model('Vod')->where('vod_pic_thumb','=','')->where('vod_pic','<>','')
                    ->where('vod_id',$canid)->select();
            } else {

                $arr_canid = explode(',', $canid);
                $arrvv='';
                foreach ($arr_canid as $vv) {
                    $arrvv .= $vv.",";
                }
                $arrvv = substr($arrvv, 0, strlen($arrvv) - 1);
                $result = model('Vod')->where('vod_pic_thumb','=','')->where('vod_pic','<>','')
                    ->where('vod_id','in',$arrvv)->select();

            }
            $datas = [];

            foreach($result as $row){
                $datas[$row['vod_id']] = [
                    'title' => $row['vod_name'],
                    'author' => $row['vod_actor'],
                    'actor' => $row['vod_director'],
                    'img' => $row['vod_pic'],
                ];

            }

            if (count($datas) > 0) {

                $nconfig='';
                if( get_addon_config('adminimgshort')){
                    $nconfig = get_addon_config('adminimgshort')['mode2'];
                }

                $keys = [];
                foreach ($datas as $key => $data) {

                    $author='';
                    if (isset($data['author']) && $data['author'] != '') {
                        $author = '导演: '.$data['author'];
                    }
                    $actor = '';
                    if (isset($data['actor']) && $data['actor'] != '') {
                        $actor = '主演: '.$data['actor'];
                    }
                    //图片上写文字
                    $word = [
                        'title' => isset($data['title']) ? $data['title'] : '影片名字未填写',
                        'author' => $author,
                        'actor' => $actor,
                    ];


                    $haibao = isset($data['img']) ? $data['img'] : 'imgmerge/haibao.jpg';  //海报图片
                    $sucai = 'imgmerge/sucai.png';    //素材图片
                    $haibaoIm = imagecreatefromstring(file_get_contents($haibao));
                    //获取海报原图的width height
                    $hbw = imagesx($haibaoIm);  //海报宽度
                    $hbh = imagesy($haibaoIm);  //海报高度

                    //创建完整的海报图片（压缩图片）
                    $hbok = imagecreatetruecolor(280, 398);
                    imagecopyresampled($hbok, $haibaoIm, 0,0, 0, 0, 280, 398, $hbw, $hbh);
                    //裁剪海报的图片中间部分
                    $hbcj = imagecreatetruecolor(280, 227);
                    imagecopy($hbcj, $hbok, 0,0, 0, 60, 280, 227);
                    //将裁减的图片进行高斯模糊处理
                    $hbcj = $this->blur($hbcj, 1);
                    //将裁减的中间部分进行拉伸并放到最终图片的底图上
                    $final = imagecreatetruecolor(750, 1100);
                    imagecopyresampled($final, $hbcj, 0, 0, 0, 0, 750, 610,280, 227);
                    //将素材加到底图上
                    $sucaiIm = imagecreatefrompng ($sucai);
                    imagesavealpha($sucaiIm, true);
                    imagecopy($final, $sucaiIm, 0, 0, 0, 0, 750, 1100);
                    //将完整海报加载到底图上
                    imagecopy($final, $hbok, 58, 190, 0, 0, 280, 398);
                    //将文字加到图片上
                    $colorIm = imagecreatetruecolor(256, 256);
                    $colorInt = imagecolorexact($colorIm, 33, 40, 53);  //获取颜色索引
                    $colorInt2 = imagecolorexact($colorIm, 134, 142, 155);  //获取颜色索引
                    imagefttext($final, 36, 0, 60, 692, $colorInt, 'imgmerge/PingFang-Medium.ttf', $word['title']);
                    imagefttext($final, 18, 0, 60, 752, $colorInt2, 'imgmerge/PingFang-Medium.ttf', $word['author']);
                    imagefttext($final, 18, 0, 60, 806, $colorInt2, 'imgmerge/PingFang-Medium.ttf', $word['actor']);

                    $hbum = imagecreatetruecolor(500, 733);  //图片大小压缩
                    imagecopyresampled($hbum, $final, 0,0, 0, 0, 500, 733, 750, 1100);

                    //图片画质压缩

                    //header('Content-type: image/png');
                    $fileName = time(). rand(1000, 9999). '.png';
                    imagepng($hbum, $fileName);



                    $pic =  ROOT_PATH.'/webpic/'.$fileName;
                    $response = Image::open($fileName)->save($pic);
                    $fileInfo = $fileName;

                    unlink($fileName);
                    imagedestroy($hbok);
                    imagedestroy($hbcj);
                    imagedestroy($final);
                    imagedestroy($colorIm);
                    imagedestroy($hbum);
                    imagedestroy($haibaoIm);
                    imagedestroy($sucaiIm);

                    if (!is_null($response)) {
                        $newres= '/webpic/'.$fileName;
                        $img = $nconfig . $newres; //movieimgs.oss-cn-beijing.aliyuncs.com/web\/15246445977163.png

                        //更新数据库
                       // $sql2 = "update mac_vod set vod_pic_thumb='" . $img . "' where vod_id=" . $key;
                        model('Vod')->where('vod_id',$key)->update(['vod_pic_thumb'=>$img]);
                        //echo $sql2;
                      //  Db::execute($sql2);
                        //$rs = $stmt->execute();
                        $keys[] = [$key];

                    }

                }

                //过滤没有生成的缩略图
                $arr_canids = explode(',', $canid);
                $result = array_reduce($keys, function ($result, $value) {
                    return array_merge($result, array_values($value));
                }, array());
                $new_diff = array_diff($arr_canids, $result);

                if (count($new_diff) > 0) {
                    echo '未成功生成缩略图的ID有';
                    echo implode(',', $new_diff); echo '<br>检查原始图片是否正常。<br>';
                }
                echo '操作完成';
            }
        }else{
            echo '没有满足条件的数据';
        }
    }


    /**
     * 图片高斯模糊
     * @param $gdImageResource  // 图片资源
     * @param int $blurFactor  //可选择的模糊程度  0使用   3默认   超过5时 极其模糊
     * @return mixed
     */
    public function blur($gdImageResource, $blurFactor = 3)
    {
        // blurFactor has to be an integer
        $blurFactor = round($blurFactor);

        $originalWidth = imagesx($gdImageResource);
        $originalHeight = imagesy($gdImageResource);

        $smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
        $smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));

        // for the first run, the previous image is the original input
        $prevImage = $gdImageResource;
        $prevWidth = $originalWidth;
        $prevHeight = $originalHeight;

        // scale way down and gradually scale back up, blurring all the way
        for($i = 0; $i < $blurFactor; $i += 1)
        {
            // determine dimensions of next image
            $nextWidth = $smallestWidth * pow(2, $i);
            $nextHeight = $smallestHeight * pow(2, $i);

            // resize previous image to next size
            $nextImage = imagecreatetruecolor($nextWidth, $nextHeight);
            imagecopyresized($nextImage, $prevImage, 0, 0, 0, 0,
                $nextWidth, $nextHeight, $prevWidth, $prevHeight);

            // apply blur filter
            imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);

            // now the new image becomes the previous image for the next step
            $prevImage = $nextImage;
            $prevWidth = $nextWidth;
            $prevHeight = $nextHeight;
        }

        // scale back to original size and blur one more time
        imagecopyresized($gdImageResource, $nextImage,0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
        imagefilter($gdImageResource, IMG_FILTER_GAUSSIAN_BLUR);
        // clean up
        imagedestroy($prevImage);
        // return result
        return $gdImageResource;
    }


    /**
     * 生成缩略图
     * @return mixed
     */
    public function imgurlall()
    {
     //   ini_set('memory_limit', '1500M');
        ignore_user_abort(true);//关掉浏览器，PHP脚本也可以继续执行.
        set_time_limit(0);// 通过set_time_limit(0)可以让程序无限制的执行下去
        $interval = 2*1;// 每隔半小时运行
        $i=0;

        echo '开始一键生成缩略图==============>'; echo '<br>';

        do {
          //  $cnt = Db::query("select vod_id from mac_vod where vod_pic_thumb = '' and vod_pic <> '' ");
            $cnt = model('Vod')->where('vod_pic_thumb','')->where('vod_pic','<>','')->count('vod_id');
            echo '需要执行操作的个数为'.$cnt.'<br>';
            //这里是你要执行的代码
           // $result = Db::query("select vod_id,vod_name,vod_pic_thumb,vod_director,vod_actor,vod_pic from mac_vod where vod_pic_thumb = '' and vod_pic <> '' limit 0,3 ");
            $result =model('Vod')->where('vod_pic_thumb','')->where('vod_pic','<>','')->limit(2)->select();
            $datas = [];
            // while ($row = $result->fetch()) {
            foreach($result as $row){
                $datas[$row['vod_id']] = [
                    'title' => $row['vod_name'],
                    'author' => $row['vod_actor'],
                    'actor' => $row['vod_director'],
                    'img' => $row['vod_pic'],
                ];

            }

            if (count($datas) > 0) {
                //post 获取 缩略图的 数据
                $nconfig='';
                if( get_addon_config('adminimgshort')){
                    $nconfig = get_addon_config('adminimgshort')['mode2'];
                }

                $keys = [];
                foreach ($datas as $key => $data) {

                    $author='';
                    if (isset($data['author']) && $data['author'] != '') {
                        $author = '导演: '.$data['author'];
                    }
                    $actor = '';
                    if (isset($data['actor']) && $data['actor'] != '') {
                        $actor = '主演: '.$data['actor'];
                    }
                    //图片上写文字
                    $word = [
                        'title' => isset($data['title']) ? $data['title'] : '影片名字未填写',
                        'author' => $author,
                        'actor' => $actor,
                    ];

                    $haibao = isset($data['img']) ? $data['img'] : 'imgmerge/haibao.jpg';  //海报图片
                    $sucai = 'imgmerge/sucai.png';    //素材图片
                    $haibaoIm = imagecreatefromstring(file_get_contents($haibao));
                    //获取海报原图的width height
                    $hbw = imagesx($haibaoIm);  //海报宽度
                    $hbh = imagesy($haibaoIm);  //海报高度

                    //创建完整的海报图片（压缩图片）
                    $hbok = imagecreatetruecolor(280, 398);
                    imagecopyresampled($hbok, $haibaoIm, 0,0, 0, 0, 280, 398, $hbw, $hbh);
                    //裁剪海报的图片中间部分
                    $hbcj = imagecreatetruecolor(280, 227);
                    imagecopy($hbcj, $hbok, 0,0, 0, 60, 280, 227);
                    //将裁减的图片进行高斯模糊处理
                    $hbcj = $this->blur($hbcj, 1);
                    //将裁减的中间部分进行拉伸并放到最终图片的底图上
                    $final = imagecreatetruecolor(750, 1100);
                    imagecopyresampled($final, $hbcj, 0, 0, 0, 0, 750, 610,280, 227);
                    //将素材加到底图上
                    $sucaiIm = imagecreatefrompng ($sucai);
                    imagesavealpha($sucaiIm, true);
                    imagecopy($final, $sucaiIm, 0, 0, 0, 0, 750, 1100);
                    //将完整海报加载到底图上
                    imagecopy($final, $hbok, 58, 190, 0, 0, 280, 398);
                    //将文字加到图片上
                    $colorIm = imagecreatetruecolor(256, 256);
                    $colorInt = imagecolorexact($colorIm, 33, 40, 53);  //获取颜色索引
                    $colorInt2 = imagecolorexact($colorIm, 134, 142, 155);  //获取颜色索引
                    imagefttext($final, 36, 0, 60, 692, $colorInt, 'imgmerge/PingFang-Medium.ttf', $word['title']);
                    imagefttext($final, 18, 0, 60, 752, $colorInt2, 'imgmerge/PingFang-Medium.ttf', $word['author']);
                    imagefttext($final, 18, 0, 60, 806, $colorInt2, 'imgmerge/PingFang-Medium.ttf', $word['actor']);

                    $hbum = imagecreatetruecolor(500, 733);  //图片大小压缩
                    imagecopyresampled($hbum, $final, 0,0, 0, 0, 500, 733, 750, 1100);

                    //图片画质压缩

                    //header('Content-type: image/png');
                    $fileName = time(). rand(1000, 9999). '.png';
                    imagepng($hbum, $fileName);



                    $pic =  ROOT_PATH.'/webpic/'.$fileName;
                    $response = Image::open($fileName)->save($pic);
                    $fileInfo = $fileName;

                    unlink($fileName);
                    imagedestroy($hbok);
                    imagedestroy($hbcj);
                    imagedestroy($final);
                    imagedestroy($colorIm);
                    imagedestroy($hbum);
                    imagedestroy($haibaoIm);
                    imagedestroy($sucaiIm);

                    if (!is_null($response)) {
                        $newres= '/webpic/'.$fileName;
                        $img = $nconfig . $newres; //movieimgs.oss-cn-beijing.aliyuncs.com/web\/15246445977163.png

                        //更新数据库
                        // $sql2 = "update mac_vod set vod_pic_thumb='" . $img . "' where vod_id=" . $key;
                        model('Vod')->where('vod_id',$key)->update(['vod_pic_thumb'=>$img]);
                        //echo $sql2;
                       // Db::execute($sql2);
                        //$rs = $stmt->execute();
                        $keys[] = [$key];

                    }

                }


                echo '更新完成'.$i.'批次';echo '<br>';
                //  echo '<script language=\'javascript\'>;alert(\'更新完成\');</script>';
                //$pdo = null;//关闭连接
                unset($response);
                unset($datas);

            }
            //ob_end_clean() ;//ob_flush();
            ob_flush();
            flush();
            sleep($interval);// 等待5分钟
            $i++;


        } while ($i<=$cnt);
        echo '更新完成';
        $cnt = model('Vod')->where('vod_pic_thumb','')->where('vod_pic','<>','')->count('vod_id');
        echo '剩余需要执行操作的个数为'.$cnt.'<br><br>当剩余个数不为0，可以尝试再次执行一键缩略图，<br>如果剩余个数仍然保持不变，请逐个检查下面视频编号的原始图片是否正常。<br>';
        $newids=model('Vod')->where('vod_pic_thumb','')->where('vod_pic','<>','')->column(['vod_id']);
        echo implode(',',$newids);



    }

}
