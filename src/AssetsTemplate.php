<?php namespace Zanozik\Cdnjs;

class AssetsTemplate
{
    /**
     * Converting blade directive into html tags
     *
     * @param string $string Asset names separated by pipe `|`
     *
     * @return string Html tags
     */
    public function explodeAndOutput($string)
    {
        $string = preg_replace('/\s+/', '', $string); // cleaning the string from all whitespaces
        $names = array_filter(explode('|', $string)); // converting to array with non-empty elements

        return $this->output($names);
    }

    /**
     * Output asset names as html tags
     *
     * @param array|string $names Asset names array or string if only URL is needed
     *
     * @return string Html tags
     */
    public function output($names)
    {
        if (!$names) return false;

        $output = '';

        if (is_array($names)) {

            foreach ($names as $name) {
                $output .= $this->getOutputString($name);
            }

        } else {
            $output = $this->getOutputString($names, true);
        }

        return $output;
    }

    private function getOutputString($name, $url = false)
    {
        $asset = cache('assets')->where('name', $name)->first();

        if ($asset) {
            $asset_url = config('cdnjs.url.ajax') . $asset->library . '/' . ($asset->testing ? $asset->new_version : $asset->current_version) . '/' . $asset->file;
        } else {
            return $url ? '' : '<script>console.error("CDNJS: Your named library `' . $name . '` was not found in your database!")</script>' . PHP_EOL;
        }

        return $url ? $asset_url : $this->wrapHtmlTags($asset_url, $asset->type);
    }

    private function wrapHtmlTags($asset_url, $type)
    {
        switch ($type) {
            case 'js':
                return '<script src="' . $asset_url . '"></script>' . PHP_EOL;
            case 'css':
                return '<link rel="stylesheet" href="' . $asset_url . '" />' . PHP_EOL;
        }

        return '';
    }

}