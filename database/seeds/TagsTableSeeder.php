<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('tags')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $tags = ['bash', 'basic', 'boo', 'c', 'c++', 'caml', 'php', 'python', 'ruby', 'java', 'javascript', 'vuejs'];

    foreach ($tags as $key => $tag) {
      DB::table('tags')->insert([
        'name' => $tag,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }
  }
}