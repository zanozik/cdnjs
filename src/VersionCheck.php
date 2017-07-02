<?php namespace Zanozik\Cdnjs;

use Illuminate\Support\Facades\Artisan;

class VersionCheck
{
    /**
     * Array to run Asset update with
     *
     * @var array
     */
    private static $data;

    /**
     * Checking for a new version switch
     *
     * @var bool
     */
    private static $check;

    /**
     * Mask regex patern
     *
     * @var string
     */
    private static $regex;

    /**
     * Current checked Asset
     *
     * @var Asset
     */
    private static $asset;

    /**
     * Checks for new asset versions and updates if needed
     *
     * @return void
     *
     */
    public static function check()
    {
        $flush_cache = false;

        foreach (cache('assets')->where('version_mask_check', '>', 0) as $asset) {
            self::$data = [];
            self::$check = true;
            self::$asset = $asset;
            self::convertMask();

            $client = new \GuzzleHttp\Client();
            $library = json_decode($client->request('GET', config('cdnjs.url.api') . $asset->library)->getBody());

            if ($asset->latest_version !== $library->version) {
                self::$data['latest_version'] = $library->version;
            }
            foreach ($library->assets as $cdnjs_asset) {

                //echo 'Checking ' . $asset->name . ' (' . $asset->current_version . ') to version ' . $cdnjs_asset->version . ' with regex ' . self::$regex . PHP_EOL;

                if (self::compare($cdnjs_asset->version)) {
                    break;
                }
            }

            if (self::$data) {

                if (isset(self::$data['current_version'], self::$data['new_version']) && self::$data['new_version'] == self::$data['current_version']) {
                    self::$data['new_version'] = '';
                }

                $asset->update(self::$data);

                $flush_cache = true;

                if (isset(self::$data['new_version'])) {
                    event(new Events\NewAssetVersion($asset));
                }
                if (isset(self::$data['current_version'])) {
                    event(new Events\AssetVersionUpdated($asset));
                }
            }
        }

        if ($flush_cache) {
            cache()->forget('assets');
            Artisan::call('view:clear');
        }
    }

    /**
     * Create a mask from a version and a mask type
     *
     * @return void
     */
    private static function convertMask()
    {

        $parts = explode('.', self::$asset->current_version);

        $str = '/^';
        $type = self::$check ? self::$asset->version_mask_check : self::$asset->version_mask_autoupdate;

        for ($i = 1; $i < $type; $i++) {
            $str .= $parts[$i - 1] . '\.';
        }
        $str .= '.*/';

        self::$regex = $str;
    }

    /**
     * Comparing version to a current version according to defined mask
     *
     * @param string $version
     *
     * @return bool Breaking from foreach
     */
    private static function compare($version)
    {
        if (in_array($version, [self::$asset->new_version, self::$asset->current_version])) {
            return true;
        }

        if (preg_match(self::$regex, $version)) {
            //echo (self::$check ? 'New version' : 'Autoupdate') . ' found for ' . self::$asset->name . ' (' . self::$asset->current_version . ') --> ' . $version . PHP_EOL;
            self::$data[self::$check ? 'new_version' : 'current_version'] = $version;
            if (self::$check && self::$asset->version_mask_autoupdate > 0) {
                self::$check = false;
                self::convertMask();

                return self::compare($version);
            } else {
                return true;
            }
        } else {
            return false;
        }

    }

}
