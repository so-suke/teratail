<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnswersTableSeeder extends Seeder {

  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('answers')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $answers = [
      [
        'question_id' => 1,
        'user_id' => 1,
      ],
      [
        'question_id' => 1,
        'user_id' => 3,
      ],
      [
        'question_id' => 2,
        'user_id' => 1,
      ],
      [
        'question_id' => 2,
        'user_id' => 3,
      ],
      [
        'question_id' => 6,
        'user_id' => 2,
      ],
      [
        'question_id' => 7,
        'user_id' => 2,
      ],
      [
        'question_id' => 7,
        'user_id' => 3,
      ],
    ];

    foreach ($answers as $key => $answer) {
      DB::table('answers')->insert([
        'question_id' => $answer['question_id'],
        'user_id' => $answer['user_id'],
        'md_content' => 'text_' . $key,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }

  }
}