<?php

use Illuminate\Database\Seeder;

class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('courses')->insert([
            [
                'name' => 'Sistemas para Internet',
                'shorthand' => 'SI'
            ],
            [
                'name' => 'Gestão Portuária',
                'shorthand' => 'GP'
            ],
            [
                'name' => 'Análise e Desenvolvimento de Sistemas',
                'shorthand' => 'ADS'
            ],
            [
                'name' => 'Gestão Empresarial',
                'shorthand' => 'GE'
            ],
            [
                'name' => 'Logística',
                'shorthand' => 'LOG'
            ]
        ]);
    }
}
