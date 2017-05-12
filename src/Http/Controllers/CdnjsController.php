<?php namespace Zanozik\Cdnjs\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Zanozik\Cdnjs\Asset;
use Illuminate\Support\Facades\Artisan;

class CdnjsController extends Controller {

	private $masks = ['Off', 'Any version', 'Include subversion', 'Minor version only'];

	public function index() {
		return view('cdnjs::index', [
			'assets' => cache('assets'),
			'masks' => $this->masks
		]);
	}

	public function create() {
		return $this->edit(new Asset());
	}

	public function store(Request $request) {
		$type = pathinfo($request->file, PATHINFO_EXTENSION);
		Asset::create(array_merge($request->all(), compact('type')));
		cache()->forget('assets');
		return redirect()->route('asset.index');
	}

	public function edit(Asset $asset) {
		return response()->json([
			'view' => \View::make('cdnjs::edit', [
				'asset' => $asset,
				'masks' => $this->masks
			])->render()
		]);
	}

	public function update(Request $request, Asset $asset) {
		$type = pathinfo($request->file, PATHINFO_EXTENSION);
		$asset->update(array_merge($request->all(), compact('type')));
		cache()->forget('assets');
		Artisan::call('view:clear');
		return redirect()->route('asset.index');
	}

	public function destroy(Asset $asset) {
		$asset->delete();
		cache()->forget('assets');
		return redirect()->route('asset.index');
	}

	public function test(Asset $asset){
		$asset->testing = !$asset->testing;
		$asset->save();
		cache()->forget('assets');
		Artisan::call('view:clear');
		return redirect()->route('asset.index');
	}

}