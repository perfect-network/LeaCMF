<?php
/**
 * Created by PhpStorm.
 * User: lea
 * Date: 2017/12/31
 * Time: 15:21
 */

namespace app\admin\widget;

use app\common\library\Qiniu;
use think\facade\Config;
use think\facade\View;

class Upload
{

    public function editor($width = '100%', $height = "300px", $name = "content")
    {
        return View::fetch('widget/editor', [
            'width'  => $width,
            'height' => $height,
            'name'   => $name,
        ]);
    }

    public function qiniu($field = 'file', $type = 'image', $value = '')
    {
        $param['type']  = $type;
        $param['field'] = $field;

        $param['single'] = substr($field, -2) == '[]' ? '' : 1;

        $param['token'] = Qiniu::getToken($type);

        if (!empty($value)) {
            if (!is_array($value)) {
                $value = array_filter(array_unique(explode(',', $value)));
            }
            $temp = [];
            foreach ($value as $val) {
                $temp[$val] = Qiniu::getDownloadUrl($val, $type);
            }
            $param['value'] = $temp;
        }
        $param['time'] = uniqid();
        return View::fetch('widget/upload', $param);
    }
}