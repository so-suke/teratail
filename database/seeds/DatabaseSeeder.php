<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run() {
    $this->call([
      UsersTableSeeder::class,
      QuestionsTableSeeder::class,
      TagsTableSeeder::class,
      QuestionToTagsTableSeeder::class,
      AnswersTableSeeder::class,
      AnswerRatesTableSeeder::class,
      BestAnswersTableSeeder::class,
      MyTagsTableSeeder::class,
      ClipsTableSeeder::class,
    ]);
  }
}
