<?php namespace Zanozik\Cdnjs\Events;

use Illuminate\Queue\SerializesModels;
use Zanozik\Cdnjs\Asset;

class NewAssetVersion
{
    use SerializesModels;

    public $asset;

    /**
     * Create a new event instance.
     *
     * @param  Asset $asset
     */
    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

}