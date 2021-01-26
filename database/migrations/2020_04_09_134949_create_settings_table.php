<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedTinyInteger('belt_id');
            $table->unsignedTinyInteger('sessions_til_stripe');
            $table->unsignedTinyInteger('weeks_absent_til_contact');
            $table->boolean('combine_same_day_checkins');
            $table->timestamps();
        });

        Schema::table('settings', function(Blueprint $table) {

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('belt_id')->references('id')->on('belts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function(Blueprint $table) {

            $table->dropForeign('settings_client_id_foreign');
            $table->dropForeign('settings_belt_id_foreign');
        });

        Schema::dropIfExists('settings');
    }
}
