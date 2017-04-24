<?php

namespace app\api\event;

use think\Cache;
use think\Config;

class LookupAppStore
{
    /**
     * 显示开发者ID的应用列表
     *
     * @param  string   $id
     * @param  string   $country
     * @return array    $apps
     */
    public function appsByArtistId($id, $country) 
    {
        $apps = Cache::get($id.$country);// 读取缓存, 有则返回，无则拉取
        if ($apps) {
            return $apps;
        }
        $result = $this->appsLookup($id, $country);
        $apps = array();
        foreach($result["results"] as $wrapper) {
            if ($wrapper["wrapperType"] == "software") {
                $apps[] = $wrapper;
                Cache::set($wrapper["trackId"].$country, $wrapper, 3600);// 设置缓存 for appByTrackId
                Cache::set($wrapper["bundleId"].$wrapper["artistId"].$country, $wrapper, 3600);// 设置缓存 for appByBundleId
            }
        }
        Cache::set($id.$country, $apps, 3600);// 设置缓存
        return $apps;
    }

    /**
     * 显示Track ID的应用
     *
     * @param  string   $id
     * @param  string   $country
     * @return array    $app
     */
    public function appByTrackId($id, $country) 
    {
        $app = Cache::get($id.$country);// 读取缓存, 有则返回，无则拉取
        if ($app) {
            return $app;
        }
        $result = $this->appsLookup($id, $country);
        $app = NULL;
        foreach($result["results"] as $wrapper) {
            if ($wrapper["wrapperType"] == "software") {
                $app = $wrapper;
                Cache::set($wrapper["bundleId"].$wrapper["artistId"].$country, $wrapper, 3600);// 设置缓存 for appByBundleId
            }
        }
        Cache::set($id.$country, $app, 3600);// 设置缓存
        return $app;
    }

    /**
     * 显示Bundle ID的应用
     *
     * @param  string   $id
     * @param  string   $artistId
     * @param  string   $country
     * @return array    $app
     */
    public function appByBundleId($id, $artistId, $country) 
    {
        $app = Cache::get($id.$artistId.$country);// 读取缓存, 有则返回，无则拉取
        if ($app) {
            return $app;
        }
        $result = $this->appsByArtistId($artistId, $country);
        $app = Cache::get($id.$artistId.$country);// 重新读取缓存
        if ($app) {
            return $app;
        }
        return NULL;
    }

    /**
     * 查找所有对应ID的应用列表
     *
     * @param  string   $ids
     * @param  string   $country
     * @return array    $apps
     */
    public function appsLookup($ids, $country) {
        if (empty($country)) {
            $parameterkeyvalue = "id=".$ids."&entity=software";
        } else {
            $parameterkeyvalue = "id=".$ids."&entity=software&country=".$country;
        }
        $url = "https://itunes.apple.com/lookup?".$parameterkeyvalue;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $apps = array();
        if (!empty($result)) {
            $apps = json_decode($result, true);
        }
        return $apps;
    }
}
