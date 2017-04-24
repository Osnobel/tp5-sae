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

class KVDB
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
        $this->handler = new \SaeKV();
        if (!$this->handler->init()) {
            throw new \BadFunctionCallException('KVDB init error');
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
        $content = sprintf('%010d', $_SERVER['REQUEST_TIME']) . $content;
        // 生成模板缓存文件
        if (false === $this->handler->set($prefix . $cacheFile, $content)) {
            throw new Exception('cache write error:' . $cacheFile);
        } else {
            $this->contents[$cacheFile] = $content;
            return true;
        }
    }

    /**
     * 读取编译编译
     * @param string  $cacheFile 缓存的文件名
     * @param array   $vars 变量数组
     * @return void
     */
    public function read($cacheFile, $vars = [])
    {
        if (!empty($vars) && is_array($vars)) {
            // 模板阵列变量分解成为独立变量
            extract($vars, EXTR_OVERWRITE);
        }
        //载入模版缓存文件
        eval('?>' . $this->get($cacheFile, 'content'));
    }

    /**
     * 检查编译缓存是否有效
     * @param string  $cacheFile 缓存的文件名
     * @param int     $cacheTime 缓存时间
     * @return boolean
     */
    public function check($cacheFile, $cacheTime)
    {
        // 缓存文件不存在, 直接返回false
        $mtime = $this->get($cacheFile, 'mtime');
        if (false === $mtime) {
            return false;
        }
        if (0 != $cacheTime && $_SERVER['REQUEST_TIME'] > $mtime + $cacheTime) {
            // 缓存是否在有效期
            return false;
        }
        return true;
    }

    /**
     * 读取缓存文件信息
     * @access private
     * @param string $filename  文件名
     * @param string $name  信息名 mtime或者content
     * @return boolean|string
     */
    private function get($filename, $name)
    {
        if (!isset($this->contents[$filename])) {
            $this->contents[$filename] = $this->handler->get($prefix . $filename);
        }
        $content = $this->contents[$filename];

        if (false === $content) {
            return false;
        }
        $info = array(
            'mtime'   => substr($content, 0, 10),
            'content' => substr($content, 10),
        );
        return $info[$name];
    }
}