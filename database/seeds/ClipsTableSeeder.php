<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClipsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('clips')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $user_id_q_ids = [
      1 => [2, 4],
    ];

    foreach ($user_id_q_ids as $user_id => $q_ids) {
      foreach ($q_ids as $key => $q_id) {
        DB::table('clips')->insert([
          'user_id' => $user_id,
          'question_id' => $q_id,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
      }
    }
  }
}
