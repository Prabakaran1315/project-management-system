<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Carbon\Carbon;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions =  [

            ['name' => 'user_create','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
            ['name' => 'user_read','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
            ['name' => 'user_update','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
            ['name' => 'user_delete','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],

            ['name' => 'project_create','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
            ['name' => 'project_read','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
            ['name' => 'project_update','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
            ['name' => 'project_delete','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],

            ['name' => 'task_create','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
            ['name' => 'task_read','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
            ['name' => 'task_update','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
            ['name' => 'task_delete','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
            ['name' => 'task_status_update','guard_name'=>'web','created_at'=>Carbon::now()->format('Y-m-d H:i:s'),'updated_at' =>Carbon::now()->format('Y-m-d H:i:s'),],
        ];

        DB::table('permissions')->upsert(
            $permissions,
            ['name'],
            ['name', 'guard_name', 'created_at', 'updated_at']
        );
    }
}
