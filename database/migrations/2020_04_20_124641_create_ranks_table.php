<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('belt_id')->nullable();
            $table->unsignedTinyInteger('stripes')->nullable();
            $table->date('last_ranked_up')->nullable();
            $table->timestamps();
        });

        Schema::table('ranks', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('belt_id')->references('id')->on('belts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ranks', function(Blueprint $table) {
            $table->dropForeign('ranks_user_id_foreign');
            $table->dropForeign('ranks_belt_id_foreign');
        });

        Schema::dropIfExists('ranks');
    }
}
