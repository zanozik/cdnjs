<?php

use Zanozik\Cdnjs\AssetsTemplate;

if (!function_exists('cdnjs')) {
    /**
     * Custom helper for printing automatically chosen html asset tags.
     *
     * @param  array|string $names Numeric array of asset names or string if only URL is needed
     *
     * @return string
     */
    function cdnjs($names = [])
    {
        return (new AssetsTemplate())->output($names);
    }
}
