<?php

namespace app\api\controller;

use think\Cache;
use think\Config;

class MyApps
{
    /**
     * 显示指定数量的我的应用(默认显示所有应用)
     *
     * @param  int  $offset
     * @param  int  $length
     * @return json $apps
     */
    public function index($offset = 0, $length = NULL) {
        // 获取配置文件中的ID列表
        $artistIds = Config::get('my_app_ids.artist_ids');
        $trackIds = Config::get('my_app_ids.track_ids');
        $country = "cn";
        $ids = join(",", array_merge($artistIds, $trackIds));
        
        $apps = Cache::get($ids.$country);// 读取缓存
        if ($apps) {
             return array_slice($apps, $offset, $length);
        }
        $event = controller('api/LookupAppStore', 'event');
        $result = $event->appsLookup($ids, $country);
        $apps = array();
        foreach($result["results"] as $wrapper) {
            if ($wrapper["wrapperType"] == "software") {
                $apps[] = $wrapper;
                Cache::set($wrapper["trackId"].$country, $wrapper, 3600);// 设置缓存 for appByTrackId
                Cache::set($wrapper["bundleId"].$wrapper["artistId"].$country, $wrapper, 3600);// 设置缓存 for appByBundleId
            }
        }
        usort($apps, array($this, "appsSort"));
        Cache::set($ids.$country, $apps, 3600);// 设置缓存
        return array_slice($apps, $offset, $length);
    }

    /**
     * 根据发布日期排列应用列表
     *
     * @param  object  $a
     * @param  object  $b
     * @return 0 | 1 | -1
     */
    private function appsSort($a, $b) {
        if ($a['releaseDate'] == $b['releaseDate']) {
            return 0;
        }
        return ($a['releaseDate'] < $b['releaseDate']) ? 1 : -1;
    }
}
