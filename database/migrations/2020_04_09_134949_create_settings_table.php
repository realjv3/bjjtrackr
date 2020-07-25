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
            $table->unsignedTinyInteger('belt');
            $table->unsignedTinyInteger('classes_til_stripe');
            $table->unsignedTinyInteger('times_absent_til_contact');
            $table->timestamps();
        });

        Schema::table('settings', function(Blueprint $table) {

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('belt')->references('id')->on('belts');
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
            $table->dropForeign('settings_belt_foreign');
        });

        Schema::dropIfExists('settings');
    }
}
