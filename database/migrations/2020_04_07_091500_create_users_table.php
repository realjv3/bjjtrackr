<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->unsignedTinyInteger('belt')->nullable();
            $table->unsignedTinyInteger('stripes')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->text('notes')->nullable();
            $table->date('start_date')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('users', function(Blueprint $table) {
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
        Schema::table('users', function(Blueprint $table) {
            $table->dropForeign('users_client_id_foreign');
            $table->dropForeign('users_belt_foreign');
        });
        Schema::dropIfExists('users');
    }
}
