<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2017 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: GoshDo <http://goshdo.sinaapp.com>
// +----------------------------------------------------------------------

namespace osnobel\sae\template;

use think\Exception;

class Memcached extends \osnobel\sae\template\KVDB
{
    // handler 对象
    private $handler;
    // 编译缓存内容
    private $contents = [];
    // 缓存前缀
    private $prefix = '';

    /**
     * 构造函数
     * @access public
     */
    public function __construct()
    {
        if (!function_exists('sae_debug')) {
            throw new \BadFunctionCallException('must run at sae');
        }
        $this->handler = new \Memcached();
        if (!$this->handler) {
            throw new \BadFunctionCallException('memcache init error');
        }
        $prefix = 'template/' . $_SERVER['HTTP_APPVERSION'] . '/';
    }

    /**
     * 写入编译缓存
     * @param string $cacheFile 缓存的文件名
     * @param string $content 缓存的内容
     * @return void|array
     */
    public function write($cacheFile, $content)
    {
        // 添加写入时间
        $content = sprintf('%010d', $$_SERVER['REQUEST_TIME']) . $content;
        // 生成模板缓存文件
        if (false === $this->handler->set($prefix . $cacheFile, $content, 0)) {
            throw new Exception('cache write error:' . $cacheFile);
        } else {
            $this->contents[$cacheFile] = $content;
            return true;
        }
    }
}