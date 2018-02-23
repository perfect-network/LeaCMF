<?php

namespace app\admin\controller;

use app\common\util\Qiniu;
use Hashids\Hashids;
use lea\Y;
use think\Controller;
use think\Db;
use think\Image;
use think\Request;

class FileController extends Controller
{

    public function upload(Request $request)
    {
        $type = $request->param('type', 'image');
        $file = $request->file('file');
        if (empty($file)) {
            return json(['status' => 5, 'msg' => '文件不存在']);
        }
        //获取上传配置
        $config = config('upload.');
        $path   = $config['upload_path'] . '/' . $type;
        if (!isset($config['upload_size_limit'][$type])) {
            return json(['code' => 2, 'msg' => '上传文件格式不允许']);
        }
        $info = $file->validate(['size' => $config['upload_size_limit'][$type], 'ext' => $config['upload_type_limit'][$type]])->move($path);
        if ($info) {
            $data          = [
                'type'     => $type,
                'ext'      => strtolower($info->getExtension()),
                'path'     => $info->getSaveName(),
                'filename' => $info->getFilename(),
                'size'     => $info->getSize(),
                'sha1'     => $info->hash('sha1'),
                'mime'     => $info->getMime(),
                'at_time'  => time()
            ];
            $id            = Db::name('uploads')->insertGetId($data);
            $result['src'] = '/uploads/' . $type . '/' . $info->getSaveName();
            $result['id']  = $id;
            return json(['code' => 0, 'msg' => '上传成功', 'data' => $result]);
        } else {
            return json(['code' => -10, 'msg' => $file->getError()]);
        }
    }

    public function um(Request $request)
    {
        $file     = $request->file('file');
        if (empty($file)) {
            return response(json_encode(['code' => 1, 'state' => '文件不存在']));
        }
        $type = 'um-editor';
        //获取上传配置
        $config = config('upload.');
        $path   = $config['upload_path'] . '/' . $type;
        if (!isset($config['upload_size_limit'][$type])) {
            return response(json_encode(['code' => 2, 'state' => '上传文件格式不允许']));
        }
        $info = $file->validate(['size' => $config['upload_size_limit'][$type], 'ext' => $config['upload_type_limit'][$type]])->move($path);
        if ($info) {
            return response(json_encode(['code' => 0, 'state' => 'SUCCESS', 'url' => '/uploads/' . $type . '/' . $info->getSaveName()]));
        } else {
            return response(json_encode(['code' => -10, 'state' => $file->getError()]));
        }
    }
}