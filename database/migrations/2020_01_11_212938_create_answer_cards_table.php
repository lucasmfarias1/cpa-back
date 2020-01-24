<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswerCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answer_cards', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('quiz_id');

            $table->unsignedBigInteger('course_id');
            $table->integer('term');
            $table->integer('age');
            $table->string('sex');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answer_cards');
    }
}
