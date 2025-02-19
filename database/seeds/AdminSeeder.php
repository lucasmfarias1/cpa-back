<?php

use Illuminate\Database\Seeder;
use carbon\Carbon;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now()->format("Y-m-d h:i:s");

        \DB::table('users')->insert([
            "name" => "Admin raiz",
            "cpf" => "99999999999",
            "is_admin" => true,
            "course_id" => 1,
            "password" => bcrypt("u%3ygDno"),
            "created_at" => $now,
            "updated_at" => $now
        ]);
    }
}
