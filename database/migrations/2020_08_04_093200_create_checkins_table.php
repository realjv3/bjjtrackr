<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('event_id')->nullable();
            $table->dateTime('checked_in_at')->comment('UTC Timezone');
            $table->timestamps();
        });

        Schema::table('checkins', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkins', function(Blueprint $table) {
            $table->dropForeign('checkins_user_id_foreign');
            $table->dropForeign('checkins_client_id_foreign');
            $table->dropForeign('checkins_event_id_foreign');
        });

        Schema::dropIfExists('checkins');
    }
}
