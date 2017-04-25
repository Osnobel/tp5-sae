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
        $this->prefix = 'template/' . $_SERVER['HTTP_APPVERSION'] . '/';
    }

    /**
     * 写入缓存文件信息
     * @access protected
     * @param string $filename  文件名
     * @param string $content  文件内容
     * @return boolean|string
     */
    protected function set($filename, $content)
    {
        return $this->handler->set($this->prefix . $filename, $content, 0);
    }
}