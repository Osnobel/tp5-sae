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

namespace osnobel\sae\cache;

class Memcached extends \think\cache\driver\Memcached
{
    /**
     * 构造函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = [])
    {
        if (!function_exists('sae_debug')) {
            throw new \BadFunctionCallException('must run at sae');
        }
        $this->handler = new \Memcached();
        if (!$this->handler) {
            throw new \BadFunctionCallException('memcache init error');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->options['prefix'] = 'cache/' . $_SERVER['HTTP_APPVERSION'] . '/' . $this->options['prefix'];
    }
}