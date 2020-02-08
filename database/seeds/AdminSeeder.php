<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        \DB::table('users')->insert([
            "name" => "Admin raiz",
            "cpf" => "99999999999",
            "is_admin" => true,
            "password" => bcrypt("u%3ygDno")
        ]);
    }
}
