<?php namespace Zanozik\Cdnjs\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('assets')->insert([
            [
                'id' => 1,
                'type' => 'js',
                'name' => 'jquery',
                'library' => 'jquery',
                'current_version' => '3.2.1',
                'latest_version' => '3.2.1',
                'file' => 'jquery.min.js',
                'version_mask_check' => 1,
                'version_mask_autoupdate' => 2,
                'updated_at' => '2017-04-30 14:02:58',
                'created_at' => '2017-04-28 07:07:15',
            ],
            [
                'id' => 2,
                'type' => 'css',
                'name' => 'bootstrap-css',
                'library' => 'twitter-bootstrap',
                'current_version' => '3.3.7',
                'latest_version' => '4.0.0-alpha.6',
                'file' => 'css/bootstrap.min.css',
                'version_mask_check' => 2,
                'version_mask_autoupdate' => 3,
                'updated_at' => '2017-04-30 11:30:30',
                'created_at' => '2017-04-29 20:25:43',
            ],
            [
                'id' => 3,
                'type' => 'js',
                'name' => 'select2-js',
                'library' => 'select2',
                'current_version' => '4.0.3',
                'latest_version' => '4.0.3',
                'file' => 'js/select2.min.js',
                'version_mask_check' => 1,
                'version_mask_autoupdate' => 2,
                'updated_at' => '2017-04-30 11:30:42',
                'created_at' => '2017-04-30 10:02:30',
            ],
            [
                'id' => 4,
                'type' => 'js',
                'name' => 'bootstrap-js',
                'library' => 'twitter-bootstrap',
                'current_version' => '3.3.7',
                'latest_version' => '4.0.0-alpha.6',
                'file' => 'js/bootstrap.min.js',
                'version_mask_check' => 2,
                'version_mask_autoupdate' => 3,
                'updated_at' => '2017-04-30 12:43:50',
                'created_at' => '2017-04-30 12:43:50',
            ],
            [
                'id' => 5,
                'type' => 'css',
                'name' => 'select2-css',
                'library' => 'select2',
                'current_version' => '4.0.3',
                'latest_version' => '4.0.3',
                'file' => 'css/select2.min.css',
                'version_mask_check' => 1,
                'version_mask_autoupdate' => 2,
                'updated_at' => '2017-04-30 12:46:21',
                'created_at' => '2017-04-30 12:46:21',
            ],
        ]);
    }
}