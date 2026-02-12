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
            User::create([
                'username' => 'test1',
                'password' => 'test'
            ]);

            $file = __DIR__ . '/nutipita_prod_db.sql';
            if (!file_exists($file)) return; // file may not be present, as not pushed to git
            try {
                $content = file_get_contents($file);
                DB::unprepared($content);
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
