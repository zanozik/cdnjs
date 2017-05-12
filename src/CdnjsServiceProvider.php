<?php namespace Zanozik\Cdnjs;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class CdnjsServiceProvider extends ServiceProvider{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Router $router){
	    $viewPath = __DIR__.'/resources/views';

	    $this->loadViewsFrom($viewPath, 'cdnjs');
	    $this->loadMigrationsFrom(__DIR__.'/database/migrations');

	    $this->publishes([
		    __DIR__ . '/config/cdnjs.php' => config_path('cdnjs.php'),
	    ]);

	    $this->publishes([
		    $viewPath => base_path('resources/views/vendor/cdnjs'),
	    ], 'views');

	    /* Configuring router for the package */
	    $group = array_merge(config('cdnjs.route'), ['namespace' => 'Zanozik\Cdnjs']);
	    $router->group($group, function($router){
		    $router->resource('assets', 'Http\Controllers\CdnjsController',
			    ['names' => [
			    	'index' => 'asset.index',
			    	'create' => 'asset.create',
			    	'edit' => 'asset.edit',
			    	'update' => 'asset.update',
			    	'destroy' => 'asset.delete'
			    ]]
	        );
		    $router->get('assets/{asset}/test', 'Http\Controllers\CdnjsController@test')->name('asset.test');
	    });

	    /* On-demand persistent asset caching. We will flush it when we need to! */
	    Cache::rememberForever('assets', function () {
		    return Asset::orderBy('name')->get();
	    });

	    /**
	     * Custom blade directive for printing automatically chosen html asset tags.
		 * Expects one or more asset names, separated by pipe `|`.
		 * Will be loaded in the same order you provide.
	     *
	     * @return string
	     */
	    Blade::directive('cdnjs', function ($names) {
		    return (new AssetsTemplate)->convert($names);
	    });
	    /**
	     * Another custom blade directive for printing a single URL of the asset. Will take single (or first) name.
	     *
	     * @return string
	     */
	    Blade::directive('cdnjs-url', function ($name) {
		    return (new AssetsTemplate)->convert($name, true);

	    });

	    /**
	     * Daily scheduler to perform version check and autoupdate.
	     *
	     */
	    $this->app->booted(function () {

		    $schedule = $this->app->make(Schedule::class);

		    $schedule->call(function () {
			    (new VersionCheck())->check();
		    })->everyMinute();
	    });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(){
	    $this->mergeConfigFrom(__DIR__ . '/config/cdnjs.php', 'cdnjs');
    }
}
