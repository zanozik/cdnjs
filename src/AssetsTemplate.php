<?php namespace Zanozik\Cdnjs;

class AssetsTemplate
{
    /**
     * Converting blade directive into html tags
     *
     * @param string $names Asset names separated by pipe `|`
     * @param bool $url If name should be converted to URL, instead of a html tag
     * @return string Html tags
     */
    public function convert($names, $url = false)
    {

        $print = '';

        $names = preg_replace('/\s+/', '', $names); // cleaning the string from all whitespaces
        $names = array_filter(explode('|', $names)); // "converting" to array with non-empty elements

        foreach ($names as $name) {
            $asset = cache('assets')->where('name', $name)->first();

            if ($asset) {
                $asset_url = config('cdnjs.url.ajax') . $asset->library . '/' . ($asset->testing ? $asset->new_version : $asset->current_version) . '/' . $asset->file;

                if ($url) {
                    $print .= $asset_url;
                    break;
                }

                switch ($asset->type) {
                    case 'js':
                        $print .= '<script src="' . $asset_url . '"></script>' . PHP_EOL;
                        break;
                    case 'css':
                        $print .= '<link rel="stylesheet" href="' . $asset_url . '" />' . PHP_EOL;
                        break;
                }

            } else {
                $print .= '<script>console.error("CDNJS: Your named library `' . $name . '` was not found in your database!")</script>' . PHP_EOL;
            }
        }

        return $print;

    }

}