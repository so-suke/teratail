<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('questions', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('user_id');
      $table->boolean('is_resolved');
      $table->string('title');
      $table->string('md_content');
      $table->timestamps();

      // $table->foreign('best_answer_id')->references('id')->on('answers');

    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('questions');
  }
}
