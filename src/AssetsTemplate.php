<?php namespace Zanozik\Cdnjs;

class AssetsTemplate{
	
	public function convert($names, $url = false){

		$print = '';

		$names = preg_replace('/\s+/', '', $names); // cleaning the string from all whitespaces
		$names = array_filter(explode('|', $names)); // "converting" to array with non-empty elements

		foreach($names as $name){
			$asset = cache('assets')->where('name', $name)->first();

			if($asset){
				$asset_url = config('cdnjs.url.ajax') . $asset->library . '/' . ($asset->testing ? $asset->new_version : $asset->current_version) . '/' . $asset->file;

				if($url){
					$print .= $asset_url;
					break;
				}

				switch($asset->type){
					case 'js':
						$print .= '<script src="' . $asset_url . '"></script>';
						break;
					case 'css':
						$print .= '<link rel="stylesheet" href="' . $asset_url . '" />';
						break;
				}

			}else{
				$print .= '<script>console.error("CDNJS: Your named library `' . $name . '` was not found in your database!")</script>';
			}
		}
		return $print;

	}

}