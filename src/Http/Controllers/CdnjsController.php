<?php namespace Zanozik\Cdnjs\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Zanozik\Cdnjs\Asset;

class CdnjsController extends Controller
{


    public function index()
    {
        return view('cdnjs::index', [
            'assets' => cache('assets')
        ]);
    }

    public function create()
    {
        return $this->edit(new Asset());
    }

    public function edit(Asset $asset)
    {
        return response()->json([
            'view' => \View::make('cdnjs::edit', compact('asset'))->render()
        ]);
    }

    public function store(Request $request)
    {
        $type = pathinfo($request->file, PATHINFO_EXTENSION);
        Asset::create(array_merge($request->all(), compact('type')));
        cache()->forget('assets');
        return redirect()->route('asset.index');
    }

    public function update(Request $request, Asset $asset)
    {
        $data = $request->all();
        if (isset($request->file)) $data['type'] = pathinfo($request->file, PATHINFO_EXTENSION);
        if (isset($request->current_version)) {
            $data['testing'] = 0;
            $data['new_version'] = null;
        }
        $asset->update($data);
        cache()->forget('assets');
        Artisan::call('view:clear');
        return redirect()->route('asset.index');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        cache()->forget('assets');
        return redirect()->route('asset.index');
    }

    public function test(Asset $asset)
    {
        $asset->testing = !$asset->testing;
        $asset->save();
        cache()->forget('assets');
        Artisan::call('view:clear');
        return redirect()->route('asset.index');
    }

}