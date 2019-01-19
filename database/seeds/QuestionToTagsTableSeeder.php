<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionToTagsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('question_to_tags')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $question_id_tag_ids = [
      1 => [4, 5],
      2 => [1, 4],
      3 => [7, 11],
      4 => [8, 7],
      5 => [9, 12],
      7 => [1, 2],
    ];

    foreach ($question_id_tag_ids as $question_id => $tag_ids) {
      foreach ($tag_ids as $key => $tag_id) {
        DB::table('question_to_tags')->insert([
          'question_id' => $question_id,
          'tag_id' => $tag_id,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
      }
    }
  }
}