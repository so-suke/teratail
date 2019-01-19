<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBestAnswersTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('best_answers', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('question_id');
      $table->unsignedInteger('answer_id');
      $table->timestamps();

      $table->foreign('question_id')->references('id')->on('questions');
      $table->foreign('answer_id')->references('id')->on('answers');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('best_answers');
  }
}
