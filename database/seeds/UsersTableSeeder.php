<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder {
  public function run() {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('users')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    for ($i = 0; $i < 5; $i++) {
      DB::table('users')->insert([
        'name' => 'a' . $i,
        'score' => 0,
        'email' => 'a' . $i . '@gmail.com',
        'password' => bcrypt('p' . $i),
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
      ]);
    }
  }
}
