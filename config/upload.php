<?php

return [
    //上传大小限制，单位字节
    'upload_size_limit' => [
        'face'      => 524288,
        'image'     => 1048576,
        'attach'    => 524288000,
        'document'  => 5242880,
        'video'     => 209715200,
        'audio'     => 5242880,
        'um-editor' => 1048576,
    ],
    //上传允许的文件格式
    'upload_type_limit' => [
        'face'      => 'jpg,png,jpeg',
        'image'     => 'jpg,png,gif,jpeg',
        'attach'    => 'zip,rar,tar.gz,apk',
        'document'  => 'xls,xlsx,doc,docx,ppt,pptx',
        'video'     => 'mp4,',
        'audio'     => 'mp3',
        'um-editor' => 'jpg,png,gif,jpe,bmp',
    ],

    //上传的位置,也可以是/home/file定义
    'upload_path'       => env('root_path') . 'public/uploads',

    /**
     * 七牛云,目前七牛云，也可以自己搭建文件服务器
     */
    'AccessKey'         => 'EfIJshCeav_qWcOqoTYaJJs-77P2tXZR-CcWtBKT',
    'SecretKey'         => 'M19-zOE99o8Z--njaScpog1uxit8ZIYNSXgec0d9',

    'UploadUrl' => 'http://up-z1.qiniu.com',

    'bucket' => [
        'image' => [
            'host'    => 'http://ozjv2l07o.bkt.clouddn.com',
            'private' => false
        ],
        'video' => [
            'host'    => 'http://p2ldt5md7.bkt.clouddn.com',
            'private' => false
        ],
        'file'  => [
            'host'    => 'http://p2ldkhm4k.bkt.clouddn.com',
            'private' => false
        ],
    ]

];