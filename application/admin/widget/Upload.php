<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/12/31
 * Time: 15:21
 */

namespace app\admin\widget;

use think\facade\View;

class Upload
{

    public function editor($width = '100%', $height = "300px", $name = "content")
    {
        //获取上传链接

        return View::fetch('widget/editor', [
            'width'  => $width,
            'height' => $height,
            'name'   => $name,
        ]);
    }
}