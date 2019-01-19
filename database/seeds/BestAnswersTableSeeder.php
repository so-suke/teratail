<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Question;

class BestAnswersTableSeeder extends Seeder {

  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('best_answers')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $best_answers = [
      [
        'question_id' => 1,
        'answer_id' => 1,
      ],
      [
        'question_id' => 6,
        'answer_id' => 5,
      ],
    ];

    foreach ($best_answers as $key => $best_answer) {
			//ベストアンサー付きの質問は解決済みにする。
			$question = Question::find($best_answer['question_id']);
			$question->is_resolved = true;
			$question->save();
      DB::table('best_answers')->insert([
        'question_id' => $best_answer['question_id'],
        'answer_id' => $best_answer['answer_id'],
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }
  }
}
