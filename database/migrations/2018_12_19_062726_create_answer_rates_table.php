<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswerRatesTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('answer_rates', function (Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('user_id');
      $table->unsignedInteger('answer_id');
      $table->enum('kind', ['high', 'low']);
      $table->timestamps();

      $table->unique(['user_id', 'answer_id']);
      $table->foreign('user_id')->references('id')->on('users');
      $table->foreign('answer_id')->references('id')->on('answers');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('answer_rates');
  }
}
