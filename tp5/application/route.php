<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    'api/getApps/[:offset]/[:length]'   => ['api/MyApps/index', ['method' => 'get'], ['offset' => '\d+', 'length' => '\d+']],
    // '[checkVersion]'    =>  [
    //     ':version/:trackId/[:country]'  =>  ['api/CheckVersion/index', ['method' => 'get'], ['trackId' => '\d+']],
    //     ':version/:bundleId/:artistId/[:country]'  =>  ['api/CheckVersion/index', ['method' => 'get'], ['artistId' => '\d+']],
    // ],// 此写法不能自动识别大小写checkversion
    'api/checkVersion/:version/:trackId/[:country]'  =>  ['api/CheckVersion/index', ['method' => 'get'], ['trackId' => '\d+']],
    'api/checkVersion/:version/:bundleId/:artistId/[:country]'  =>  ['api/CheckVersion/index', ['method' => 'get'], ['artistId' => '\d+']],
    'about' => 'index/About/index',
];
