<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnswerRatesTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('answer_rates')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $answer_rates = [
      [
        'user_id' => 2,
        'answer_id' => 1,
        'kind' => 'high',
      ],
      [
        'user_id' => 3,
        'answer_id' => 1,
        'kind' => 'high',
      ],
      [
        'user_id' => 4,
        'answer_id' => 1,
        'kind' => 'low',
      ],
    ];

    foreach ($answer_rates as $key => $answer_rate) {
      DB::table('answer_rates')->insert([
        'user_id' => $answer_rate['user_id'],
        'answer_id' => $answer_rate['answer_id'],
        'kind' => $answer_rate['kind'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }

  }
}
