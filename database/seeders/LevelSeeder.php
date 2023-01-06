<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Level::factory(3)
            ->has(Quiz::factory(10)
                ->has(Question::factory(50))
            )->create();

    }
}
