<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsTableSeeder extends Seeder {
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('questions')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $user_id_q_titles = [
      2 => [
        'C++でオブジェクト指向でゲームを作るには',
        'bashとCに関する質問',
        'phpとjavascriptに関する質問',
        'pythonとphpに関する質問',
        'rubyとvuejsに関する質問',
      ],
      1 => [
        'u_id1がする質問_0',
        'u_id1がする質問_1',
      ],
    ];

		// $best_answer_id = 2;
    foreach ($user_id_q_titles as $user_id => $q_titles) {
      foreach ($q_titles as $key => $q_title) {
        DB::table('questions')->insert([
          'user_id' => $user_id,
          'is_resolved' => false,
          'title' => $q_title,
          'md_content' => '質問内容_' . $key,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
      }
    }

  }
}
