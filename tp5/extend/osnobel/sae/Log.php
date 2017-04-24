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

namespace osnobel\sae;

/**
 * 调试输出到SAE
 */
class Log
{
	protected $config = [
		'time_format' => ' c ',
	];
	
	// 	实例化并传入参数
	public function __construct(array $config = [])
	{
		if (!function_exists('sae_debug')) {
			throw new \BadFunctionCallException('must run at sae');
		}
		if (is_array($config)) {
			$this->config = array_merge($this->config, $config);
		}
	}
	
	
	/**
	 * 日志写入接口
	 * @access public
	 * @param array $log 日志信息
	 * @return bool
	 */
	public function save(array $log = [])
	{
		static $is_debug = null;
		// 获取基本信息
		if (isset($_SERVER['HTTP_HOST'])) {
			$current_uri = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		else {
			$current_uri = "cmd:" . implode(' ', $_SERVER['argv']);
		}
		
		$runtime    = round(microtime(true) - THINK_START_TIME, 10);
		$reqs       = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
		$time_str   = ' [运行时间：' . number_format($runtime, 6) . 's][吞吐率：' . $reqs . 'req/s]';
		$memory_use = number_format((memory_get_usage() - THINK_START_MEM) / 1024, 2);
		$memory_str = ' [内存消耗：' . $memory_use . 'kb]';
		$file_load  = ' [文件加载：' . count(get_included_files()) . ']';
		
		$info = '[ info ] ' . $current_uri . $time_str . $memory_str . $file_load . "\r\n";
		foreach ($log as $type => $val) {
			foreach ($val as $msg) {
				if (!is_string($msg)) {
					$msg = var_export($msg, true);
				}
				$info .= '[ ' . $type . ' ] ' . $msg . "\r\n";
			}
		}
		$now     = date($this->config['time_format']);
		$server  = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '0.0.0.0';
		$remote  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
		$uri     = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		
		$logstr = "[{$now}] {$server} {$remote} {$uri}\r\n" . $info;
		if (is_null($is_debug)) {
			$appSettings = [];
			preg_replace_callback('@(\w+)\=([^;]*)@', function ($match) use (&$appSettings) {
				$appSettings[$match['1']] = $match['2'];
			}, $_SERVER['HTTP_APPCOOKIE']);
			$is_debug = in_array($_SERVER['HTTP_APPVERSION'], explode(',', $appSettings['debug'])) ? true : false;
		}
		if ($is_debug) {
			ini_set('display_errors',0);// 日志记录不输出到屏幕。
		}
		sae_debug($logstr);
		if ($is_debug) {
			ini_set('display_errors',1);// 日志记录输出到屏幕。
		}
		return true;
	}
}
