<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!app()->isProduction()) {


            $file = __DIR__ . '/nutipita_prod_db.sql';
            if (!file_exists($file)) return; // file may not be present, as not pushed to git
            try {

                DB::statement('SET FOREIGN_KEY_CHECKS=0');

                $tables = DB::select('SHOW TABLES');
                $dbName = config('database.connections.mysql.database');

                foreach ($tables as $table) {
                    $tableName = $table->{"Tables_in_$dbName"};
                    DB::statement("DROP TABLE IF EXISTS `$tableName`");
                }

                $content = file_get_contents($file);
                DB::unprepared($content);

                User::create([
                    'username' => 'test1',
                    'password' => 'test'
                ]);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
