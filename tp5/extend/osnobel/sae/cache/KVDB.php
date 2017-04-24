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

use think\cache\Driver;

class KVDB extends Driver
{
    protected $options = [
        'expire'   => 0,
        'prefix'   => '',
        'option'   => [],
    ];

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
        $this->handler = new \SaeKV();
        if (!$this->handler->init()) {
            throw new \BadFunctionCallException('KVDB init error');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->options['prefix'] = 'cache/' . $_SERVER['HTTP_APPVERSION'] . '/' . $this->options['prefix'];
        if (!empty($this->options['option'])) {
            $this->handler->setOptions($this->options['option']);
        }
    }

    /**
     * 判断缓存
     * @access public
     * @param string $name 缓存变量名
     * @return bool
     */
    public function has($name)
    {
        return $this->get($name) ? true : false;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $result = $this->handler->get($this->getCacheKey($name));
        if (false !== $result) {
            $expire = (int) substr($result, 0, 10);
            if (0 != $expire && $_SERVER['REQUEST_TIME'] > $expire) {
                //缓存过期删除缓存记录
                $this->rm($name);
                return $default;
            }
            return substr($result, 10);
        } else {
            return $default;
        }
    }

    /**
     * 写入缓存
     * @access public
     * @param string    $name 缓存变量名
     * @param mixed     $value  存储数据
     * @param integer   $expire  有效时间（秒）
     * @return bool
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($this->tag && !$this->has($name)) {
            $first = true;
        }
        $key    = $this->getCacheKey($name);
        $expire = 0 == $expire ? 0 : $_SERVER['REQUEST_TIME'] + $expire;
        if ($this->handler->set($key, sprintf('%010d', $expire) . $value)) {
            isset($first) && $this->setTagItem($key);
            return true;
        }
        return false;
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        if ($this->has($name)) {
            $value = $this->get($name) + $step;
        } else {
            $value = $step;
        }
        return $this->set($name, $value, 0) ? $value : false;
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string    $name 缓存变量名
     * @param int       $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        if ($this->has($name)) {
            $value = $this->get($name) - $step;
        } else {
            $value = -$step;
        }
        return $this->set($name, $value, 0) ? $value : false;
    }

    /**
     * 删除缓存
     * @param    string  $name 缓存变量名
     * @param bool|false $ttl
     * @return bool
     */
    public function rm($name)
    {
        $key = $this->getCacheKey($name);
        return $this->handler->delete($key);
    }

    /**
     * 清除缓存
     * @access public
     * @param string $tag 标签名
     * @return bool
     */
    public function clear($tag = null)
    {
        if ($tag) {
            // 指定标签清除
            $keys = $this->getTagItem($tag);
            foreach ($keys as $key) {
                $this->handler->delete($key);
            }
            $this->rm('tag_' . md5($tag));
            return true;
        }
        $key = $this->getCacheKey('');
        $keys = [];
        $ret = $this->handler->pkrget($key, 100);
        while (true) {
            foreach ($ret as $k => $v) {
                $keys[] = $k;
            }
            end($ret);
            $start_key = key($ret);
            $i = count($ret);
            if ($i < 100) break;
            $ret = $this->handler->pkrget($key, 100, $start_key);
        }
        foreach($keys as $k) {
            $this->handler->delete($k);
        }
        return true;
    }
}