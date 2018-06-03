<?php

namespace addons\adminimgshort\controller;

use think\addons\Controller;
use think\Addons;
use addons\adminimgshort\Adminimgshort;

class Index extends Controller
{

    public function imgconfig(){
        $nn = new Adminimgshort;
        $config = $nn->getConfig();
        echo $config['value'];
        return $config;

    }

}

