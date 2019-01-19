<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMyTagsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('my_tags', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('owner_id');
      $table->unsignedInteger('tag_id');
      $table->timestamps();

			$table->foreign('owner_id')->references('id')->on('users');
			$table->foreign('tag_id')->references('id')->on('tags');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('my_tags');
  }
}
