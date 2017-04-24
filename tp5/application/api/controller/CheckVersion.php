<?php
namespace app\api\controller;

class CheckVersion
{
    /**
     * 检查应用是否有新的版本
     *
     * @return json     $data
     */
    public function index()
    {
        $version = input('version');
        $artistId = input('artistId');
        $bundleId = input('bundleId');
        $trackId = input('trackId');
        $country = input('country');

        $data['code'] = 0;
        $data['description'] = "no new version";
        $event = controller('LookupAppStore', 'event');
        $app = NULL;

        if (!empty($version) && !empty($artistId) && !empty($bundleId)) {
            $app = $event->appByBundleId($bundleId, $artistId, $country);
        } elseif (!empty($version) && !empty($trackId)) {
            $app = $event->appByTrackId($trackId, $country);
        }
        if (!empty($app)) {
            $lastVersion = $app['version'];
            if (version_compare($version, $lastVersion, '<')) {
                $data['code'] = 1;
                $data['description'] = "have a new version";
                $data['version'] = $lastVersion;
                $data['releaseDate'] = $app['releaseDate'];
                $data['releaseNotes'] = $app['releaseNotes'];
                $data['url'] = $app['trackViewUrl'];
                // 更新描述中以###开头的版本则为必须升级的版本
                if (substr($app['releaseNotes'], 0, 3) == '###') {
                    $data['code'] = 2;
                    $data['description'] = "must update the new version";
                }
            }
        }
        return json($data);
    }
}
