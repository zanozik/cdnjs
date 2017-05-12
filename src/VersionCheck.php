<?php namespace Zanozik\Cdnjs;

use Illuminate\Support\Facades\Artisan;

class VersionCheck{

	private function convertMask($version, $mask_type){

		$parts = explode('.', $version);

		$regex = '/^';
		for($i = 1; $i < $mask_type; $i++){
			$regex .= $parts[$i-1] . '\.';
		}
		$regex .= '.*/';

		return $regex;

	}

	public function check(){

		$flush_cache = false;

		foreach(cache('assets')->where('version_mask_check', '>', 0) as $asset){
			$vmc = $asset->version_mask_check;
			$vma = $asset->version_mask_autoupdate;
			$ver = $asset->current_version;

			$update_data = [];

			$client = new \GuzzleHttp\Client();

			$library = json_decode($client->request('GET', config('cdnjs.url.api') . $asset->library)->getBody());

			if($asset->latest_version !== $library->version) $update_data['latest_version'] = $library->version;

			$regex = $this->convertMask($ver, $vmc);

			$update_key = 'new_version';
			
			foreach($library->assets as $version){
				echo 'Checking ' . $asset->name . ' (' . $ver . ') to version ' . $version->version . ' with regex ' . $regex . PHP_EOL;
				if(preg_match($regex, $version->version)){
					if($ver !== $version->version){
						if($update_key == 'current_version'){
							echo 'Updating ' . $asset->name . ' (' . $ver . ') --> ' . $version->version . PHP_EOL;
							$update_data[$update_key] = $version->version;
						}
						if($update_key == 'new_version'){
							if($asset->new_version = !$version->version){
								echo 'New version found for ' . $asset->name . ' (' . $ver . ') --> ' . $version->version . PHP_EOL;
								$update_data[$update_key] = $version->version;
							}
							$update_key = 'current_version';
							if($vmc === $vma){
								echo 'Updating ' . $asset->name . ' (' . $ver . ') --> ' . $version->version . PHP_EOL;
								$update_data[$update_key] = $version->version;
								break;
							}
							else{
								$regex = $this->convertMask($ver, $vma);
							}
						}else break;
					}else break;
				}
			}

			if($update_data){
				var_dump($update_data);
				if(isset($update_data['current_version'], $update_data['new_version']) && $update_data['new_version'] == $update_data['current_version']) $update_data['new_version'] = '';
				Asset::find($asset->id)->update($update_data);
				$flush_cache = true;
			}
		}
		if($flush_cache) {
			cache()->forget('assets');
			Artisan::call('view:clear');
		}

	}

}