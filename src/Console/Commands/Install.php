<?php namespace Zanozik\Cdnjs\Console\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'package:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish package`s assets, run database migrations and seed it with one command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        //publishing config, blade templates, language file
        $this->call('vendor:publish', [
            '--provider' => 'Zanozik\Cdnjs\CdnjsServiceProvider'
        ]);
        //migrating assets table to db
        $this->call('migrate', [
            '--path' => '/packages/zanozik/cdnjs/src/database/migrations'
        ]);
        //seeding asset table with sample assets for manager frontend
        $this->call('db:seed', [
            '--class' => 'Zanozik\Cdnjs\Database\Seeds\AssetsTableSeeder'
        ]);
        //clearing cache in case we already have that key in our cache
        $this->call('cache:clear');
    }
}
