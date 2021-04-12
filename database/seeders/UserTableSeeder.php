<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'surname' => 'MR',
            'first_name' => 'Imtiaz',
            'last_name' => 'ahmed',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin1234'),
        ]);
    }
}
