# CDNjs Asset Manager
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Packagist Downloads][downloads-packagist]][link-packagist]
[![Github Downloads][downloads-github]][link-github]
[![Software License][ico-license]](LICENSE)

CDNjs Asset Manager helps you install, update, manage and test CDNjs assets in your Laravel app. It uses custom helper `cdnjs()` and Blade directive (depreciated!) to include appropriate assets in your template by an alias you define. All assets are stored in database and cached on the first request indefinitely.

Front-end of the manager lets you add, edit, update and test assets, fetching them directly from CDNjs. You can also set up a scheduler to automatically check for (and even update to) new version of the asset, according to version mask you define.

## Examples

You can add something like this in your blade template or partial:
```html
<html>
<body>
    <head>
        <!-- include css assets with helper-->
        {{cdnjs(['bootstrap-css','select2-css'])}}

        <!-- OR Blade directive (DEPRECIATED) -->
        @cdnjs(bootstrap-css|select2-css)
    </head>
    
......

    <!-- include js assets -->
    {{cdnjs(['jquery','bootstrap-js','select2-js'])}}
    
    <!-- OR Blade directive (DEPRECIATED) -->
    @cdnjs(jquery|bootstrap-js|select2-js)
</body>
</html>
```
And you will get an output like this:
```html
<html>
<body>
    <head>
        <!-- include css assets -->
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" />

    </head>
    
<!-- ... -->

    <!-- include js assets -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

</body>
</html>
```

## Installation

Require the package by running in your console:
```
composer require zanozik/cdnjs:dev-master
```

Add `Zanozik\Cdnjs\CdnjsServiceProvider::class` to the end of the `providers` array:
```
// config/app.php
'providers' => [
    ...
    Zanozik\Cdnjs\CdnjsServiceProvider::class,
],
```
Publishing assets and running migration is easy:
```
php artisan package:install
```
This will do all of the following:

* Create `assets` table in your database,
* Seed it with sample data (used to render cdnjs Asset manager, as example)
* Create `config/cdnjs.php` configuration file
* Create `resources/lang/en/cdnjs.php` English language file
* Create `resources/view/vendor/cdnjs/index.php` and `resources/view/vendor/cdnjs/edit.php`
  CDNjs Asset Manager blade template files for your convenience.

## Configuration

`config/cdnjs.php` file consists of routes, url and time options.
You can change them according to your needs, although default setting should also work just fine.

Change to desired time for daily version check and autoupdate (assign `false` if you want to completely disable this feature):
```
	/*
	|--------------------------------------------------------------------------
	| Daily version update check time (H:i)
	|--------------------------------------------------------------------------
	|
	*/
	'time' => '0:00'
```
Route prefix and middleware you want to use:
```
	/*
	|--------------------------------------------------------------------------
	| Routes group config
	|--------------------------------------------------------------------------
	|
	*/
	'route' => [
		'prefix' => 'cdnjs',
		'middleware' => 'web'
	],
```
Change prefix to whatever you want to appear before the path to you cdnjs Asset Manager.
By default the path is `/cdnjs/assets`, so you can change it to something like, `/admin/assets`
by assigning  `'prefix' => 'admin',`. You can even disable prefix by assigning an empty string
and call the route by `/assets`.

You can add more middlewares by an array, if you need to, like this:
```
	/*
	|--------------------------------------------------------------------------
	| Routes group config
	|--------------------------------------------------------------------------
	|
	*/
	'route' => [
		'prefix' => 'cdnjs',
		'middleware' => [
		    'web',
		    'auth'
        ],
	],
```
## Usage

### Manager

Click on *Add new asset* and search for desired library by entering partial keyword in *Type* select search box
on opened modal.

![Screenshot](http://i.imgur.com/BVU2B6L.png)

Choose desired version and asset, your custom alias (name) to call from your templates,
default will be generated for you. If you want to use version check, choose *Version check* and *Autoupdate* masks.
Make sure you configured you cron scheduler correctly, if you want to use version checks (refer to[Task Scheduling](https://laravel.com/docs/5.4/scheduling) on Laravel website)

![Screenshot](http://i.imgur.com/E0Q8UbR.png)

*Autoupdate mask* cannot be wider than *Version check mask*.

If a new version is found automatically, according to defined masks, during the version check,
cdnjs Asset Manager will record the version and let you test and update to it.

It will automatically update current version to a new version, if a new version happens to fall under defined *Autoupdate mask*.

The package will also fire predefined `Events`.

## Handling events

The package can fire two events:
* `\Zanozik\Cdnjs\Events\NewAssetVersion`
* `\Zanozik\Cdnjs\Events\AssetVersionUpdated`

The package will pass `Asset` collection with each `Event`.

You can listen for and catch these events any way you want (read further about[Events](https://laravel.com/docs/5.4/events) on Laravel website).

#### Example:

Create app/Listeners/NewVersionListener.php
```
<?php

namespace App\Listeners;

class NewVersionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Zanozik\Cdnjs\Events\NewAssetVersion $event
     * @return void
     */
    public function handle(\Zanozik\Cdnjs\Events\NewAssetVersion $event)
    {
        //$event->asset will return your affected asset
    }
}

```
 You may run `php artisan make:listener NewVersionListener --event="\Zanozik\Cdnjs\Events\NewAssetVersion"`) in your console instead
 (New feature, already in [laravel:master](https://github.com/laravel/framework/pull/19660))

And register that listener in your `$listen` array:
```
//app/Providers/EventServiceProvider.php
    protected $listen = [
        'Zanozik\Cdnjs\Events\NewAssetVersion' => [
            'App\Listeners\NewVersionListener',
        ],
    ];

```

### Helper function

The package provides custom helper function:
* `cdnjs()`

Use an array as a function variable, eg. `cdnjs(['asset1', 'asset2', 'asset3'])`, when you want to output HTML assets in you blade template.

Use a string as a function variable, eg. `cdnjs('asset4')`, when you want to output only URL of defined asset.

### Blade templates (DEPRECIATED)

The package provides two blade directives:
* `@cdnjs`
* `cdnjs-url`

Use `@cdnjs(asset1|asset2|asset3)` when you want to output HTML assets in you blade template.

Use `@cdnjs-url(asset4)` when you want to output only URL of defined asset.


## Important notes

If you fear you will break cdnjs Asset Manager functionality by changing default assets, 
override `cdnjs` functions in published `index.blade.php` (appropriate HTML tags have been preset for you).

Assets collection is being automatically cached and flushed, so if you made some manual changes,
don't forget to clear views and cache:
```
php artisan cache:clear
php artisan view:clear
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/vpre/zanozik/cdnjs.svg
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg

[link-packagist]: https://packagist.org/packages/zanozik/cdnjs
[link-github]: https://github.com/zanozik/cdnjs

[downloads-packagist]: https://img.shields.io/packagist/dt/zanozik/cdnjs.svg
[downloads-github]: https://img.shields.io/github/downloads/zanozik/cdnjs/total.svg