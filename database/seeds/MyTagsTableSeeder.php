<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MyTagsTableSeeder extends Seeder {
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('my_tags')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $owner_id_tag_ids = [
      1 => [4, 7],
    ];

		$mytags = [
			[
				'owner_id' => 1,
				'tag_ids' => [4, 7],
			],
			[
				'owner_id' => 2,
				'tag_ids' => [8, 10],
			],
			[
				'owner_id' => 3,
				'tag_ids' => [11, 12],
			],
		];

    foreach ($owner_id_tag_ids as $owner_id => $tag_ids) {
      foreach ($tag_ids as $key => $tag_id) {
        DB::table('my_tags')->insert([
          'owner_id' => $owner_id,
          'tag_id' => $tag_id,
          'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
          'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
      }
    }
  }
}
